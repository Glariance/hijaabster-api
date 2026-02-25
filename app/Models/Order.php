<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'tracking_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_option_id',
        'shipping_cost',
        'payment_method',
        'subtotal',
        'tax_estimate',
        'total',
        'shipping_address',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'tax_estimate' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
