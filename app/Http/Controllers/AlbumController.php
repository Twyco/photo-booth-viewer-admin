<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Models\Album;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

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
            'cover_path' => $validated['cover_path'] ?? null,
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
}
