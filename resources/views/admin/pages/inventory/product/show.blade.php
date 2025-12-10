<div class="card-body">
    <table class="table table-bordered align-middle text-break" style="table-layout: fixed;">
        <tr>
            <th class="w-25">Name</th>
            <td class="w-75">{{ $product->name }}</td>
        </tr>
        <tr>
            <th class="w-25">Slug</th>
            <td class="w-75">{{ $product->slug }}</td>
        </tr>
        <tr>
            <th>Brand</th>
            <td>{{ $product->brand->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Category</th>
            <td>{{ $product->category && $product->category->parent ? $product->category->parent->name . ' > ' . $product->category->name : ($product->category->name ?? '-') }}</td>
        </tr>
        @if ($product->amazon_link)
            <tr>
                <th>Amazon Link</th>
                <td>
                    <a href="{{ $product->amazon_link }}" target="_blank" rel="noopener" class="d-inline-block text-break" style="word-break: break-word;">
                        {{ $product->amazon_link }}
                    </a>
                </td>
            </tr>
        @endif
        <tr>
            <th>Status</th>
            <td>{!! defaultBadge(ucfirst(config('constants.product.status.' . $product->status)), 25) !!}</td>
        </tr>
        <tr>
            <th>Featured</th>
            <td>{!! defaultBadge(ucfirst(config('constants.product.featured.' . $product->featured)), 25) !!}</td>
        </tr>
        <tr>
            <th>Created-At</th>
            <td>{{ $product->created_at->format('d M Y, h:i A') }}</td>
        </tr>
        <tr>
            <th>Desciption</th>
            <td>{!! $product->description !!}</td>
        </tr>
        @if ($product->media)
            <tr>
                <th>Image</th>
                <td class="d-flex flex-wrap gap-2">
                    @foreach ($product->media as $media)
                        <img src="{{ asset('storage/' . $media->path) }}" alt="{{ $product->name }}" width="120" class="rounded border">
                    @endforeach
                </td>
            </tr>
        @endif
    </table>
</div>
