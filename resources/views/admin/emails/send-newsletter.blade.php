@php
$emailLogoUrl = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
$primary = '#b8325d';
$primaryDark = '#9A2E4A';
$bgLight = '#f1d8e2';
$muted = '#6B7280';
@endphp
@component('mail::message')
@include('emails.partials.logo-header')

<div style="background: linear-gradient(135deg, {{ $primaryDark }} 0%, {{ $primary }} 100%); padding: 18px 32px; border-radius: 8px; margin-bottom: 20px; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
    <h1 style="margin: 0; font-size: 20px; font-weight: 600; color: #ffffff; letter-spacing: 0.3px;">{{ $subject ?? 'Message from ' . config('app.name') }}</h1>
</div>

<div style="background: #ffffff; border: 1px solid #E5E7EB; border-radius: 8px; padding: 20px; margin-bottom: 24px; color: #374151; line-height: 1.7; font-size: 15px; min-height: 60px;">
    {!! $body['message'] !!}
</div>

<div style="margin-top: 24px; color: {{ $muted }}; font-size: 14px; line-height: 1.6;">
    <p style="margin: 0 0 8px;">If you have any questions, reply to this email and our team will assist.</p>
    <p style="margin: 0;">
        Thanks,<br>
        <strong style="color: {{ $primary }};">{{ config('app.name') }}</strong>
    </p>
</div>
@include('emails.partials.logo-footer')
@endcomponent
