<?php

use App\Http\Controllers\API\AlbumAccessCodeController;
use App\Http\Controllers\API\AlbumController;
use App\Http\Controllers\API\SettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('albums')->group(function () {

    Route::get('/', [AlbumController::class, 'index']);

    Route::get('{uuid}', [AlbumController::class, 'show']);

    Route::get('{uuid}/cover', [AlbumController::class, 'getAlbumCover']);

    Route::get('{uuid}/images', [AlbumController::class, 'getAlbumImages']);

    Route::get('{uuid}/images/count', [AlbumController::class, 'getImageCount']);

    Route::get('{uuid}/image/{number}', [AlbumController::class, 'getImageByNumber']);

    Route::get('{uuid}/image/{number}/download', [AlbumController::class, 'downloadImageByNumber']);

    Route::get('{uuid}/images/{imageName}', [AlbumController::class, 'getImageByName']);

    Route::get('{uuid}/images/{imageName}/download', [AlbumController::class, 'downloadImageByName']);

    Route::post('/store', [AlbumController::class, 'store']);

    Route::put('{uuid}', [AlbumController::class, 'update']);

    Route::delete('{uuid}', [AlbumController::class, 'destroy']);

});

Route::prefix('access')->group(function () {

    Route::get('{code}', [AlbumAccessCodeController::class, 'getAlbumByAccessCode']);
});

Route::prefix('settings')->group(function () {

    Route::get('/active-album-uuid', [SettingsController::class, 'getActiveAlbum']);

    Route::post('/active-album-uuid', [SettingsController::class, 'setActiveAlbum']);
});

Route::prefix('photobooth')->group(function () {

    Route::get('/auth/{authKey}/album/{uuid}/folder-path', [AlbumController::class, 'getAlbumFolderPath']);

    Route::get('/auth/{authKey}/album/{uuid}/name', [AlbumController::class, 'getAlbumName']);

    Route::get('/auth/{authKey}/album/{uuid}/access-code', [AlbumAccessCodeController::class, 'getAccessCode']);

});


