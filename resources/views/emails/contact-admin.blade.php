@component('mail::message')
@php
    $primary = '#7c3aed';
    $deep = '#2b0050';
    $muted = '#4b5563';
@endphp

<div style="text-align:center; margin-bottom: 16px;">
    <img src="{{ (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png' }}" alt="{{ config('app.name') }} logo" style="max-width: 180px;">
</div>

<div style="background: linear-gradient(135deg, {{ $deep }} 0%, {{ $primary }} 100%); padding: 24px; border-radius: 12px; margin-bottom: 24px; color: #f3e8ff;">
    <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #e9d5ff;">New Website Inquiry</h1>
    <p style="margin: 8px 0 0; color: #e9d5ff;">A visitor submitted the contact form. Details are below.</p>
</div>

@component('mail::panel')
**Summary:** A new inquiry from {{ $inquiry->name }} ({{ $inquiry->email }})  
**Service:** {{ $inquiry->service ?? 'Not specified' }}  
**Company:** {{ $inquiry->company ?? 'Not provided' }}  
**Phone:** {{ $inquiry->phone ?? 'Not provided' }}
@endcomponent

<table width="100%" style="border-collapse: collapse; margin: 12px 0;">
    <tbody>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; color: {{ $muted }};">Name</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $inquiry->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; color: {{ $muted }};">Email</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $inquiry->email }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; color: {{ $muted }};">Company</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $inquiry->company ?? 'Not provided' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; color: {{ $muted }};">Phone</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $inquiry->phone ?? 'Not provided' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; color: {{ $muted }};">Service</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $inquiry->service ?? 'Not specified' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; color: {{ $muted }};">Message</td>
            <td style="padding: 8px; border: 1px solid #e5e7eb;">{!! nl2br(e($inquiry->message)) !!}</td>
        </tr>
    </tbody>
</table>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
