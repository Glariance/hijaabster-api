@component('mail::message')
@php
    $primary = '#BE446C';
    $primaryLight = '#E8A4B8';
    $primaryDark = '#9A2E4A';
    $muted = '#6B7280';
    $bgLight = '#FDF2F8';
@endphp

<div style="text-align:center; margin-bottom: 24px; padding: 20px 0;">
    <img src="{{ (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png' }}" alt="{{ config('app.name') }} logo" style="max-width: 180px; height: auto;">
</div>

<div style="background: linear-gradient(135deg, {{ $primaryDark }} 0%, {{ $primary }} 100%); padding: 32px; border-radius: 16px; margin-bottom: 32px; color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <h1 style="margin: 0; font-size: 28px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">New Website Inquiry</h1>
    <p style="margin: 12px 0 0; color: #ffffff; font-size: 16px; opacity: 0.95;">A visitor submitted the contact form. Details are below.</p>
</div>

<div style="background: {{ $bgLight }}; padding: 24px; border-radius: 12px; margin-bottom: 24px; border-left: 4px solid {{ $primary }};">
    <p style="margin: 0 0 8px; font-weight: 600; color: {{ $primary }}; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Quick Summary</p>
    <p style="margin: 4px 0; color: {{ $muted }}; font-size: 14px;"><strong>From:</strong> {{ $inquiry->name }} ({{ $inquiry->email }})</p>
    @if($inquiry->service)
    <p style="margin: 4px 0; color: {{ $muted }}; font-size: 14px;"><strong>Topic:</strong> {{ $inquiry->service }}</p>
    @endif
    @if($inquiry->phone)
    <p style="margin: 4px 0; color: {{ $muted }}; font-size: 14px;"><strong>Phone:</strong> {{ $inquiry->phone }}</p>
    @endif
</div>

<table width="100%" style="border-collapse: collapse; margin: 0 0 24px; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
    <tbody>
        <tr>
            <td style="padding: 16px; background: {{ $bgLight }}; font-weight: 600; color: {{ $primary }}; width: 30%; border-bottom: 1px solid #e5e7eb;">Name</td>
            <td style="padding: 16px; border-bottom: 1px solid #e5e7eb; color: #374151;">{{ $inquiry->name }}</td>
        </tr>
        <tr>
            <td style="padding: 16px; background: {{ $bgLight }}; font-weight: 600; color: {{ $primary }}; border-bottom: 1px solid #e5e7eb;">Email</td>
            <td style="padding: 16px; border-bottom: 1px solid #e5e7eb; color: #374151;"><a href="mailto:{{ $inquiry->email }}" style="color: {{ $primary }}; text-decoration: none;">{{ $inquiry->email }}</a></td>
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
            <td style="padding: 16px; border-bottom: 1px solid #e5e7eb; color: #374151;"><a href="tel:{{ $inquiry->phone }}" style="color: {{ $primary }}; text-decoration: none;">{{ $inquiry->phone }}</a></td>
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

<div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb; text-align: center;">
    <p style="margin: 0; color: {{ $muted }}; font-size: 14px;">Thanks,<br><strong style="color: {{ $primary }};">{{ config('app.name') }} Team</strong></p>
</div>
@endcomponent
