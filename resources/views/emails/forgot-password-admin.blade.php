@php
$emailLogoUrl = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
$primary = '#b8325d';
$primaryDark = '#9A2E4A';
$bgLight = '#f1d8e2';
$muted = '#6B7280';
@endphp
@component('mail::message')
@include('emails.partials.logo-header')

<div style="background: linear-gradient(135deg, {{ $primaryDark }} 0%, {{ $primary }} 100%); padding: 20px; border-radius: 12px; margin-bottom: 20px; color: #ffffff;">
    <h1 style="margin: 0; font-size: 18px; font-weight: 700; color: #ffffff;">Password reset requested</h1>
    <p style="margin: 8px 0 0; color: #ffffff;">A user has requested a password reset link.</p>
</div>

<table width="100%" style="border-collapse: collapse; font-size: 14px;">
    <tr>
        <td style="padding: 8px; background: {{ $bgLight }}; font-weight: 600; color: {{ $muted }};">User</td>
        <td style="padding: 8px;">{{ $user->name }}</td>
    </tr>
    <tr>
        <td style="padding: 8px; background: {{ $bgLight }}; font-weight: 600; color: {{ $muted }};">Email</td>
        <td style="padding: 8px;">{{ $user->email }}</td>
    </tr>
    <tr>
        <td style="padding: 8px; background: {{ $bgLight }}; font-weight: 600; color: {{ $muted }};">Time</td>
        <td style="padding: 8px;">{{ now()->format('Y-m-d H:i') }}</td>
    </tr>
</table>

Thanks,<br>
{{ config('app.name') }}
@include('emails.partials.logo-footer')
@endcomponent
