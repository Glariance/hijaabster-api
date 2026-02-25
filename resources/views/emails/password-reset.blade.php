@php
$emailLogoUrl = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
$primary = '#b8325d';
$primaryDark = '#9A2E4A';
$bgLight = '#f1d8e2';
$muted = '#6B7280';
@endphp
@component('mail::message')
@include('emails.partials.logo-header')

<div style="background: linear-gradient(135deg, {{ $primaryDark }} 0%, {{ $primary }} 100%); padding: 32px; border-radius: 12px; margin-bottom: 24px; color: #ffffff;">
    <h1 style="margin: 0 0 12px; font-size: 24px; font-weight: 800; color: #ffffff;">Reset your password</h1>
    <p style="margin: 0; line-height: 1.6; color: #ffffff;">
        Hi {{ $user->name ?? 'there' }}, we received a request to reset the password for your {{ config('app.name') }} account.
    </p>
</div>

@component('mail::panel')
Click the button below to choose a new password. The link will expire in 60 minutes.
@endcomponent

@component('mail::button', ['url' => $resetUrl, 'color' => 'primary'])
Reset Password
@endcomponent

If the button doesn't work, copy and paste this link into your browser:

<div style="margin: 12px 0; padding: 12px; background: {{ $bgLight }}; border: 1px solid #e5e7eb; border-radius: 8px; word-break: break-all; color: #1f2937;">
    {{ $resetUrl }}
</div>

For reference, your reset token is:

<div style="margin: 8px 0 16px; padding: 10px; background: {{ $bgLight }}; border: 1px solid {{ $primary }}; border-radius: 8px; word-break: break-all; color: {{ $primaryDark }}; font-weight: 700;">
    {{ $token }}
</div>

If you didn't request this, you can safely ignore this email.

Thanks,<br>
@include('emails.partials.logo-footer')
@endcomponent
