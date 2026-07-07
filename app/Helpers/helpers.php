<?php

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Variation;
use App\Models\SystemSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use GeoSot\EnvEditor\Facades\EnvEditor;
use Illuminate\Support\Facades\Storage;

/**
 * defaultTax
 *
 * @return void
 */
function getDefaultTax()
{
    return config('default_tax') ?? 0;
}

/**
 * calculateDue
 *
 * @param  mixed $total
 * @param  mixed $total_paid
 * @return void
 */
function calculateDue($total, $total_paid)
{
    if ($total > $total_paid) {
        return $total - $total_paid;
    } else {
        return 0;
    }
}

/**
 * currencySymbol
 *
 * @return void
 */
function currencySymbol()
{
    return (config('currency_symbol') ?? '৳') . ' ';
}

/**
 * checkPermission
 *
 * @param  mixed $permissions
 * @return void
 */
function checkPermission($permissions)
{
    if (!auth()->user()->can($permissions)) {
        abort(403);
    }
}




/**
 * generateBarcode
 *
 * @return void
 */
function generateBarcode()
{
    return time();
}

/**
 * invoiceStatusBadge
 *
 * @param  mixed $status
 * @return void
 */
function invoiceStatusBadge($status)
{
    switch ($status) {
        case Invoice::STATUS_PAID:
            return '<span class="badge badge-success">' . Invoice::INVOICE_ALL_STATUS[$status] . '</span>';
            break;
        case Invoice::STATUS_PARTIALLY_PAID:
            return '<span class="badge badge-info">' . Invoice::INVOICE_ALL_STATUS[$status] . '</span>';
            break;
        case Invoice::STATUS_OVERDUE:
            return '<span class="badge badge-warning">' . Invoice::INVOICE_ALL_STATUS[$status] . '</span>';
            break;
        case Invoice::STATUS_CANCELED:
            return '<span class="badge badge-danger">' . Invoice::INVOICE_ALL_STATUS[$status] . '</span>';
            break;

        default:
            return '<span class="badge badge-warning">' . Invoice::INVOICE_ALL_STATUS[$status] . '</span>';
            break;
    }
}
function invoiceDeliveryStatusBadge($status)
{
    switch ($status) {
        case Invoice::DELIVERY_STATUS_DELIVERED:
            return '<span class="badge badge-success">' . ucfirst(Invoice::DELIVERY_STATUS_DELIVERED) . '</span>';
            break;
        case Invoice::DELIVERY_STATUS_PROCESSING:
            return '<span class="badge badge-info">' . ucfirst(Invoice::DELIVERY_STATUS_PROCESSING) . '</span>';
            break;
        case Invoice::DELIVERY_STATUS_PENDING:
            return '<span class="badge badge-warning">' . ucfirst(Invoice::DELIVERY_STATUS_PENDING) . '</span>';
            break;
        case Invoice::DELIVERY_STATUS_CANCELED:
            return '<span class="badge badge-danger">' . ucfirst(Invoice::DELIVERY_STATUS_CANCELED) . '</span>';
            break;

        default:
            return '';
    }
}

function returnRequestStatusBadge($status)
{
    switch ($status) {
        case \App\Models\SaleReturnRequest::STATUS_ACCEPTED:
            return '<span class="badge badge-success">' . ucfirst($status) . '</span>';
            break;
        case \App\Models\SaleReturnRequest::STATUS_PENDING:
            return '<span class="badge badge-warning">' . ucfirst($status) . '</span>';
            break;
        case \App\Models\SaleReturnRequest::STATUS_REJECTED:
            return '<span class="badge badge-danger">' . ucfirst($status) . '</span>';
            break;
        default:
            return '<span class="badge badge-warning">' . ucfirst($status) . '</span>';
            break;
    }
}

// Make minimum 8 digits
/**
 * make8digits
 *
 * @param  mixed $num
 * @return void
 */
function make8digits($num)
{
    return sprintf("%08d", $num);
}

/**
 * Convert English digits in a string to Bangla numerals.
 * Non-digit characters (dashes, spaces, etc.) are preserved.
 *
 * @param  mixed $value
 * @return string
 */
if (!function_exists('to_bangla_number')) {
    function to_bangla_number($value)
    {
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($en, $bn, (string) $value);
    }
}

/**
 * Convert Bangla numerals in a string to English digits.
 * Non-digit characters are preserved. Useful for normalising
 * user input (e.g. phone numbers) before validation/storage.
 *
 * @param  mixed $value
 * @return string
 */
if (!function_exists('to_english_number')) {
    function to_english_number($value)
    {
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($bn, $en, (string) $value);
    }
}

// Make minimum 2 digits
/**
 * make2digits
 *
 * @param  mixed $num
 * @return void
 */
function make2digits($num)
{
    return sprintf("%02d", $num);
}

/**
 * make2decimal
 *
 * @param  mixed $number
 * @return void
 */
function make2dec($number)
{
    return number_format((float)$number, 2, '.', '');
}
function make2decimal($number)
{
    return formatNumber($number);
    //return number_format((float)$number, 2, '.', '');
}
function findCategory($name)
{
    return \App\Models\ProductCategory::where('name', $name)->first()->id;
}
if (!function_exists('formatNumber')) {
    function formatNumber($number)
    {
        $locale = config('decimal_separator') ?? 'en-US';
        $decimals = config('no_of_decimals') ?? 2;
        $localeSettings = [
            'en-US' => [',', '.'],
            'en-IN' => [',', '.'],
            'es-ES' => ['.', ','],
            'fr-FR' => [' ', ','],
            'it-CH' => ['’', '.'],
            'bn-BD' => [',', '.'],
            'ar-SA' => ['٬', '٫'],
        ];
        [$thousandsSeparator, $decimalSeparator] = $localeSettings[$locale] ?? [',', '.'];
        return number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
    }
}

/**
 * commaSeparateObjectItem
 *
 * @param  mixed $object
 * @param  mixed $field
 * @return void
 */
function commaSeparateObjectItem($object, $field)
{
    $total = count($object);
    $data_string = '';

    for ($i = 0; $i < $total; $i++) {
        $string = $data_string . $object[$i]->$field;
        if ($i < $total - 1) $string .= ', ';

        $data_string = $string;
    }
    return $data_string;
}

/**
 * convertDbSettingsToConfig
 *
 * @param  mixed $data
 * @return void
 */
function convertDbSettingsToConfig($settings)
{
    $settings_array = [];
    foreach ($settings as $s) {
        if (is_array($s->settings_value)) {
            foreach ($s->settings_value as $key => $value) {
                $settings_array[$key] = $value;
            }
        } else {
            $settings_array[$s->settings_key] = $s->settings_value;
        }
    }

    return resolveSettingsImageUrls($settings_array);
}

/**
 * settingsImageKeys
 *
 * Settings fields whose value is a stored image filename (under the "settings"
 * disk path) and must be resolved to a full URL when read.
 *
 * @return array
 */
function settingsImageKeys()
{
    return [
        'site_logo',
        'favicon',
        'login_background',
        'login_slider_image_1',
        'login_slider_image_2',
        'login_slider_image_3',
        'login_slider_image_m_1',
        'login_slider_image_m_2',
        'login_slider_image_m_3',
    ];
}

/**
 * resolveSettingsImageUrls
 *
 * Replace stored image filenames with a full URL resolved for the current
 * request, so URLs always match the active host/scheme instead of a stale
 * absolute URL baked in at upload time. basename() keeps this backward
 * compatible with legacy rows that stored a full URL.
 *
 * @param  array $settings
 * @return array
 */
function resolveSettingsImageUrls($settings)
{
    foreach (settingsImageKeys() as $imgKey) {
        if (!empty($settings[$imgKey])) {
            $settings[$imgKey] = getStorageImage('settings', basename($settings[$imgKey]));
        }
    }
    return $settings;
}

/**
 * generateSlug
 *
 * @param  mixed $value
 * @return void
 */
function generateSlug($value)
{
    try {
        return preg_replace('/\s+/u', '-', trim($value));
    } catch (\Exception $e) {
        return '';
    }
}

/**
 * rootDir
 *
 * @return void
 */
function rootDir()
{
    $paths = explode(DIRECTORY_SEPARATOR, $_SERVER['DOCUMENT_ROOT']);
    array_pop($paths);
    return implode(DIRECTORY_SEPARATOR, $paths);
}

/**
 * getDefaultImage
 *
 * @return void
 */
function getDefaultImage()
{
    return static_asset('images/default.png');
}

/**
 * getUserDefaultImage
 *
 * @return void
 */
function getUserDefaultImage()
{
    return static_asset('images/user_default.png');
}

/**
 * getStorageImage
 *
 * @param  mixed $path
 * @param  mixed $name
 * @param  mixed $is_user
 * @return void
 */
function getStorageImage($path, $name, $is_user = false)
{
    if ($name && Storage::disk(config('filesystems.default', 'public'))->exists($path . '/' . $name)) {
        if (strpos(php_sapi_name(), 'cli') !== false || defined('LARAVEL_START_FROM_PUBLIC')) :
            if (config('filesystems.default') == 'public_path'):
                return app('url')->asset('files/' . $path . '/' . $name);
            else:
                return app('url')->asset('storage/' . $path . '/' . $name);
            endif;
        else:
            if (config('filesystems.default') == 'public_path'):
                return app('url')->asset('public/files/' . $path . '/' . $name);
            else:
                return app('url')->asset('public/storage/' . $path . '/' . $name);
            endif;
        //            return app('url')->asset('public/storage/' . $path . '/' . $name);
        endif;
    }
    return $is_user ? getUserDefaultImage() : getDefaultImage();
}
function getPublicStorageImage($path, $name, $is_user = false)
{

    if ($name && Storage::exists($path . '/' . $name)) {
        return app('url')->asset('public/storage/' . $path . '/' . $name);
    }
}

/**
 * getStorageFile
 *
 * @param  mixed $path
 * @param  mixed $name
 * @return void
 */
function getStorageFile($path, $name)
{
    if (strpos(php_sapi_name(), 'cli') !== false || defined('LARAVEL_START_FROM_PUBLIC')) :
        return app('url')->asset('storage/' . $path . '/' . $name);
    else:
        return app('url')->asset('public/storage/' . $path . '/' . $name);
    endif;
    //    return Storage::url($path . '/' . $name);
}
if (!function_exists('getConnectedWooCommerceStores')) {
    /**
     * Check if WooCommerce Addon is installed and return connected stores
     *
     * @return \Illuminate\Support\Collection
     */
    function getConnectedWooCommerceStores()
    {
        // Check if WooCommerce addon is installed
        // $isInstalled = \App\Models\Addon::where('name', 'WooCommerce Addon')->exists();

        // if (!$isInstalled) {
        //     return collect(); // return empty collection if addon not installed
        // }

        // Return all connected WooCommerce stores
        return \App\Models\Platform::where([
            'type' => 'wooCommerce',
            'is_connected' => 1,
        ])->get();
    }
}
if (!function_exists('generateUniqueBarcode')) {
    function generateUniqueBarcode()
    {
        do {
            // Always 10 digits, no leading zeros
            $barcode = mt_rand(1000000000, 9999999999);
        } while (
            Product::where('barcode', $barcode)->exists() ||
            Variation::where('barcode', $barcode)->exists()
        );

        return (string) $barcode;
    }
}

if (!function_exists('generateUniqueSku')) {
    function generateUniqueSku()
    {
        do {
            // Always 10 digits, no leading zeros
            $sku = mt_rand(1000000000, 9999999999);
        } while (
            Product::where('sku', $sku)->exists() ||
            Variation::where('sku', $sku)->exists()
        );

        return (string) $sku;
    }
}

function isInstalledWooCommerceAddon(): bool
{
    return \App\Models\Addon::where('name', 'WooCommerce Addon')->exists();
}

/**
 * get_page_meta
 *
 * @param  mixed $metaName
 * @return void
 */
function get_page_meta($metaName = "title")
{
    if (session()->has('page_meta_' . $metaName)) {
        $title = session()->get("page_meta_" . $metaName);
        session()->forget("page_meta_" . $metaName);
        return $title;
    }
    return null;
}

/**
 * set_page_meta
 *
 * @param  mixed $content
 * @param  mixed $metaName
 * @return void
 */
function set_page_meta($content = null, $metaName = "title")
{
    if ($content && $metaName == "title") {
        session()->put('page_meta_' . $metaName, $content . ' |');
    } else {
        session()->put('page_meta_' . $metaName, $content);
    }
}

/**
 * custom_datetime
 *
 * @param  mixed $datetime
 * @param  mixed $format
 * @return void
 */
function custom_datetime($datetime, $format = null)
{
    if ($format) return date($format, strtotime($datetime));

    return date('Y-m-d g:i A', strtotime($datetime));
}

/**
 * custom_date
 *
 * @param  mixed $datetime
 * @param  mixed $format
 * @return void
 */
function custom_date($datetime, $format = null)
{
    if ($format) return date($format, strtotime($datetime));

    return date('Y-m-d', strtotime($datetime));
}

/**
 * getAppLogo
 *
 * @return void
 */
function getAppLogo()
{
    return static_asset('images/logo.png');
}


if (!function_exists('__t')) {

    /**
     * __t
     *
     * @param  mixed $key
     * @param  mixed $options
     * @param  mixed $isCapitalized
     * @return void
     */
    function __t($key = '', $options = [], $isCapitalized = false)
    {

        $vars = count($options) ? array_merge(...array_map(function ($k) use ($options) {
            $value = __("custom.$options[$k]");
            return [
                "{" . $k . "}" =>  $value,
                "{ $k }" =>  $value,
                "{ $k}" => $value,
                "{" . $k . " }" =>  $value,
                ":$k" => $value
            ];
        }, array_keys($options))) : [];

        $string = strtr(__("custom.{$key}"), $vars);
        return $isCapitalized ? ucwords($string) : $string;
    }
}

if (!function_exists('site_logo')) {

    function site_logo()
    {
        $array = explode('/', config('site_logo'));
        if (config('site_logo') && config('site_logo') != null && Storage::exists('settings/' . $array[count($array) - 1])) {
            return config('site_logo');
        } else {
            return static_asset('admin/images/logo.png');
        }
    }
}
if (!function_exists('static_asset')) {

    function static_asset($path = null, $secure = null)
    {
        if (strpos(php_sapi_name(), 'cli') !== false || defined('LARAVEL_START_FROM_PUBLIC')) :
            return app('url')->asset($path, $secure);
        else:
            $all_null = ($path == null && $secure == null) ? '/' : '';
            return app('url')->asset('public/' . $path, $secure) . $all_null;
        endif;
    }
}
if (!function_exists('favicon')) {

    function favicon()
    {
        $array = explode('/', config('favicon'));

        if (config('favicon') && config('favicon') != null && Storage::exists('settings/' . $array[count($array) - 1])) {

            return config('favicon');
        } else {

            return static_asset('admin/images/favicon.png');
        }
    }
}
if (!function_exists('envWrite')) {
    function envWrite($key, $value)
    {
        $env_value = (is_numeric($value) || is_bool($value)) ? $value : '"' . $value . '"';

        if (EnvEditor::keyExists($key)) {
            EnvEditor::editKey($key, $env_value);
        } else {
            EnvEditor::addKey($key, $env_value);
        }
    }
}

if (!function_exists('verify_purchase_code')):
    function verify_purchase_code($code)
    {
        $verified = false;
        define('VERSION', config('app.version'));

        $script_url = str_replace("install/process", "", (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

        $fields = array(
            'purchase_code'     => urlencode($code),
            'domain'            => urlencode($_SERVER['SERVER_NAME']),
            'remote_addr'       => urlencode($_SERVER['REMOTE_ADDR']),
            'url'               => urlencode($script_url),
            'app_version'       => urlencode(VERSION),
            'user_agent'        => request()->header('User-Agent'),
            'email'             => Session::get('email'),
            'ip'                => getIp(),
            'source'            => 'codecanyon',
            'product_id'        => 34897071,
        );
        $fields_string = '';
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

        $url = "https://trivonsystems.com/api/v100/check-installation";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $curl_response = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        curl_close($ch);

        $curl_response = json_decode($curl_response);
        if ($curl_info["http_code"] == "200"):

            if ($curl_response->status == true && $curl_response->message == "Purchased Product Verified Successfully"):
                envWrite('APP_VERSION', urlencode(VERSION));
                return true;
            else:
                return $curl_response->message;
            endif;
        else:
            if (isset($curl_response->message)) {
                return $curl_response->message;
            } else {
                return __('There is a problem to connect with server.Make sure you have active internet connection!');
            }
        endif;

        return false;
    }
endif;
if (!function_exists('getIp')) {
    function getIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return server ip when no client ip found
    }
}

if (!function_exists('isInstalled')) {

    function isInstalled(): bool
    {
        if (
            config('app.app_installed') == true && config('app.app_installed') != null &&
            config('app.app_purchase_code') != null &&
            preg_match("/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i", config('app.app_purchase_code'))
        ) {
            if (Storage::exists('installed.txt') && Storage::get('installed.txt') == config('app.app_purchase_code')) {
                return true;
            } else {
                try {
                    $verify = verify_purchase_code(config('app.app_purchase_code'));

                    if ($verify === true) {
                        Storage::put('installed.txt', config('app.app_purchase_code'));
                        storePurchaseCode(config('app.app_purchase_code'));
                        return true;
                    } else {
                        return false;
                    }
                } catch (\Exception $e) {
                    return false;
                }
            }
        }
        return false;
    }
}

if (!function_exists('storePurchaseCode')) {
    function storePurchaseCode($code)
    {
        $purchase_info = SystemSettings::where('settings_key', 'purchase_info')->first();

        if ($purchase_info) {
            $purchase_info->update([
                'settings_value' =>  [
                    'purchase_code'     => session()->get('purchase_code'),
                    'install_at'        => now()->toDateTimeString(),
                    'domain'            => url('/'),
                    'product_version'   => config('app.version'),
                ],
            ]);
        } else {
            SystemSettings::create([
                'settings_key'      => 'purchase_info',
                'settings_value'    => [
                    'purchase_code'     => session()->get('purchase_code'),
                    'install_at'        => now()->toDateTimeString(),
                    'domain'            => url('/'),
                ],
            ]);
        }
    }
}

if (!function_exists('check_db_and_table_exist')) {

    function check_db_and_table_exist()
    {
        try {
            if (DB::connection()->getPdo() && DB::connection()->getDatabaseName() && Schema::hasTable('ic_products')) {

                return true;
            }
        } catch (Exception $exception) {
            if (file_exists(storage_path('installed'))) {
                unlink(storage_path('installed'));
            }
            return false;
        }
    }
}

if (!function_exists('all_timezones')) {
    function all_timezones()
    {
        if (Cache::has('all_timezones')) {
            $timezones = Cache::get('all_timezones');
        } else {
            Cache::put('all_timezones', config('clanvent_config.timezone'), \Carbon\Carbon::now()->addMonth(1));
            $timezones = Cache::get('all_timezones');
        }
        return $timezones;
    }
}

if (!function_exists('convertAmountWithCurrencyConverter')) {
    function convertAmountWithCurrencyConverter($amount): float
    {

        $exhangeRate        = config('currency_exchange_rate') ?? 1;
        $convertedAmount    = $amount * $exhangeRate;

        return $convertedAmount;
    }
}
if (!function_exists('user_type')) {
    function user_type()
    {
        if (auth()->guard('web')->check()) {
            return 'admin';
        } elseif (auth()->guard('customer')->check()) {
            $customer = Customer::where('id', auth()->guard('customer')->user()->id)->first();
            if ($customer->type == 'customer') {
                return Customer::TYPE_CUSTOMER;
            } else {
                return Customer::TYPER_EMPLOYEE;
            }
        }
        return null;
    }
}
if (!function_exists('user_id')) {
    function user_id()
    {
        if (auth()->guard('customer')->check()) {
            if (auth()->guard('customer')->user()->type == 'customer') {
                return auth()->guard('customer')->user()->id;
            } else {
                return auth()->guard('customer')->user()->customer_id;
            }
        }
        return null;
    }
}
if (!function_exists('customer_id')) {
    function customer_id($id)
    {
        $user = Customer::where('id', $id)->first();
        if ($user->type == 'customer') {
            return $user->id;
        } else {
            return $user->customer_id;
        }
        return null;
    }
}
if (!function_exists('api_user_id')) {
    function api_user_id()
    {
        if (auth()->guard('api_customer')->check()) {
            if (auth()->guard('api_customer')->user()->type == 'customer') {
                return auth()->guard('api_customer')->user()->id;
            } else {
                return auth()->guard('api_customer')->user()->customer_id;
            }
        }
        return null;
    }
}
if (!function_exists('get_parent_user')) {
    function get_parent_user()
    {
        if (auth()->guard('customer')->check()) {
            if (auth()->guard('customer')->user()->type == 'customer') {
                return auth()->guard('customer')->user();
            } else {
                $customer = Customer::where('id', auth()->guard('customer')->user()->customer_id)->first();
                return $customer;
            }
        }
        return null;
    }
}

if (!function_exists('render_mpdf')) {
    /**
     * Render a Blade view to a PDF using mPDF with full Bangla (Bengali)
     * complex-script shaping. Unlike DomPDF, mPDF reorders matras, builds
     * conjuncts (juktokkhor) and embeds the font, so Bangla + English both
     * render correctly without broken glyphs or empty boxes.
     *
     * @param  string $view      Blade view name
     * @param  array  $data      View data
     * @param  string $filename  Download file name
     * @param  array  $config    Extra mPDF config overrides (e.g. ['orientation' => 'L'])
     * @param  string $dest      'download' | 'stream' | 'string'
     * @return mixed
     */
    function render_mpdf($view, array $data = [], $filename = 'document.pdf', array $config = [], $dest = 'download')
    {
        // mPDF default font dirs + our app font dir holding the Bangla TTF.
        $defaultConfig     = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();

        // Hind Siliguri covers both Bengali (with OpenType shaping) and Latin,
        // so one font renders the whole mixed Bangla/English invoice correctly.
        $mpdf = new \Mpdf\Mpdf(array_merge([
            'mode'              => 'utf-8',
            'format'            => 'A4',
            'tempDir'           => storage_path('app/mpdf'),
            'fontDir'           => array_merge($defaultConfig['fontDir'], [resource_path('fonts')]),
            'fontdata'          => $defaultFontConfig['fontdata'] + [
                'hindsiliguri' => [
                    'R'          => 'HindSiliguri-Regular.ttf',
                    'useOTL'     => 0xFF,   // enable OpenType Layout (Bangla shaping)
                    'useKashida' => 75,
                ],
            ],
            'default_font'      => 'hindsiliguri',
            // Keep bundled DejaVu as a safety fallback for any uncovered glyph.
            'backupSubsFont'    => ['dejavusanscondensed'],
        ], $config));

        $mpdf->WriteHTML(view($view, $data)->render());

        if ($dest === 'stream') {
            return response($mpdf->Output($filename, \Mpdf\Output\Destination::INLINE), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        if ($dest === 'string') {
            return $mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN);
        }

        return response($mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
