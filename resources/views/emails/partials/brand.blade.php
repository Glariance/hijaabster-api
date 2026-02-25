@php
// Site brand colors (match scarf frontend: primary #b8325d, muted #f1d8e2)
if (!isset($emailLogoUrl)) {
    $emailLogoUrl = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
}
if (!isset($primary)) { $primary = '#b8325d'; }
if (!isset($primaryDark)) { $primaryDark = '#9A2E4A'; }
if (!isset($bgLight)) { $bgLight = '#f1d8e2'; }
if (!isset($muted)) { $muted = '#6B7280'; }
@endphp
