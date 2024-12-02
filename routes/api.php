<?php

use App\Http\Controllers\AlbumController;
use Illuminate\Support\Facades\Route;

Route::prefix('albums')->group(function () {

  Route::get('/', [AlbumController::class, 'index']);

  Route::get('{uuid}', [AlbumController::class, 'show']);

  Route::get('{uuid}/images', [AlbumController::class, 'getAlbumImages']);

  Route::get('{uuid}/images/{imageName}', [AlbumController::class, 'getImage']);

  Route::post('/', [AlbumController::class, 'store']);

  Route::put('{uuid}', [AlbumController::class, 'update']);

  Route::delete('{uuid}', [AlbumController::class, 'destroy']);

});
