<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'meta_keyword',
        'meta_title',
        'meta_description',
        'description',
        'status',
    ];

    // Relations
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_tag');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediaable');
    }

    public function featuredImage()
    {
        return $this->morphOne(Media::class, 'mediaable')->where('is_featured', true);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id')->with('replies');
    }
}
