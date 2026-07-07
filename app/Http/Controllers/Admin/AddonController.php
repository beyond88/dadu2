<?php

namespace App\Http\Controllers\Admin;

use App\Models\Addon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Addon\AddonService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\Admin\AddonInstallRequest;

class AddonController extends Controller
{
    protected $addon;

    public function __construct(AddonService $addon){
        $this->addon = $addon;
    }
    public function installAddons(){


        // $addons = $this->addon->paginate(get_pagination('pagination'));
        $addons = Addon::all();
        return view('admin.addons.installed-addons',compact('addons'));
    }

    public function availableAddons(){

//        $url        = "https://desk.spagreen.net/yoori-plugins";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://desk.spagreen.net/yoori-plugins",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer gjMhFjGmLXsafyZ2",
                "Content-Type: application/json",
            ),

        ));

        $response = curl_exec($curl);

        $curl_info      = curl_getinfo($curl);
        curl_close($curl);

//        $response   = Http::get($url);

            $decodedResponse =json_decode($response);
            return view('admin.addons.available-addons',compact('decodedResponse','curl_info'));

    }
    public function statusChange(Request $request){
        if (isDemoServer()):
            $response['message']    = __('This function is disabled in demo server.');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;
        if($this->addon->statusChange($request['data'])):
            $response['message'] = __('Updated Successfully');
            $response['title']   = __('Success');
            $response['status']   = 'success';
            return response()->json($response);
        else:
            $response['message'] = __('Something went wrong, please try again');
            $response['title']   = __('Ops..!');
            $response['status']   = 'error';
            return response()->json($response);
        endif;
    }

    public function installNewAddon(Request $request)
{
    // Step 1: Define paths
    $tempPath = storage_path('temp/addons'); // Temporary path for extracted files
    $uploadedFile = $request->file('addon_zip_file'); // Uploaded ZIP file
    $fileName = $uploadedFile->getClientOriginalName();
    $uploadedFile->move($tempPath, $fileName); // Move ZIP to temp path

    // Step 2: Extract ZIP
    $zip = new \ZipArchive();
    $zipFilePath = $tempPath . '/' . $fileName;
    $extractToPath = $tempPath . '/' . pathinfo($fileName, PATHINFO_FILENAME);

    if ($zip->open($zipFilePath) === true) {
        $zip->extractTo($extractToPath);
        $zip->close();
    } else {
        flash(__('Failed to open ZIP file.'))->error();
        return back();
    }

    // Step 3: Read and validate config.json
    $configPath = $extractToPath . '/config.json';
    if (!file_exists($configPath)) {
        flash(__('Missing config.json file.'))->error();
        return back();
    }

    $config = json_decode(file_get_contents($configPath), true);
    if (!$config || !isset($config['files'])) {
        flash(__('Invalid config.json format.'))->error();
        return back();
    }

    // Step 4: Process files/folders
    foreach ($config['files'] as $file) {
        $sourcePath = $extractToPath . '/' . $file['source'];
        $destinationPath = base_path($file['destination']); // Resolve the destination path

        // Ensure the destination directory exists
        $destinationDir = dirname($destinationPath);
        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true); // Create directory if it doesn't exist
        }

        // Copy file or directory
        if (file_exists($sourcePath)) {
            if (is_dir($sourcePath)) {
                File::copyDirectory($sourcePath, $destinationPath);
            } else {
                File::copy($sourcePath, $destinationPath);
            }
        } else {
            flash(__("Missing file or directory: {$file['source']}"))->error();
            return back();
        }
    }
       try {
            \Artisan::call('migrate', [
                '--force' => true, // Force without confirmation
            ]);
        } catch (\Exception $e) {
            flash(__('Addon installed but migration failed: ') . $e->getMessage())->warning();
            return back();
        }
    // Step 6: Create or update addon record in the database
    $this->addon->createOrUpdate(array_merge($config, ['purchase_code' => $request->purchase_code]));

    // Step 7: Cleanup temp files
    File::deleteDirectory($extractToPath); // Remove extracted folder
    File::delete($zipFilePath); // Remove ZIP file

    flash(__('Installed Successfully'))->success();
    return back();
}






}
