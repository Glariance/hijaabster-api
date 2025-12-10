<div class="card-body">
    <form class="row g-3" id="attribute-form"
        action="{{ isset($attribute) ? route('admin.inventory.attributes.update', $attribute->id) : route('admin.inventory.attributes.store') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if (isset($attribute))
            @method('PUT')
        @endif
        <!-- Name -->
        <div class="col-md-12">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required placeholder="Enter Attribute"
                value="{{ old('name', $attribute->name ?? '') }}">
        </div>
        <!-- Options -->
        <div class="col-md-12 d-flex flex-column" id="options-wrapper">
            <label class="form-label">Options</label>
            @php
                $options = old('options', $attribute->options ?? ['']);
            @endphp
            @foreach ($options as $index => $option)
                <div class="form-group d-flex {{ $index > 0 ? 'mt-2' : '' }}">
                    <div class="flex-grow-1">
                        <input type="hidden" name="option_ids[]" value="{{ is_object($option) ? $option->id : null }}">
                        <input type="text" name="options[]" class="form-control" required placeholder="Enter Option"
                            value="{{ is_object($option) ? $option->value : $option }}">
                    </div>
                    @if ($index === 0)
                        <button type="button" class="btn btn-light btn-sm ms-4" onclick="addMoreOptions()">
                            Add More Options
                        </button>
                    @else
                        {{-- <button type="button" class="btn btn-light btn-sm ms-4" onclick="removeOption(this)">
                            <i class="bx bx-trash"></i>
                        </button> --}}
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <button type="submit" id="attribute-btn" class="btn btn-light mt-3 px-5">
                {{ isset($attribute) ? 'Update' : 'Save' }}
            </button>
        </div>
    </form>
</div>

<!-- Script to add/remove option fields -->
<script>
    function addMoreOptions() {
        event.preventDefault();
        const wrapper = document.getElementById('options-wrapper');
        const newField = document.createElement('div');
        newField.classList.add('form-group', 'd-flex', 'mt-2');

        newField.innerHTML = `
        <div class="flex-grow-1">
            <input type="hidden" name="option_ids[]" value="">
            <input type="text" name="options[]" class="form-control" required placeholder="Enter Option">
        </div>
        <button type="button" class="btn btn-light btn-sm ms-4" onclick="removeOption(this)">
            <i class="bx bx-trash"></i>
        </button>
    `;

        wrapper.appendChild(newField);
    }

    function removeOption(button) {
        button.parentElement.remove();
    }
</script>
