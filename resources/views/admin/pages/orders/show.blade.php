<div class="card-body">
    <table class="table table-bordered">
        <tr>
            <th class="w-25">Order ID</th>
            <td class="w-75">#{{ $order->id }}</td>
        </tr>
        <tr>
            <th>Tracking Number</th>
            <td><strong>{{ $order->tracking_number }}</strong></td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'shipped' ? 'success' : 'secondary') }}">
                    {{ ucfirst($order->status ?? '—') }}
                </span>
            </td>
        </tr>
        <tr>
            <th>Customer Name</th>
            <td>{{ $order->customer_name }}</td>
        </tr>
        <tr>
            <th>Customer Email</th>
            <td><a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a></td>
        </tr>
        @if($order->customer_phone)
        <tr>
            <th>Customer Phone</th>
            <td><a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></td>
        </tr>
        @endif
        <tr>
            <th>Payment Method</th>
            <td>{{ $order->payment_method === 'online' ? 'Online' : 'Cash on delivery' }}</td>
        </tr>
        <tr>
            <th>Subtotal</th>
            <td>{{ number_format($order->subtotal, 0) }} PKR</td>
        </tr>
        <tr>
            <th>Shipping Cost</th>
            <td>{{ number_format($order->shipping_cost, 0) }} PKR</td>
        </tr>
        <tr>
            <th>Tax</th>
            <td>{{ number_format($order->tax_estimate, 0) }} PKR</td>
        </tr>
        <tr>
            <th>Total</th>
            <td><strong>{{ number_format($order->total, 0) }} PKR</strong></td>
        </tr>
        <tr>
            <th>Placed At</th>
            <td>{{ $order->created_at?->format('d M Y, H:i') }}</td>
        </tr>
        @if($order->shipping_address && is_array($order->shipping_address))
        <tr>
            <th>Shipping Address</th>
            <td>
                {{ $order->shipping_address['addressLine1'] ?? '' }}
                @if(!empty($order->shipping_address['addressLine2']))
                    <br>{{ $order->shipping_address['addressLine2'] }}
                @endif
                <br>{{ implode(', ', array_filter([
                    $order->shipping_address['city'] ?? null,
                    $order->shipping_address['state'] ?? null,
                    $order->shipping_address['postalCode'] ?? null,
                    $order->shipping_address['country'] ?? null,
                ])) }}
            </td>
        </tr>
        @endif
        @if($order->notes)
        <tr>
            <th>Notes</th>
            <td style="white-space: pre-wrap;">{{ $order->notes }}</td>
        </tr>
        @endif
    </table>

    <h6 class="mt-4 mb-2">Items ({{ $order->items->count() }})</h6>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ number_format($item->price, 0) }} PKR</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price * $item->quantity, 0) }} PKR</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
