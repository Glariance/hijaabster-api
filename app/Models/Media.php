<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'path',
        'media_type',
        'mediaable_id',
        'mediaable_type',
        'is_featured',
    ];

    public function mediaable()
    {
        return $this->morphTo();
    }
}
