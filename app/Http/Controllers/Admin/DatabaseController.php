<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DbOperationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use ZipArchive;

class DatabaseController extends Controller
{
    /**
     * Whole module is restricted to Super Admin users.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (! $user || ! $user->hasRole('Super Admin')) {
                abort(403, __('custom.db_access_denied'));
            }
            return $next($request);
        });
    }

    /**
     * Backup directory (created on demand).
     */
    protected function backupPath(): string
    {
        $path = storage_path('app/db_backups');
        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
        return $path;
    }

    public function index()
    {
        set_page_meta(__('custom.db_management'));

        $logs = DbOperationLog::with('user')->latest()->limit(25)->get();

        return view('admin.database.index', [
            'totalTables'    => $this->getTableCount(),
            'databaseSize'   => $this->getDatabaseSize(),
            'lastBackupDate' => $this->getLastBackupDate(),
            'logs'           => $logs,
        ]);
    }

    /**
     * Delete a single database activity-log entry.
     */
    public function destroyLog(DbOperationLog $log)
    {
        $log->delete();

        flash('Activity log deleted successfully.')->success();

        return redirect()->route('admin.database.index');
    }

    /**
     * Export the full database as a downloadable .sql (or .zip) file.
     */
    public function export(Request $request)
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        $format   = $request->input('format') === 'zip' ? 'zip' : 'sql';
        $database = config('database.connections.' . config('database.default') . '.database');
        $stamp    = now()->format('Ymd_His');
        $sqlName  = "backup_{$database}_{$stamp}.sql";
        $sqlFile  = $this->backupPath() . DIRECTORY_SEPARATOR . $sqlName;

        try {
            $this->dumpDatabase($sqlFile);

            $downloadPath = $sqlFile;
            $downloadName = $sqlName;

            if ($format === 'zip') {
                if (! class_exists(ZipArchive::class)) {
                    throw new \RuntimeException(__('custom.db_zip_unavailable'));
                }
                $zipName = "backup_{$database}_{$stamp}.zip";
                $zipFile = $this->backupPath() . DIRECTORY_SEPARATOR . $zipName;
                $zip = new ZipArchive();
                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                    throw new \RuntimeException(__('custom.db_zip_failed'));
                }
                $zip->addFile($sqlFile, $sqlName);
                $zip->close();
                @unlink($sqlFile);
                $downloadPath = $zipFile;
                $downloadName = $zipName;
            }

            $this->log(DbOperationLog::ACTION_EXPORT, DbOperationLog::STATUS_SUCCESS, $downloadName);

            // Stream the file to the client and delete it from disk afterwards so
            // full database dumps never linger on the server.
            return response()->download($downloadPath, $downloadName, [
                'Content-Type' => $format === 'zip' ? 'application/zip' : 'application/sql',
            ])->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            $this->log(DbOperationLog::ACTION_EXPORT, DbOperationLog::STATUS_FAILED, null, $e->getMessage());
            flash(__('custom.db_export_failed') . ': ' . $e->getMessage())->error();
            return redirect()->route('admin.database.index');
        }
    }

    /**
     * Import a previously exported .sql / .zip backup, overwriting current data.
     */
    public function import(Request $request)
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        $request->validate([
            'backup_file' => ['required', 'file', 'max:307200'], // 300 MB
        ]);

        $file      = $request->file('backup_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, ['sql', 'zip'], true)) {
            flash(__('custom.db_invalid_file_type'))->error();
            return redirect()->route('admin.database.index');
        }

        $workDir = $this->backupPath() . DIRECTORY_SEPARATOR . 'import_' . Str::random(8);
        File::makeDirectory($workDir, 0755, true);

        try {
            if ($extension === 'zip') {
                if (! class_exists(ZipArchive::class)) {
                    throw new \RuntimeException(__('custom.db_zip_unavailable'));
                }
                // Store the upload under a server-generated name (never the
                // client filename) so a crafted name cannot escape the work dir.
                $zipFull = $workDir . DIRECTORY_SEPARATOR . 'upload.zip';
                $file->move($workDir, 'upload.zip');

                $zip = new ZipArchive();
                if ($zip->open($zipFull) !== true) {
                    throw new \RuntimeException(__('custom.db_zip_open_failed'));
                }

                // Extract the first .sql entry by STREAM, writing to a fixed path.
                // We never use the archive's internal paths on disk, so malicious
                // entries (e.g. ../../foo) cannot cause a zip-slip write.
                $sqlPath = $workDir . DIRECTORY_SEPARATOR . 'import.sql';
                $found   = false;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entry = $zip->getNameIndex($i);
                    if ($entry === false || ! preg_match('/\.sql$/i', $entry)) {
                        continue;
                    }
                    $stream = $zip->getStream($entry);
                    if ($stream === false) {
                        continue;
                    }
                    $dest = fopen($sqlPath, 'w');
                    stream_copy_to_stream($stream, $dest);
                    fclose($dest);
                    fclose($stream);
                    $found = true;
                    break;
                }
                $zip->close();
                @unlink($zipFull);

                if (! $found) {
                    throw new \RuntimeException(__('custom.db_no_sql_in_zip'));
                }
            } else {
                $sqlPath = $workDir . DIRECTORY_SEPARATOR . 'import.sql';
                $file->move($workDir, 'import.sql');
            }

            if (filesize($sqlPath) === 0) {
                throw new \RuntimeException(__('custom.db_empty_backup'));
            }

            // Basic validation: peek at the head of the file (so we don't load
            // a multi-hundred-MB dump into memory) and confirm it looks like SQL.
            $head = file_get_contents($sqlPath, false, null, 0, 65536);
            if ($head === false || ! preg_match('/\b(CREATE TABLE|INSERT INTO|DROP TABLE)\b/i', $head)) {
                throw new \RuntimeException(__('custom.db_invalid_sql'));
            }

            $this->importSqlFile($sqlPath);

            File::deleteDirectory($workDir);

            $this->log(DbOperationLog::ACTION_IMPORT, DbOperationLog::STATUS_SUCCESS, $file->getClientOriginalName());
            flash(__('custom.db_import_success'))->success();
        } catch (\Throwable $e) {
            File::deleteDirectory($workDir);
            $this->log(DbOperationLog::ACTION_IMPORT, DbOperationLog::STATUS_FAILED, optional($file)->getClientOriginalName(), $e->getMessage());
            flash(__('custom.db_import_failed') . ': ' . $e->getMessage())->error();
        }

        return redirect()->route('admin.database.index');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Dump the full database to $targetFile using mysqldump. Falls back to a
     * pure-PHP dump only if the mysqldump binary cannot be located.
     */
    protected function dumpDatabase(string $targetFile): void
    {
        $binary = $this->findBinary('mysqldump', 'db.mysqldump_path');

        if (! $binary) {
            // No mysqldump on this host (common on shared hosting / where exec is
            // disabled). Use the pure-PHP dumper, which needs no external binary.
            $this->phpDumpDatabase($targetFile);
            return;
        }

        try {
            $this->mysqldump($binary, $targetFile);
        } catch (\Throwable $e) {
            // mysqldump exists but failed at runtime (wrong path, version
            // mismatch, exec disabled, permissions, ...). Rather than failing the
            // whole export, fall back to the binary-free PHP dumper so export
            // keeps working on the live host.
            Log::warning('mysqldump failed, falling back to PHP dump: ' . $e->getMessage());
            $this->phpDumpDatabase($targetFile);
        }
    }

    /**
     * Run the actual mysqldump process, writing only STDOUT to $targetFile.
     */
    protected function mysqldump(string $binary, string $targetFile): void
    {
        $cfg  = $this->connectionConfig();
        $cnf  = $this->writeDefaultsFile($cfg);
        $errn = '';

        try {
            $process = new Process(array_merge([
                $binary,
                '--defaults-extra-file=' . $cnf,
                '--single-transaction',
                '--quick',
                '--routines',
                '--triggers',
                '--events',
                '--add-drop-table',
                '--default-character-set=utf8mb4',
                '--set-gtid-purged=OFF',
            ], $this->hostArgs($cfg), [$cfg['database']]));
            $process->setTimeout(null);

            $out = fopen($targetFile, 'w');
            if ($out === false) {
                throw new \RuntimeException(__('custom.db_export_write_failed'));
            }

            // Only STDOUT is the dump; keep STDERR aside so warnings/errors
            // never corrupt the .sql file.
            $process->run(function ($type, $buffer) use ($out, &$errn) {
                if ($type === Process::OUT) {
                    fwrite($out, $buffer);
                } else {
                    $errn .= $buffer;
                }
            });
            fclose($out);

            if (! $process->isSuccessful()) {
                throw new \RuntimeException(trim($errn) ?: 'mysqldump exited with code ' . $process->getExitCode());
            }

            // Remove the DEFINER clauses mysqldump adds to views/triggers/
            // routines/events. They hardcode `root`@`localhost`, which makes
            // the dump fail to import on servers where that user does not
            // exist or the importing account lacks SUPER/SET_USER_ID.
            $this->stripDefiners($targetFile);
        } finally {
            @unlink($cnf);
        }
    }

    /**
     * Strip `DEFINER=`user`@`host`` clauses (and downgrade SQL SECURITY DEFINER
     * to INVOKER) from a dump file so it restores under the importing user.
     * Streams line-by-line via a temp file to stay memory-safe on large dumps.
     */
    protected function stripDefiners(string $file): void
    {
        $in  = fopen($file, 'r');
        if ($in === false) {
            return;
        }
        $tmp = $file . '.tmp';
        $out = fopen($tmp, 'w');
        if ($out === false) {
            fclose($in);
            return;
        }

        while (($line = fgets($in)) !== false) {
            fwrite($out, $this->stripDefinerSql($line));
        }

        fclose($in);
        fclose($out);
        rename($tmp, $file);
    }

    /**
     * Remove DEFINER clauses from a chunk of SQL and downgrade SQL SECURITY
     * DEFINER to INVOKER. Used for both the streamed mysqldump output and the
     * CREATE VIEW statements produced by the PHP dumper, so a dump never carries
     * a hardcoded `user`@`host` that may not exist on the importing server.
     */
    protected function stripDefinerSql(string $sql): string
    {
        if (stripos($sql, 'DEFINER') === false) {
            return $sql;
        }
        $sql = preg_replace('/\sDEFINER\s*=\s*`[^`]*`@`[^`]*`/i', '', $sql);
        $sql = preg_replace('/\sDEFINER\s*=\s*[^ ]+@[^ ]+/i', '', $sql);
        return preg_replace('/\bSQL SECURITY DEFINER\b/i', 'SQL SECURITY INVOKER', $sql);
    }

    /**
     * Restore a dump from $sqlPath using the mysql client. Falls back to the
     * pure-PHP importer only if the mysql binary cannot be located.
     */
    protected function importSqlFile(string $sqlPath): void
    {
        $binary = $this->findBinary('mysql', 'db.mysql_path');

        if (! $binary) {
            // No mysql client available: restore with the binary-free importer.
            $this->phpImport($sqlPath);
            return;
        }

        try {
            $this->mysqlImport($binary, $sqlPath);
        } catch (\Throwable $e) {
            // mysql client failed at runtime; fall back to the PHP importer so a
            // restore still works on the live host.
            Log::warning('mysql import failed, falling back to PHP import: ' . $e->getMessage());
            $this->phpImport($sqlPath);
        }
    }

    /**
     * Restore a dump by piping it into the mysql client.
     */
    protected function mysqlImport(string $binary, string $sqlPath): void
    {
        $cfg  = $this->connectionConfig();
        $cnf  = $this->writeDefaultsFile($cfg);
        $errn = '';

        try {
            $process = new Process(array_merge([
                $binary,
                '--defaults-extra-file=' . $cnf,
                '--default-character-set=utf8mb4',
            ], $this->hostArgs($cfg), [$cfg['database']]));
            $process->setTimeout(null);

            $input = fopen($sqlPath, 'r');
            if ($input === false) {
                throw new \RuntimeException(__('custom.db_empty_backup'));
            }
            $process->setInput($input);

            $process->run(function ($type, $buffer) use (&$errn) {
                if ($type === Process::ERR) {
                    $errn .= $buffer;
                }
            });

            if (! $process->isSuccessful()) {
                throw new \RuntimeException(trim($errn) ?: 'mysql exited with code ' . $process->getExitCode());
            }
        } finally {
            @unlink($cnf);
        }
    }

    /**
     * Locate an executable: explicit config path first, then the system PATH.
     */
    protected function findBinary(string $name, string $configKey): ?string
    {
        $configured = config($configKey);
        if ($configured && is_executable($configured)) {
            return $configured;
        }
        return (new ExecutableFinder())->find($name) ?: null;
    }

    /**
     * Write a temporary [client] defaults file so credentials are never passed
     * on the command line (and special characters in passwords are safe).
     */
    protected function writeDefaultsFile(array $cfg): string
    {
        $cnf = tempnam(sys_get_temp_dir(), 'dbcnf_');
        $contents = "[client]\n"
            . 'user="' . addcslashes($cfg['username'] ?? '', '"\\') . "\"\n"
            . 'password="' . addcslashes($cfg['password'] ?? '', '"\\') . "\"\n";
        file_put_contents($cnf, $contents);
        @chmod($cnf, 0600);
        return $cnf;
    }

    /**
     * Host/port/socket args shared by mysqldump and mysql.
     */
    protected function hostArgs(array $cfg): array
    {
        if (! empty($cfg['unix_socket'])) {
            return ['--socket=' . $cfg['unix_socket']];
        }
        $args = ['--host=' . ($cfg['host'] ?? '127.0.0.1')];
        if (! empty($cfg['port'])) {
            $args[] = '--port=' . $cfg['port'];
        }
        return $args;
    }

    protected function connectionConfig(): array
    {
        $default = config('database.default');
        return config("database.connections.{$default}");
    }

    /**
     * Pure-PHP dump fallback (used only when mysqldump is unavailable).
     */
    protected function phpDumpDatabase(string $targetFile): void
    {
        $handle = fopen($targetFile, 'w');
        if ($handle === false) {
            throw new \RuntimeException(__('custom.db_export_write_failed'));
        }

        fwrite($handle, "-- Database backup generated by " . config('app.name') . "\n");
        fwrite($handle, "-- Date: " . now()->toDateTimeString() . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

        $pdo = DB::getPdo();

        // 1) Base tables: structure + data.
        foreach ($this->getTableNames() as $table) {
            $create = DB::selectOne("SHOW CREATE TABLE `{$table}`");
            $createSql = $create->{'Create Table'} ?? null;
            if (! $createSql) {
                continue;
            }

            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($handle, $createSql . ";\n\n");

            // Raw SQL (not the query builder) so the connection's table prefix
            // is not applied a second time to the already-real table name.
            // DB::cursor streams rows, so even huge tables stay memory-safe.
            $columns = null;
            foreach (DB::cursor("SELECT * FROM `{$table}`") as $row) {
                $row = (array) $row;
                if ($columns === null) {
                    $columns = '`' . implode('`, `', array_keys($row)) . '`';
                }
                $values = array_map(function ($value) use ($pdo) {
                    return is_null($value) ? 'NULL' : $pdo->quote((string) $value);
                }, array_values($row));

                fwrite($handle, "INSERT INTO `{$table}` ({$columns}) VALUES (" . implode(', ', $values) . ");\n");
            }

            fwrite($handle, "\n");
        }

        // 2) Views: structure only, dumped after tables (they depend on them).
        //    The DEFINER is stripped so a view created by a user that does not
        //    exist on the target server (e.g. `emd`@`%`) still restores and can
        //    be queried. We never SELECT from a view, so a broken definer on the
        //    source can never block the export either.
        foreach ($this->getViewNames() as $view) {
            $create = DB::selectOne("SHOW CREATE VIEW `{$view}`");
            $createSql = $create->{'Create View'} ?? null;
            if (! $createSql) {
                continue;
            }
            fwrite($handle, "DROP VIEW IF EXISTS `{$view}`;\n");
            fwrite($handle, $this->stripDefinerSql($createSql) . ";\n\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    /**
     * Pure-PHP import fallback (used when the mysql client is unavailable or
     * fails). Streams the file statement-by-statement so large dumps never get
     * loaded into memory at once, and honours DELIMITER blocks (triggers /
     * routines) emitted by mysqldump.
     */
    protected function phpImport(string $sqlPath): void
    {
        $in = fopen($sqlPath, 'r');
        if ($in === false) {
            throw new \RuntimeException(__('custom.db_empty_backup'));
        }

        $pdo = DB::getPdo();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

        $delimiter = ';';
        $buffer    = '';

        try {
            while (($line = fgets($in)) !== false) {
                $trimmed = ltrim($line);

                // Skip comment / blank lines, but only when not mid-statement.
                if ($buffer === '' && ($trimmed === '' || $trimmed[0] === '#'
                    || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*'))) {
                    continue;
                }

                // Handle "DELIMITER xx" directives (used around triggers/routines).
                if ($buffer === '' && preg_match('/^DELIMITER\s+(\S+)/i', $trimmed, $m)) {
                    $delimiter = $m[1];
                    continue;
                }

                $buffer .= $line;

                $rtrim = rtrim($buffer);
                if (substr($rtrim, -strlen($delimiter)) === $delimiter) {
                    $statement = trim(substr($rtrim, 0, -strlen($delimiter)));
                    if ($statement !== '') {
                        $pdo->exec($statement);
                    }
                    $buffer = '';
                }
            }

            $leftover = trim($buffer);
            if ($leftover !== '') {
                $pdo->exec($leftover);
            }
        } finally {
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
            fclose($in);
        }
    }

    protected function getTableNames(): array
    {
        $rows = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
        $names = [];
        foreach ($rows as $row) {
            $names[] = array_values((array) $row)[0];
        }
        return $names;
    }

    protected function getViewNames(): array
    {
        $rows = DB::select('SHOW FULL TABLES WHERE Table_type = "VIEW"');
        $names = [];
        foreach ($rows as $row) {
            $names[] = array_values((array) $row)[0];
        }
        return $names;
    }

    protected function getTableCount(): int
    {
        return count($this->getTableNames());
    }

    protected function getDatabaseSize(): string
    {
        $database = config('database.connections.' . config('database.default') . '.database');
        $result = DB::selectOne(
            'SELECT SUM(data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = ?',
            [$database]
        );
        return $this->humanFileSize((int) ($result->size ?? 0));
    }

    protected function getLastBackupDate(): ?string
    {
        $files = glob($this->backupPath() . DIRECTORY_SEPARATOR . 'backup_*.{sql,zip}', GLOB_BRACE);
        if (empty($files)) {
            $log = DbOperationLog::where('action', DbOperationLog::ACTION_EXPORT)
                ->where('status', DbOperationLog::STATUS_SUCCESS)
                ->latest()
                ->first();
            return $log ? $log->created_at->format('d M Y, h:i A') : null;
        }

        $latest = collect($files)->sortByDesc(fn ($f) => filemtime($f))->first();
        return date('d M Y, h:i A', filemtime($latest));
    }

    protected function humanFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        return round($bytes / (1024 ** $power), 2) . ' ' . $units[$power];
    }

    protected function log(string $action, string $status, ?string $fileName = null, ?string $message = null): void
    {
        DbOperationLog::create([
            'user_id'   => Auth::id(),
            'action'    => $action,
            'status'    => $status,
            'file_name' => $fileName,
            'message'   => $message,
        ]);
    }
}
