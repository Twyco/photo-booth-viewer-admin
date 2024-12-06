<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumAccessCode extends Model
{
    protected $fillable = ['album_uuid', 'access_code', 'uses'];
    protected $guarded = ['id'];

    public function album(): belongsTo
    {
        return $this->belongsTo(Album::class, 'album_uuid', 'uuid');
    }
}
