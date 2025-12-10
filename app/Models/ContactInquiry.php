<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactInquiry extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'email', 'phone', 'company', 'service', 'subject', 'message', 'is_read'];

    protected $dates = ['deleted_at'];
}
