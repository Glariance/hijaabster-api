<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_purchase',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_until',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'status' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function isActive()
    {
        if (!$this->status) {
            return false;
        }

        $now = now();
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }
        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }
}
