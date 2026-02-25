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
    <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #ffffff;">New Order Received</h1>
    <p style="margin: 8px 0 0; color: #ffffff;">Tracking: <strong style="color: #ffffff;">{{ $order->tracking_number }}</strong></p>
</div>

<div style="background: {{ $bgLight }}; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
    <p style="margin: 0 0 4px; font-weight: 600; color: {{ $primary }};">Customer</p>
    <p style="margin: 0; color: {{ $muted }};">{{ $order->customer_name }} &lt;{{ $order->customer_email }}&gt; @if($order->customer_phone) · {{ $order->customer_phone }} @endif</p>
</div>

<table width="100%" style="border-collapse: collapse; margin-bottom: 16px; font-size: 14px;">
    <thead>
        <tr style="background: {{ $bgLight }};">
            <th style="padding: 10px; text-align: left; width: 56px;">Image</th>
            <th style="padding: 10px; text-align: left;">Item</th>
            <th style="padding: 10px; text-align: right;">Qty</th>
            <th style="padding: 10px; text-align: right;">Price</th>
            <th style="padding: 10px; text-align: right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        @php
            $imgPath = $item->product && $item->product->mediaFeatured ? $item->product->mediaFeatured->path : null;
            $imgUrl = $imgPath ? rtrim(config('app.url'), '/') . '/storage/' . ltrim($imgPath, '/') : null;
        @endphp
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 10px; vertical-align: middle;">
                @if($imgUrl)
                <img src="{{ $imgUrl }}" alt="{{ $item->name }}" width="48" height="48" style="display: block; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;" />
                @else
                <span style="color: #9ca3af; font-size: 12px;">—</span>
                @endif
            </td>
            <td style="padding: 10px;">{{ $item->name }}</td>
            <td style="padding: 10px; text-align: right;">{{ $item->quantity }}</td>
            <td style="padding: 10px; text-align: right;">PKR {{ number_format($item->price, 0) }}</td>
            <td style="padding: 10px; text-align: right;">PKR {{ number_format($item->price * $item->quantity, 0) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="margin: 0 0 4px; color: {{ $muted }};">Subtotal: PKR {{ number_format($order->subtotal, 0) }} · Shipping: PKR {{ number_format($order->shipping_cost, 0) }} · Tax: PKR {{ number_format($order->tax_estimate, 0) }}</p>
<p style="margin: 0 0 8px; font-weight: 700;">Total: PKR {{ number_format($order->total, 0) }}</p>
<p style="margin: 0; color: {{ $muted }};">Payment: {{ $order->payment_method === 'online' ? 'Online' : 'Cash on delivery' }}</p>

@if($order->shipping_address && count($order->shipping_address))
<div style="margin-top: 16px; padding: 12px; background: {{ $bgLight }}; border-radius: 8px;">
    <p style="margin: 0 0 4px; font-weight: 600; font-size: 12px;">Shipping address</p>
    <p style="margin: 0; font-size: 14px; color: {{ $muted }};">
        @foreach($order->shipping_address as $v) @if($v) {{ $v }}<br> @endif @endforeach
    </p>
</div>
@endif

@if($order->notes)
<p style="margin-top: 12px; padding: 8px; background: #fef3c7; border-radius: 6px;"><strong>Notes:</strong> {{ $order->notes }}</p>
@endif

Thanks,<br>{{ config('app.name') }}
@include('emails.partials.logo-footer')
@endcomponent
