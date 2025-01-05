<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ImageController;
use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Models\Album;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AlbumController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $albums = Album::orderBy('event_date', 'desc')->get();
        if ($albums->isEmpty()) {
            return response()->json([
                'message' => 'Es sind noch keine Alben vorhanden'
            ]);
        }
        return response()->json($albums->sortBy('event_date'));
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid): JsonResponse
    {
        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
            return response()->json($album);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlbumRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $albumData = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'path' => $validated['path'],
            'cover_file_name' => $validated['cover_file_name'] ?? null,
        ];

        if (isset($validated['event_date'])) {
            $albumData['event_date'] = $validated['event_date'];
        }

        $album = Album::create($albumData);

        return response()->json([
            'message' => 'Album erfolgreich erstellt',
            'created_album' => $album
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlbumRequest $request, $uuid): JsonResponse
    {
        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
            $validated = $request->validated();


            $album->update($validated);
            return response()->json($album);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid): JsonResponse
    {
        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
            $album->delete();

            return response()->json(['message' => 'Successfully deleted!']);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }
    }

    public function getAlbumCover($uuid): BinaryFileResponse|JsonResponse
    {
        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }

        $imagePath = storage_path('app/cover/' . $album->cover_file_name);
        if (!is_file($imagePath)) {
            return response()->file(storage_path('app/cover/default.png'));
        }

        return response()->file($imagePath);
    }

    public function getAlbumImages($uuid): JsonResponse
    {
        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }

        $albumPath = storage_path('app/albums/' . $album->path);
        if (!is_dir($albumPath)) {
            return response()->json([
                'error' => 'Album path not found',
            ], 404);
        }

        $images = [];
        foreach (glob($albumPath . '/*.jpg') as $image) {
            $images[] = basename($image);
        }

        if (empty($images)) {
            return response()->json([
                'error' => 'This album does not have any images!',
            ], 200);
        }

        return response()->json([
            'album' => $album,
            'images' => $images,
        ], 200);
    }

    public function getImageByName($uuid, $imageName): BinaryFileResponse|JsonResponse
    {
        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }

        $imagePath = storage_path('app/albums/' . $album->path . '/' . $imageName);
        if (!file_exists($imagePath)) {
            return response()->json([
                'error' => 'Image not Found!',
            ], 404);
        }

        $compressedFilePath = storage_path('app/albums' . $album->path . '/_compressed/compressed_' . $imageName);
        if(!file_exists($compressedFilePath)){
            ImageController::compressImage($imagePath, $album->path);
        }

        return response()->file($compressedFilePath);
    }

    public function downloadImageByName($uuid, $imageName): BinaryFileResponse|JsonResponse
    {
        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }

        $imagePath = storage_path('app/albums/' . $album->path . '/' . $imageName);
        if (!file_exists($imagePath)) {
            return response()->json([
                'error' => 'Image not Found!',
            ], 404);
        }

        return response()->download($imagePath, $imageName);
    }

    public function getImageByNumber($uuid, $number): BinaryFileResponse|JsonResponse
    {
        $number = intval($number);
        if ($number <= 0) {
            return response()->json([
                'error' => 'Image not found',
                'uuid' => $uuid,
            ], 404);
        }
        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }

        $albumPath = storage_path('app/albums/' . $album->path);
        if (!is_dir($albumPath)) {
            return response()->json([
                'error' => 'Album path not found',
            ], 404);
        }

        $count = 1;
        foreach (glob($albumPath . '/*.jpg') as $image) {
            if ($count >= $number) {
                return response()->file($image);
            }
            $count++;
        }

        return response()->json([
            'error' => 'Image not found!',
        ], 200);
    }
    public function downloadImageByNumber($uuid, $number): BinaryFileResponse|JsonResponse
    {
        $number = intval($number);
        if ($number <= 0) {
            return response()->json([
                'error' => 'Image not found',
                'uuid' => $uuid,
            ], 404);
        }
        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }

        $albumPath = storage_path('app/albums/' . $album->path);
        if (!is_dir($albumPath)) {
            return response()->json([
                'error' => 'Album path not found',
            ], 404);
        }

        $count = 1;
        foreach (glob($albumPath . '/*.jpg') as $image) {
            if ($count >= $number) {
                return response()->download($image, basename($image));
            }
            $count++;
        }

        return response()->json([
            'error' => 'Image not found!',
        ], 200);
    }

    public function getImageCount($uuid): JsonResponse
    {
        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }

        $albumPath = storage_path('app/albums/' . $album->path);
        if (!is_dir($albumPath)) {
            return response()->json([
                'error' => 'Album path not found',
            ], 404);
        }

        $count = count(glob($albumPath . '/*.jpg'));
        return response()->json([
            'imageCount' => $count
        ], 200);
    }

    /**
     * Following are Function for PhotoBooth Client
     *
     */

    public function getAlbumFolderPath($authKey, $uuid): JsonResponse
    {
        if($authKey !== 'ocywT1'){
            return response()->json([
               'error' => 'Unauthorized',
            ], 403);
        }

        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }

        return response()->json($album->path, 200);
    }

    public function getAlbumName($authKey, $uuid): JsonResponse
    {
        if($authKey !== 'ocywT1'){
            return response()->json([
                'error' => 'Unauthorized',
            ], 403);
        }

        try {
            $album = Album::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Album not found',
                'uuid' => $uuid,
            ], 404);
        }

        return response()->json($album->name, 200);
    }
}
