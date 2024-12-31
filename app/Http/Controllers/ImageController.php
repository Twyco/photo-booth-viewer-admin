<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

class ImageController
{
    public static function compressImage(string $filePath, string $albumPath): void
    {

        $filename = 'compressed_' . basename($filePath);

        $path = storage_path('app/albums' . $albumPath . '/_compressed/');
        if (!file_exists($path)) {
            Log::warning('Folder created');
            mkdir($path, 0777, true);
        }
        $compressedFilePath = $path . $filename;
        Log::info($compressedFilePath);
        // Bild komprimieren
        $image = ImageManager::imagick()->read($filePath);

        // Bildgröße anpassen und speichern
        $image->scaleDown(854)->save($compressedFilePath,80);
    }
}
