<div class="card-body">
    <table class="table table-bordered align-middle text-break" style="table-layout: fixed;">
        <tr>
            <th class="w-25">Code</th>
            <td class="w-75">{{ $coupon->code }}</td>
        </tr>
        <tr>
            <th class="w-25">Name</th>
            <td class="w-75">{{ $coupon->name }}</td>
        </tr>
        @if($coupon->description)
        <tr>
            <th>Description</th>
            <td>{{ $coupon->description }}</td>
        </tr>
        @endif
        <tr>
            <th>Discount Type</th>
            <td>{{ ucfirst($coupon->discount_type) }}</td>
        </tr>
        <tr>
            <th>Discount Value</th>
            <td>
                @if($coupon->discount_type === 'percentage')
                    {{ $coupon->discount_value }}%
                @else
                    PKR {{ number_format($coupon->discount_value, 2) }}
                @endif
            </td>
        </tr>
        @if($coupon->minimum_purchase)
        <tr>
            <th>Minimum Purchase</th>
            <td>PKR {{ number_format($coupon->minimum_purchase, 2) }}</td>
        </tr>
        @endif
        @if($coupon->maximum_discount)
        <tr>
            <th>Maximum Discount</th>
            <td>PKR {{ number_format($coupon->maximum_discount, 2) }}</td>
        </tr>
        @endif
        @if($coupon->usage_limit)
        <tr>
            <th>Usage Limit</th>
            <td>{{ $coupon->usage_limit }} times</td>
        </tr>
        @endif
        <tr>
            <th>Used Count</th>
            <td>{{ $coupon->used_count }} times</td>
        </tr>
        @if($coupon->valid_from)
        <tr>
            <th>Valid From</th>
            <td>{{ $coupon->valid_from->format('d M Y, h:i A') }}</td>
        </tr>
        @endif
        @if($coupon->valid_until)
        <tr>
            <th>Valid Until</th>
            <td>{{ $coupon->valid_until->format('d M Y, h:i A') }}</td>
        </tr>
        @endif
        <tr>
            <th>Status</th>
            <td>{!! defaultBadge(config('constants.status.' . $coupon->status), 25) !!}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $coupon->created_at->format('d M Y, h:i A') }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $coupon->updated_at->format('d M Y, h:i A') }}</td>
        </tr>
    </table>
</div>

