<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = [
        'page_title',
        'page_slug',
        'page_meta_title',
        'page_meta_keyword',
        'page_meta_description'
    ];

    public function sections()
    {
        return $this->hasMany(CmsPageSection::class);
    }
}
