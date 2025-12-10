@component('mail::message')
@php
    $primary = '#7c3aed';
    $deep = '#2b0050';
    $muted = '#4b5563';
    $logo = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
@endphp

<div style="text-align:center; margin-bottom: 16px;">
    <img src="{{ $logo }}" alt="{{ config('app.name') }} logo" style="max-width: 180px;">
</div>

<div style="background: linear-gradient(135deg, {{ $deep }} 0%, {{ $primary }} 100%); padding: 24px; border-radius: 12px; margin-bottom: 24px; color: #f3e8ff;">
    <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #e9d5ff;">Welcome to Hijabster</h1>
    <p style="margin: 8px 0 0; color: #e9d5ff;">Thanks for signing up. Your account is ready.</p>
</div>

@component('mail::panel')
Here are your account details:
@endcomponent

<table width="100%" style="border-collapse: collapse; margin: 12px 0;">
    <tbody>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; color: {{ $muted }};">Name</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $user->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; color: {{ $muted }};">Email</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $user->email }}</td>
        </tr>
    </tbody>
</table>

Need help? Reply to this email and our team will assist.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
