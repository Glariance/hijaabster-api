<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = ['key', 'type', 'value'];

    /**
     * Get a setting value by key.
     */
    public static function getSetting($key, $default = null)
    {
        return self::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Set or update a setting value.
     */
    public static function setSetting($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
