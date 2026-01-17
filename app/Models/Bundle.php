<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bundle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'discount_type',
        'discount_value',
        'total_price',
        'bundle_price',
        'status',
        'created_by',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'total_price' => 'decimal:2',
        'bundle_price' => 'decimal:2',
        'status' => 'boolean',
        'created_by' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bundle) {
            if (empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->name);
            }
        });

        static::updating(function ($bundle) {
            if ($bundle->isDirty('name') && empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->name);
            }
        });
    }

    /**
     * Get the products in this bundle.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'bundle_products')
            ->withPivot('quantity', 'price', 'sort_order')
            ->withTimestamps()
            ->orderBy('bundle_products.sort_order');
    }

    /**
     * Get the creator of the bundle.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Calculate total price of all products in bundle.
     */
    public function calculateTotalPrice()
    {
        $total = 0;
        foreach ($this->products as $product) {
            $price = $product->pivot->price ?? $product->base_price;
            $total += $price * $product->pivot->quantity;
        }
        return $total;
    }

    /**
     * Calculate bundle price after discount.
     */
    public function calculateBundlePrice()
    {
        $totalPrice = $this->total_price ?? $this->calculateTotalPrice();
        
        if ($this->discount_type === 'percentage') {
            $discount = ($totalPrice * $this->discount_value) / 100;
            return $totalPrice - $discount;
        } else {
            return max(0, $totalPrice - $this->discount_value);
        }
    }

    /**
     * Check if bundle is active.
     */
    public function isActive()
    {
        return $this->status && $this->products()->count() > 0;
    }
}
