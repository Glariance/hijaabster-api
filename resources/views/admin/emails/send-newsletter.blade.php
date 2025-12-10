@component('mail::message')
@php
    $primary = '#7c3aed';
    $deep = '#2b0050';
    $logo = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
@endphp

<div style="text-align:center; margin-bottom: 16px;">
    <img src="{{ $logo }}" alt="{{ config('app.name') }} logo" style="max-width: 180px;">
</div>

<div style="background: linear-gradient(135deg, {{ $deep }} 0%, {{ $primary }} 100%); padding: 24px; border-radius: 12px; margin-bottom: 24px; color: #f3e8ff;">
    <h1 style="margin: 0; font-size: 22px; font-weight: 800;">{{ $subject ?? 'Message from Hijabster' }}</h1>
</div>

<div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; color: #1f2937; line-height: 1.6;">
    {!! $body['message'] !!}
</div>

<div style="margin-top: 16px; color: #4b5563; font-size: 12px;">
    If you have any questions, reply to this email and our team will assist.
</div>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
