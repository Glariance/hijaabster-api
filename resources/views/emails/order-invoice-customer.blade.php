@php
$emailLogoUrl = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
$primary = '#b8325d';
$primaryDark = '#9A2E4A';
$bgLight = '#f1d8e2';
$muted = '#6B7280';
@endphp
@component('mail::message')
@include('emails.partials.logo-header')

<div style="background: linear-gradient(135deg, {{ $primaryDark }} 0%, {{ $primary }} 100%); padding: 32px; border-radius: 16px; margin-bottom: 24px;">
    <h1 style="margin: 0; font-size: 24px; font-weight: 700;"><span style="color: #ffffff;">Order Confirmation</span></h1>
    <p style="margin: 12px 0 0; opacity: 0.95;"><span style="color: #ffffff;">Thank you for your order. Your tracking number is below.</span></p>
</div>

<div style="background: {{ $bgLight }}; padding: 20px; border-radius: 12px; margin-bottom: 24px; border-left: 4px solid {{ $primary }};">
    <p style="margin: 0 0 8px; font-weight: 600; color: {{ $primary }}; font-size: 12px; text-transform: uppercase;">Tracking number</p>
    <p style="margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 1px;">{{ $order->tracking_number }}</p>
</div>

<table width="100%" style="border-collapse: collapse; margin-bottom: 24px; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <thead>
        <tr style="background: {{ $bgLight }};">
            <th style="padding: 12px; text-align: left; color: {{ $primary }}; font-size: 12px; width: 56px;">Image</th>
            <th style="padding: 12px; text-align: left; color: {{ $primary }}; font-size: 12px;">Item</th>
            <th style="padding: 12px; text-align: right; color: {{ $primary }}; font-size: 12px;">Qty</th>
            <th style="padding: 12px; text-align: right; color: {{ $primary }}; font-size: 12px;">Price</th>
            <th style="padding: 12px; text-align: right; color: {{ $primary }}; font-size: 12px;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        @php
            $imgPath = $item->product && $item->product->mediaFeatured ? $item->product->mediaFeatured->path : null;
            $imgUrl = $imgPath ? rtrim(config('app.url'), '/') . '/storage/' . ltrim($imgPath, '/') : null;
        @endphp
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px; vertical-align: middle;">
                @if($imgUrl)
                <img src="{{ $imgUrl }}" alt="{{ $item->name }}" width="48" height="48" style="display: block; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;" />
                @else
                <span style="color: #9ca3af; font-size: 12px;">—</span>
                @endif
            </td>
            <td style="padding: 12px; color: #374151;">{{ $item->name }}</td>
            <td style="padding: 12px; text-align: right; color: {{ $muted }};">{{ $item->quantity }}</td>
            <td style="padding: 12px; text-align: right; color: {{ $muted }};">PKR {{ number_format($item->price, 0) }}</td>
            <td style="padding: 12px; text-align: right; font-weight: 600;">PKR {{ number_format($item->price * $item->quantity, 0) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table width="100%" style="border-collapse: collapse; margin-bottom: 24px;">
    <tr><td style="padding: 4px; text-align: right; color: {{ $muted }};">Subtotal</td><td style="padding: 4px; text-align: right; width: 120px;">PKR {{ number_format($order->subtotal, 0) }}</td></tr>
    <tr><td style="padding: 4px; text-align: right; color: {{ $muted }};">Shipping</td><td style="padding: 4px; text-align: right;">PKR {{ number_format($order->shipping_cost, 0) }}</td></tr>
    <tr><td style="padding: 4px; text-align: right; color: {{ $muted }};">Tax (est.)</td><td style="padding: 4px; text-align: right;">PKR {{ number_format($order->tax_estimate, 0) }}</td></tr>
    <tr><td style="padding: 12px 4px; text-align: right; font-weight: 700; color: {{ $primary }};">Total</td><td style="padding: 12px 4px; text-align: right; font-weight: 700;">PKR {{ number_format($order->total, 0) }}</td></tr>
</table>

<p style="margin: 0; color: {{ $muted }}; font-size: 14px;">Payment: {{ $order->payment_method === 'online' ? 'Pay online' : 'Cash on delivery' }}</p>

<div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb; text-align: center;">
    <p style="margin: 0; color: {{ $muted }}; font-size: 14px;">Thanks,<br><strong style="color: {{ $primary }};">{{ config('app.name') }} Team</strong></p>
</div>
@include('emails.partials.logo-footer')
@endcomponent
