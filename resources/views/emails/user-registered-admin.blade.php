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
    <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #ffffff;">New User Registered</h1>
    <p style="margin: 8px 0 0; color: #ffffff;">A new account was created on the site.</p>
</div>

<table width="100%" style="border-collapse: collapse; margin: 12px 0;">
    <tbody>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: {{ $bgLight }}; font-weight: 600; color: {{ $muted }};">Name</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $user->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: {{ $bgLight }}; font-weight: 600; color: {{ $muted }};">Email</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: {{ $bgLight }}; font-weight: 600; color: {{ $muted }};">Role</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $user->role->name ?? 'User' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: {{ $bgLight }}; font-weight: 600; color: {{ $muted }};">Joined</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $user->created_at?->format('Y-m-d') }}</td>
        </tr>
    </tbody>
</table>

Thanks,<br>
{{ config('app.name') }}
@include('emails.partials.logo-footer')
@endcomponent
