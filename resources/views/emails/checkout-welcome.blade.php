@php
$emailLogoUrl = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
$primary = '#b8325d';
$primaryDark = '#9A2E4A';
$bgLight = '#f1d8e2';
$muted = '#6B7280';
@endphp
@component('mail::message')
@include('emails.partials.logo-header')

<div style="background: linear-gradient(135deg, {{ $primaryDark }} 0%, {{ $primary }} 100%); padding: 32px; border-radius: 16px; margin-bottom: 24px; color: #ffffff;">
    <h1 style="margin: 0; font-size: 22px; font-weight: 700;">Your account is ready</h1>
    <p style="margin: 12px 0 0; opacity: 0.95;">We created an account for you so you can log in and track your order.</p>
</div>

<div style="background: {{ $bgLight }}; padding: 20px; border-radius: 12px; margin-bottom: 24px; border-left: 4px solid {{ $primary }};">
    <p style="margin: 0 0 8px; font-weight: 600; color: {{ $primary }}; font-size: 12px;">Login details</p>
    <p style="margin: 4px 0; color: {{ $muted }};"><strong>Email:</strong> {{ $user->email }}</p>
    <p style="margin: 4px 0; color: {{ $muted }};"><strong>Password:</strong> <code style="background: #fff; padding: 4px 8px; border-radius: 4px;">{{ $plainPassword }}</code></p>
    <p style="margin: 12px 0 0; font-size: 14px; color: {{ $muted }};">Please change your password after your first login.</p>
</div>

<p style="margin: 0; color: {{ $muted }}; font-size: 14px;">Thanks,<br><strong style="color: {{ $primary }};">{{ config('app.name') }} Team</strong></p>
@include('emails.partials.logo-footer')
@endcomponent
