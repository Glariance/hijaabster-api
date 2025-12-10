<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    // Polymorphic relationship with Media
    public function media()
    {
        return $this->morphOne(Media::class, 'mediaable');
    }
}
