<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'amazon_link',
        'base_price',
        'stock',
        'has_variations',
        'category_id',
        'brand_id',
        'coupon_id',
        'has_discount',
        'discount_type',
        'discount_value',
        'created_by',
        'featured',
        'new',
        'top',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediaable');
    }
    public function mediaFeatured()
    {
        return $this->morphOne(Media::class, 'mediaable')->where('is_featured', 1);
    }
}
