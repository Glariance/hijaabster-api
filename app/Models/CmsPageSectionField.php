<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPageSectionField extends Model
{
    protected $fillable = ['cms_page_section_id', 'field_group', 'field_name', 'field_type', 'field_value'];

    public function section()
    {
        return $this->belongsTo(CmsPageSection::class);
    }
}
