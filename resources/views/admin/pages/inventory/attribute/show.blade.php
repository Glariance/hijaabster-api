<div class="card-body">
    <table class="table table-bordered">
        <tr>
            <th class="w-25">Name</th>
            <td class="w-75">{{ $brand->name }}</td>
        </tr>
        <tr>
            <th class="w-25">Slug</th>
            <td class="w-75">{{ $brand->slug }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{!! ucfirst(defaultBadge(config('constants.status.' . $brand->status), 25)) !!}</td>
        </tr>
        </tr>
        <tr>
            <th>Created-At</th>
            <td>{{ $brand->created_at->format('d M Y, h:i A') }}</td>
        <tr>
            <th>Description</th>
            <td>{!! $brand->description !!}</td>
        </tr>
        <tr>
            <th>Image</th>
            <td><img src="{{ $brand->media->path }}" alt="{{ $brand->slug }}" width="300"></td>
        </tr>
    </table>

</div>
