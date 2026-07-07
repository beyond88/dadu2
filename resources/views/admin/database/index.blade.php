@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.db_management') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.db_management') }}</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Export / Import actions --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1">{{ __('custom.db_backup_restore') }}</h5>
                            <p class="text-muted mb-0">{{ __('custom.db_backup_restore_hint') }}</p>
                        </div>
                        <div class="d-flex flex-wrap" style="gap:8px;">
                            <a href="{{ route('admin.database.export', ['format' => 'sql']) }}"
                               class="btn btn-primary">
                                <i class="mdi mdi-database-export"></i> {{ __('custom.export_database') }} (.sql)
                            </a>
                            <a href="{{ route('admin.database.export', ['format' => 'zip']) }}"
                               class="btn btn-outline-primary">
                                <i class="mdi mdi-folder-zip"></i> .zip
                            </a>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#importDbModal">
                                <i class="mdi mdi-database-import"></i> {{ __('custom.import_database') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Database information --}}
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <span class="badge badge-soft-primary" style="font-size:22px;padding:12px;">
                                <i class="mdi mdi-table"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($totalTables) }}</h3>
                            <p class="text-muted mb-0">{{ __('custom.total_tables') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <span class="badge badge-soft-info" style="font-size:22px;padding:12px;">
                                <i class="mdi mdi-database"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $databaseSize }}</h3>
                            <p class="text-muted mb-0">{{ __('custom.database_size') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <span class="badge badge-soft-success" style="font-size:22px;padding:12px;">
                                <i class="mdi mdi-clock-outline"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="mb-0" style="font-size:18px;">{{ $lastBackupDate ?? __('custom.no_backup_yet') }}</h3>
                            <p class="text-muted mb-0">{{ __('custom.last_backup_date') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity log --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('custom.db_activity_log') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-centered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('custom.user') }}</th>
                                    <th>{{ __('custom.action') }}</th>
                                    <th>{{ __('custom.status') }}</th>
                                    <th>{{ __('custom.file') }}</th>
                                    <th>{{ __('custom.date_time') }}</th>
                                    <th>{{ __('custom.message') }}</th>
                                    <th class="text-center">{{ __('custom.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $index => $log)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ optional($log->user)->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $log->action === \App\Models\DbOperationLog::ACTION_EXPORT ? 'badge-info' : 'badge-warning' }}">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $log->status === \App\Models\DbOperationLog::STATUS_SUCCESS ? 'badge-success' : 'badge-danger' }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $log->file_name ?? '-' }}</td>
                                        <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                                        <td><small class="text-muted">{{ $log->message ?? '-' }}</small></td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.database.log.destroy', $log->id) }}" method="POST"
                                                  onsubmit="return confirm('Delete this activity log entry?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="{{ __('custom.delete') }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">{{ __('custom.no_records_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import confirmation modal --}}
    <div class="modal fade" id="importDbModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.database.import') }}" method="POST" enctype="multipart/form-data" id="importDbForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('custom.import_database') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="mdi mdi-alert"></i>
                            <strong>{{ __('custom.warning') }}!</strong> {{ __('custom.db_import_overwrite_warning') }}
                        </div>
                        <div class="form-group">
                            <label for="backup_file">{{ __('custom.select_backup_file') }} <span class="text-danger">*</span></label>
                            <input type="file" class="form-control no-uppercase" id="backup_file" name="backup_file"
                                   accept=".sql,.zip" required>
                            <small class="text-muted">{{ __('custom.db_accepted_formats') }}</small>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="confirmImport">
                            <label class="custom-control-label" for="confirmImport">{{ __('custom.db_import_confirm_checkbox') }}</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('custom.cancel') }}</button>
                        <button type="submit" class="btn btn-danger" id="importSubmitBtn" disabled>
                            <i class="mdi mdi-database-import"></i> {{ __('custom.confirm_import') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
<script>
$(document).ready(function () {
    // Only enable the import button once the user explicitly confirms.
    $('#confirmImport').on('change', function () {
        $('#importSubmitBtn').prop('disabled', !this.checked);
    });

    // Show a busy state while the (synchronous) import runs.
    $('#importDbForm').on('submit', function () {
        $('#importSubmitBtn')
            .prop('disabled', true)
            .html('<i class="mdi mdi-loading mdi-spin"></i> {{ __('custom.importing') }}...');
    });
});
</script>
@endpush
