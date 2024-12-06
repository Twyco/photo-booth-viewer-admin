<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];
    protected $guarded = ['id'];

    public static function getActiveAlbumUuid(): ?string
    {
        $activeAlbumSetting = self::firstOrCreate(['key' => 'active_album'], ['value' => null]);

        if (empty($activeAlbumSetting->value)) {
            return null;
        }

        return $activeAlbumSetting->value;
    }

    public static function setActiveAlbum(string $albumUuid): bool
    {
        $albumExists = Album::where('uuid', $albumUuid)->exists();

        if (!$albumExists) {
            return false;
        }

        self::updateOrCreate(
            ['key' => 'active_album'],
            ['value' => $albumUuid]
        );

        return true;
    }

}
