<div class="card-body">
    <form class="row g-3" id="newsletter-form"
    action="{{ isset($newsletter) ? route('admin.newsletter-management.update', $newsletter->id) : route('admin.newsletter-management.store') }}"
    method="POST">
        @csrf
        @if (isset($newsletter))
            @method('PUT')
        @endif
        <div class="col-md-12">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                value="{{ isset($newsletter) ? $newsletter->email : '' }}">
        </div>
        <div class="col-12">
            <button type="submit" id="newsletter-btn"
                class="btn btn-light mt-3 px-5">{{ isset($newsletter) ? 'Update' : 'Save' }}</button>
        </div>
    </form>
</div>
