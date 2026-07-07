<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

trait HasImageImporter
{


    public function importImage($image, $old_name = null, $upload_path = null)
    {
        $imageValue = trim($image ?? '');
        $storedPath = null;

        if ($imageValue && filter_var($imageValue, FILTER_VALIDATE_URL)) {
            try {
                $response = Http::timeout(10)->get($imageValue);
                if (!$response->ok()) {
                    logger("Failed to download image, HTTP code: {$response->status()} for URL: $imageValue");
                    return null;
                }

                $imageContent = $response->body();

                $ext = pathinfo(parse_url($imageValue, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $filename = Str::random(10) . '.' . $ext;

                Storage::disk('public')->put("{$upload_path}/{$filename}", $imageContent);
                $storedPath = $filename;

            } catch (\Exception $e) {
                logger("Failed to download image from URL: $imageValue | ".$e->getMessage());
                return null; // or throw $e if you want to stop import
            }
        }

        return $storedPath;
    }

}
