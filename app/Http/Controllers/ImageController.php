<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;

class ImageController
{
    public static function compressImage(string $filePath, string $albumPath): void
    {

        $filename = 'compressed_' . basename($filePath);

        $path = storage_path('app/albums' . $albumPath . '/_compressed/');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $compressedFilePath = $path . $filename;

        // Bild komprimieren
        $image = ImageManager::imagick()->read($filePath);

        // Bildgröße anpassen und speichern
        $image->scaleDown(854)->save($compressedFilePath,80);
    }
}
