<?php

namespace App\Http\Controllers\Admin\Settings;

use DateTimeZone;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SystemSettings;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App\Services\Utils\FileUploadService;

class SystemSettingsController extends Controller
{
    public const FILE_STORE_PATH = 'settings';
    protected $fileUploadService;

    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct()
    {
        $this->fileUploadService = app(FileUploadService::class);

        $this->middleware(['permission:Site Settings'])->only(['edit']);
    }

    /**
     * edit
     *
     * @return void
     */
    public function edit()
    {
        try {
            $tzlists = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

            $settings = [];
            $raw_settings = SystemSettings::all();
            $roles = Role::query()->pluck('name', 'id');

            foreach ($raw_settings as $s) {
                $settings[$s->settings_key] = $s->settings_value;
            }

            // Resolve stored image filenames to a full URL for the preview.
            if (!empty($settings['general'])) {
                $settings['general'] = resolveSettingsImageUrls($settings['general']);
            }

            set_page_meta('System Settings');
            return view('admin.system_settings.edit', compact('settings', 'roles', 'tzlists'));
        } catch (\Throwable $th) {
            \Log::error('SystemSettings edit error: ' . $th->getMessage() . ' in ' . $th->getFile() . ':' . $th->getLine());
            throw $th;
        }
    }

    /**
     * update
     *
     * @param  mixed $request
     * @return void
     */
    public function update(Request $request)
    {
        try {
        $data = $request->except('_token');


        // Set site logo
        $data = $this->uploadImage($data, 'site_logo');
        // Set favicon
        $data = $this->uploadImage($data, 'favicon');
        // Set login background
        $data = $this->uploadImage($data, 'login_background');
        // Set login slider image
        $data = $this->uploadImage($data, 'login_slider_image_1');
        $data = $this->uploadImage($data, 'login_slider_image_2');
        $data = $this->uploadImage($data, 'login_slider_image_3');
        $data = $this->uploadImage($data, 'login_slider_image_m_1');
        $data = $this->uploadImage($data, 'login_slider_image_m_2');
        $data = $this->uploadImage($data, 'login_slider_image_m_3');


        $keys = array_keys($data);


        foreach ($keys as $key) {
            $settings = SystemSettings::where('settings_key', $key)->first();
            if (!$settings) $settings = new SystemSettings();

            $settings->settings_key = $key;
            $value = isset($data[$key]) ? $data[$key] : null;
            $settings->settings_value = (is_array($value) || is_object($value)) ? $value : (($value !== null && $value !== '') ? $value : []);
            $settings->save();

        }

        if(array_key_exists('timezone', $data['general'])){
            envWrite('APP_TIMEZONE', $data['general']['timezone']);
        }
        if(in_array('general', $keys)){
            // then check timezone value is available or not
            $timezone = $data['general']['timezone'] ? $data['general']['timezone'] : config('app.timezone');

            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }


        flash('System settings updated successfully')->success();
        return redirect()->back();
        } catch (\Throwable $th) {
            \Log::error('SystemSettings update error: ' . $th->getMessage() . ' in ' . $th->getFile() . ':' . $th->getLine());
            throw $th;
        }
    }


    /**
     * uploadImage
     *
     * @param  mixed $data
     * @param  mixed $field
     * @return void
     */
    public function uploadImage($data, $field)
    {
        $general = SystemSettings::where('settings_key', 'general')->first();

        if (isset($data['general'][$field])) {
            $value = $data['general'][$field];

            // Only process an actual new upload (an uploaded file or a base64
            // payload). If the field came back as a plain string (e.g. the
            // existing resolved URL/filename re-submitted by the form), keep the
            // stored filename untouched instead of corrupting it.
            $is_new_upload = ($value instanceof \Illuminate\Http\UploadedFile)
                || (is_string($value) && strpos($value, 'base64') !== false);

            if (!$is_new_upload) {
                if (isset($general->settings_value[$field])) {
                    $data['general'][$field] = $general->settings_value[$field];
                }
                return $data;
            }

            \Log::info("[Settings Upload] field={$field}", [
                'file_type'        => is_object($value) ? get_class($value) : gettype($value),
                'filesystem_disk'  => config('filesystems.default'),
                'disk_root'        => config('filesystems.disks.' . config('filesystems.default') . '.root'),
                'storage_writable' => is_writable(storage_path('app/public')),
                'symlink_exists'   => file_exists(public_path('storage')),
                'is_symlink'       => is_link(public_path('storage')),
            ]);

            if(isset($general['settings_value'][$field]) && $general['settings_value'][$field] != null){
                $array = explode('/', $general['settings_value'][$field]);
                $this->fileUploadService->delete('settings/'.$array[count($array) - 1]);
            }

            if (strpos($data['general'][$field], 'base64') !== false) {
                $name = $this->fileUploadService->uploadBase64($data['general'][$field], self::FILE_STORE_PATH);
            } else {
                $name = $this->fileUploadService->upload($data['general'][$field], self::FILE_STORE_PATH);
            }

            \Log::info("[Settings Upload] result", [
                'field'      => $field,
                'saved_name' => $name,
                'final_url'  => getStorageImage(self::FILE_STORE_PATH, $name),
            ]);

            // Store only the filename. The full URL is resolved per-request via
            // getStorageImage() so it always matches the current host/scheme and
            // never bakes in a stale absolute URL (e.g. a wrong https host).
            $data['general'][$field] = $name;

        } else {
            if (isset($general->settings_value[$field])) {
                $data['general'][$field] = $general->settings_value[$field];
            }
        }

        return $data;
    }
}
