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
            return response()->json($album, 200);
        }catch (ModelNotFoundException $e){
            return response()->json([
                'error' => 'Unknown access code',
                'access_code' => $code,
            ], 404);
        }
    }
}
