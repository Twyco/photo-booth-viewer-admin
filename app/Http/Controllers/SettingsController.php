<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController
{
    public function getActiveAlbum(): JsonResponse
    {
        $activeAlbum = Setting::getActiveAlbumUuid();

        if ($activeAlbum) {
            return response()->json([
                'album_uuid' => $activeAlbum,
            ], 200);
        }

        return response()->json([
            'error' => 'No active album set.',
        ], 404);
    }

    public function setActiveAlbum(Request $request): JsonResponse
    {

        $validated = $request->validate([
            'album_uuid' => 'required|uuid',
        ]);

        $success = Setting::setActiveAlbum($validated['album_uuid']);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Active album updated successfully.',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Album not found or invalid UUID.',
        ], 404);
    }


}
