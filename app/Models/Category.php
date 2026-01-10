<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'status',
    ];

    // Relationship to parent category (for multi-level)
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relationship to child categories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Polymorphic relationship with Media (excluding soft deleted)
    public function media()
    {
        return $this->morphMany(Media::class, 'mediaable')->whereNull('deleted_at');
    }

    // Get featured media (excluding soft deleted)
    public function mediaFeatured()
    {
        return $this->morphOne(Media::class, 'mediaable')->where('is_featured', 1)->whereNull('deleted_at');
    }
}
