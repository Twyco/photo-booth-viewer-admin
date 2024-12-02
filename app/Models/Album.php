<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Album extends Model
{
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'description', 'path', 'cover_path', 'event_date'];
    protected $guarded = ['uuid'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($album) {
            $album->uuid = (string) Str::uuid();
        });
    }
}
