<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlbumAccessCodeRequest;
use App\Http\Requests\UpdateAlbumAccessCodeRequest;
use App\Models\Album;
use App\Models\AlbumAccessCode;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class AlbumAccessCodeController
{
    public function getAlbumByAccessCode($code): JsonResponse
    {
        try {
            $accessCode = AlbumAccessCode::where('access_code', $code)->firstOrFail();
            $album = $accessCode->album;
            return response()->json([
                'album' => $album,
            ], 200);
        }catch (ModelNotFoundException $e){
            return response()->json([
                'error' => 'Unknown access code',
                'access_code' => $code,
            ], 404);
        }
    }

    public function getAccessCode($authKey, $uuid): JsonResponse
    {
        if($authKey !== 'ocywT1'){
            return response()->json([
                'error' => 'Unauthorized',
            ], 403);
        }

        try {
            $albumAccessCode = AlbumAccessCode::where('album_uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Access Code not found',
                'uuid' => $uuid,
            ], 404);
        }

        return response()->json($albumAccessCode->access_code, 200);
    }
}
