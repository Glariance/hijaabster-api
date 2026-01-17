<div class="card-body">
    <div class="row">
        <div class="col-md-6">
            <h5>Bundle Information</h5>
            <table class="table table-bordered">
                <tr>
                    <th width="40%">Name:</th>
                    <td>{{ $bundle->name }}</td>
                </tr>
                <tr>
                    <th>Slug:</th>
                    <td>{{ $bundle->slug }}</td>
                </tr>
                <tr>
                    <th>Description:</th>
                    <td>{!! $bundle->description ?? 'N/A' !!}</td>
                </tr>
                <tr>
                    <th>Discount Type:</th>
                    <td>{{ ucfirst($bundle->discount_type) }}</td>
                </tr>
                <tr>
                    <th>Discount Value:</th>
                    <td>
                        @if($bundle->discount_type === 'percentage')
                            {{ $bundle->discount_value }}%
                        @else
                            PKR {{ number_format($bundle->discount_value, 2) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Total Price:</th>
                    <td>PKR {{ number_format($bundle->total_price ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>Bundle Price:</th>
                    <td><strong>PKR {{ number_format($bundle->bundle_price ?? 0, 2) }}</strong></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>{!! defaultBadge(config('constants.status.' . $bundle->status), 25) !!}</td>
                </tr>
                <tr>
                    <th>Created By:</th>
                    <td>{{ $bundle->creator->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Created At:</th>
                    <td>{{ $bundle->created_at->format('d M Y, h:i A') }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h5>Products in Bundle ({{ $bundle->products->count() }})</h5>
            @if($bundle->products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bundle->products as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->pivot->quantity }}</td>
                                    <td>PKR {{ number_format($product->pivot->price ?? $product->base_price, 2) }}</td>
                                    <td>PKR {{ number_format(($product->pivot->price ?? $product->base_price) * $product->pivot->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">Total:</th>
                                <th>PKR {{ number_format($bundle->total_price ?? 0, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="4">Bundle Price (After Discount):</th>
                                <th>PKR {{ number_format($bundle->bundle_price ?? 0, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-muted">No products in this bundle.</p>
            @endif
        </div>
    </div>
</div>

