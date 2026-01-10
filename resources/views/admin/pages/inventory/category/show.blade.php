<div class="card-body">
    <table class="table table-bordered align-middle text-break" style="table-layout: fixed;">
        <tr>
            <th class="w-25">Name</th>
            <td class="w-75">{{ $category->name }}</td>
        </tr>
        <tr>
            <th class="w-25">Slug</th>
            <td class="w-75">{{ $category->slug }}</td>
        </tr>
        <tr>
            <th>Parent</th>
            <td>{{ $category->parent->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{!! defaultBadge(ucfirst(config('constants.status.' . $category->status)), 25) !!}</td>
        </tr>
        <tr>
            <th>Received-At</th>
            <td>{{ $category->created_at->format('d M Y, h:i A') }}</td>
        </tr>
        <tr>
            <th>Desciption</th>
            <td>{!! $category->description !!}</td>
        </tr>
        @if($category->media && $category->media->count() > 0)
            <tr>
                <th>Images</th>
                <td class="py-3">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($category->media as $media)
                            <div class="position-relative">
                                <img src="{{ asset($media->path) }}" alt="{{ $category->slug }}" class="rounded border"
                                    style="max-width: 280px; max-height: 240px; object-fit: contain;">
                            </div>
                        @endforeach
                    </div>
                </td>
            </tr>
        @endif
    </table>
</div>
