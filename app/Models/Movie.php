<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'file_path',
        'artists',
        'genre',
        'number_of_views'
    ];

    function getArtistsAttribute(): array
    {
        return explode('|', $this->attributes['artists'] ?? "");
    }
}
