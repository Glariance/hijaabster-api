@component('mail::message')
@php
    $primary = '#7c3aed';
    $deep = '#2b0050';
    $text = '#1f2937';
    $logoUrl = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
@endphp

<div style="text-align:center; margin-bottom: 16px;">
    <img src="{{ $logoUrl }}" alt="{{ config('app.name') }} logo" style="max-width: 180px;">
</div>

<div style="background: linear-gradient(135deg, {{ $deep }} 0%, {{ $primary }} 100%); padding: 32px; border-radius: 12px; margin-bottom: 24px; color: #f3e8ff;">
    <h1 style="margin: 0 0 12px; font-size: 24px; font-weight: 800; color: #e9d5ff;">Reset your password</h1>
    <p style="margin: 0; line-height: 1.6; color: #e9d5ff;">
        Hi {{ $user->name ?? 'there' }}, we received a request to reset the password for your Hijabster account.
    </p>
</div>

@component('mail::panel')
Click the button below to choose a new password. The link will expire in 60 minutes.
@endcomponent

@component('mail::button', ['url' => $resetUrl, 'color' => 'primary'])
Reset Password
@endcomponent

If the button doesn't work, copy and paste this link into your browser:

<div style="margin: 12px 0; padding: 12px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; word-break: break-all; color: {{ $text }};">
    {{ $resetUrl }}
</div>

For reference, your reset token is:

<div style="margin: 8px 0 16px; padding: 10px; background: #f3e8ff; border: 1px solid #e9d5ff; border-radius: 8px; word-break: break-all; color: {{ $deep }}; font-weight: 700;">
    {{ $token }}
</div>

If you didn’t request this, you can safely ignore this email.

Thanks,<br>
<div style="margin-top: 12px;">
    <img src="{{ (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png' }}" alt="{{ config('app.name') }} logo" style="max-width: 140px;">
</div>
@endcomponent
