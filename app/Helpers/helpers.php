<?php

use App\Models\CmsPage;
use Illuminate\Support\Facades\Cache;
use App\Models\GeneralSetting;

if (!function_exists('getSetting')) {
    function getSetting($key, $default = null)
    {
        // return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
        $setting = GeneralSetting::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return $setting->type === 'file' ? 'storage/' . $setting->value : $setting->value;
        // });
    }
}
if (!function_exists('getCmsPage')) {
    function getCmsPage()
    {
        $cmsPages = CmsPage::all();
        return $cmsPages;
    }
}

if (!function_exists('defaultBadge')) {
    function defaultBadge($value, $width = 100)
    {
        // $width in percentage [25, 50, 75, 100]
        return '<div class="badge rounded-pill bg-light w-' . $width . '">' . $value . '</div>';
    }
}
if (!function_exists('getCmsContent')) {
    function getCmsContent($value, $default)
    {
        return !empty($value) ? $value : $default;
    }
}
