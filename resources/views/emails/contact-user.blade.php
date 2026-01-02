@component('mail::message')
@php
    $primary = '#BE446C';
    $primaryLight = '#E8A4B8';
    $primaryDark = '#9A2E4A';
    $muted = '#6B7280';
    $bgLight = '#FDF2F8';
    $logo = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
@endphp

<div style="text-align:center; margin-bottom: 24px; padding: 20px 0;">
    <img src="{{ $logo }}" alt="{{ config('app.name') }} logo" style="max-width: 180px; height: auto;">
</div>

<div style="background: linear-gradient(135deg, {{ $primaryDark }} 0%, {{ $primary }} 100%); padding: 32px; border-radius: 16px; margin-bottom: 32px; color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <h1 style="margin: 0; font-size: 28px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">We received your inquiry</h1>
    <p style="margin: 12px 0 0; color: #ffffff; font-size: 16px; opacity: 0.95;">Thanks for reaching out! Our team will contact you shortly.</p>
</div>

<div style="background: {{ $bgLight }}; padding: 24px; border-radius: 12px; margin-bottom: 24px; border-left: 4px solid {{ $primary }};">
    <p style="margin: 0; font-weight: 600; color: {{ $primary }}; font-size: 16px;">Here's a summary of what you sent us:</p>
</div>

<table width="100%" style="border-collapse: collapse; margin: 0 0 24px; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
    <tbody>
        <tr>
            <td style="padding: 16px; background: {{ $bgLight }}; font-weight: 600; color: {{ $primary }}; width: 30%; border-bottom: 1px solid #e5e7eb;">Name</td>
            <td style="padding: 16px; border-bottom: 1px solid #e5e7eb; color: #374151;">{{ $inquiry->name }}</td>
        </tr>
        <tr>
            <td style="padding: 16px; background: {{ $bgLight }}; font-weight: 600; color: {{ $primary }}; border-bottom: 1px solid #e5e7eb;">Email</td>
            <td style="padding: 16px; border-bottom: 1px solid #e5e7eb; color: #374151;">{{ $inquiry->email }}</td>
        </tr>
        @if($inquiry->company)
        <tr>
            <td style="padding: 16px; background: {{ $bgLight }}; font-weight: 600; color: {{ $primary }}; border-bottom: 1px solid #e5e7eb;">Company</td>
            <td style="padding: 16px; border-bottom: 1px solid #e5e7eb; color: #374151;">{{ $inquiry->company }}</td>
        </tr>
        @endif
        @if($inquiry->phone)
        <tr>
            <td style="padding: 16px; background: {{ $bgLight }}; font-weight: 600; color: {{ $primary }}; border-bottom: 1px solid #e5e7eb;">Phone</td>
            <td style="padding: 16px; border-bottom: 1px solid #e5e7eb; color: #374151;">{{ $inquiry->phone }}</td>
        </tr>
        @endif
        @if($inquiry->service)
        <tr>
            <td style="padding: 16px; background: {{ $bgLight }}; font-weight: 600; color: {{ $primary }}; border-bottom: 1px solid #e5e7eb;">Topic</td>
            <td style="padding: 16px; border-bottom: 1px solid #e5e7eb; color: #374151;">{{ $inquiry->service }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 16px; background: {{ $bgLight }}; font-weight: 600; color: {{ $primary }}; vertical-align: top;">Message</td>
            <td style="padding: 16px; color: #374151; line-height: 1.6;">{!! nl2br(e($inquiry->message)) !!}</td>
        </tr>
    </tbody>
</table>

<div style="background: {{ $bgLight }}; padding: 20px; border-radius: 8px; margin-bottom: 24px; text-align: center;">
    <p style="margin: 0; color: {{ $muted }}; font-size: 14px; line-height: 1.6;">If any details need correcting, just reply to this email.</p>
</div>

<div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb; text-align: center;">
    <p style="margin: 0; color: {{ $muted }}; font-size: 14px;">Thanks,<br><strong style="color: {{ $primary }};">{{ config('app.name') }} Team</strong></p>
</div>
@endcomponent
