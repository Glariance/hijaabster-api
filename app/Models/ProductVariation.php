<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'price',
        'stock',
        'option_ids',
    ];

    protected $casts = [
        'option_ids' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return ProductAttributeOption::whereIn('id', $this->option_ids)->get();
    }

    public function getOptionsAttribute()
    {
        $ids = json_decode($this->option_ids, true);
        return \App\Models\ProductAttributeOption::with('attribute')
            ->whereIn('id', $ids)
            ->get()
            ->map(function ($option) {
                return [
                    'name' => $option->attribute->name,
                    'value' => $option->value,
                ];
            });
    }
    public function getOptionIdsAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value, true) ?: [];
    }
    public function getOptionsWithAttributes()
    {
        if (empty($this->option_ids)) {
            return collect();
        }

        return \App\Models\ProductAttributeOption::with('attribute')
            ->whereIn('id', $this->option_ids)
            ->get();
    }
}
