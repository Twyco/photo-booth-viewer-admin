<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Album extends Model
{
    public mixed $uuid;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'description', 'path', 'cover_file_name', 'event_date'];
    protected $guarded = ['uuid'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($album) {
            $album->uuid = (string) Str::uuid();
        });

        static::created(function ($album) {
            $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 25);
            AlbumAccessCode::create([
                'album_uuid' => $album->uuid,
                'access_code' => $randomString,
            ]);
        });
    }
}
