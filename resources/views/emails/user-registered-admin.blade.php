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
    <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #e9d5ff;">New User Registered</h1>
    <p style="margin: 8px 0 0; color: #e9d5ff;">A new account was created on the site.</p>
</div>

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
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; color: {{ $muted }};">Role</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $user->role->name ?? 'User' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; color: {{ $muted }};">Joined</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $user->created_at?->format('Y-m-d') }}</td>
        </tr>
    </tbody>
</table>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
