@php
$emailLogoUrl = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
$primary = '#b8325d';
$primaryDark = '#9A2E4A';
$bgLight = '#f1d8e2';
$muted = '#6B7280';
@endphp
@component('mail::message')
@include('emails.partials.logo-header')

<div style="background: linear-gradient(135deg, {{ $primaryDark }} 0%, {{ $primary }} 100%); padding: 24px; border-radius: 12px; margin-bottom: 24px; color: #ffffff;">
    <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #ffffff;">Password changed</h1>
    <p style="margin: 8px 0 0; color: #ffffff;">Hi {{ $user->name ?? 'there' }}, your password was successfully updated.</p>
</div>

<p style="color: {{ $muted }};">If you did not make this change, please contact support immediately.</p>

Thanks,<br>
{{ config('app.name') }}
@include('emails.partials.logo-footer')
@endcomponent
