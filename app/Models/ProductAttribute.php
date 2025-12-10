<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $fillable = ['name', 'status'];

    protected $casts = ['status' => 'boolean'];

    public function options()
    {
        return $this->hasMany(ProductAttributeOption::class, 'attribute_id');
    }
}
