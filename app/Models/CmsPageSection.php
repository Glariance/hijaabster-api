<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPageSection extends Model
{
    protected $fillable = ['cms_page_id', 'section_name', 'section_type', 'section_sort_order'];

    public function page()
    {
        return $this->belongsTo(CmsPage::class);
    }

    public function fields()
    {
        return $this->hasMany(CmsPageSectionField::class);
    }

    public function repeaterGroups()
    {
        return $this->fields()
            ->select('field_group')
            ->whereNotNull('field_group')
            ->groupBy('field_group');
    }
}
