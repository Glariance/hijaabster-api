<div class="card-body">
    <form class="row g-3" id="tag-form"
        action="{{ isset($tag) ? route('admin.tags.update', $tag->id) : route('admin.tags.store') }}" method="POST">
        @csrf
        @if (isset($tag))
            @method('PUT')
        @endif
        <div class="col-md-12">
            <label for="name" class="form-label">Name</label>
            <input type="name" name="name" class="form-control" value="{{ isset($tag) ? $tag->name : '' }}">
        </div>
        <div class="col-12">
            <button type="submit" id="tag-btn"
                class="btn btn-light mt-3 px-5">{{ isset($tag) ? 'Update' : 'Save' }}</button>
        </div>
    </form>
</div>
