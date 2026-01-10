<div class="card-body">
    <form class="row g-3" id="coupon-form"
        action="{{ isset($coupon) ? route('admin.coupon.update', $coupon->id) : route('admin.coupon.store') }}"
        method="POST">
        @csrf
        @if (isset($coupon))
            @method('PUT')
        @endif

        <!-- Code -->
        <div class="col-md-6">
            <label for="code" class="form-label">Coupon Code <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code" class="form-control" 
                value="{{ isset($coupon) ? $coupon->code : old('code') }}" 
                placeholder="e.g., SAVE20">
            <small class="text-muted">Unique code for the coupon</small>
        </div>

        <!-- Name -->
        <div class="col-md-6">
            <label for="name" class="form-label">Coupon Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" 
                value="{{ isset($coupon) ? $coupon->name : old('name') }}" 
                placeholder="e.g., Summer Sale 2024">
        </div>

        <!-- Description -->
        <div class="col-md-12">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3"
                placeholder="Optional description for this coupon">{{ isset($coupon) ? $coupon->description : old('description') }}</textarea>
        </div>

        <!-- Discount Type -->
        <div class="col-md-6">
            <label for="discount_type" class="form-label">Discount Type <span class="text-danger">*</span></label>
            <select name="discount_type" id="discount_type" class="form-select">
                <option value="percentage" {{ (isset($coupon) && $coupon->discount_type == 'percentage') || old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                <option value="fixed" {{ (isset($coupon) && $coupon->discount_type == 'fixed') || old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (PKR)</option>
            </select>
        </div>

        <!-- Discount Value -->
        <div class="col-md-6">
            <label for="discount_value" class="form-label">Discount Value <span class="text-danger">*</span></label>
            <input type="number" name="discount_value" id="discount_value" class="form-control" 
                step="0.01" min="0" 
                value="{{ isset($coupon) ? $coupon->discount_value : old('discount_value', 0) }}" 
                placeholder="e.g., 20">
            <small class="text-muted" id="discount-hint">Enter percentage (e.g., 20 for 20%)</small>
        </div>

        <!-- Minimum Purchase -->
        <div class="col-md-6">
            <label for="minimum_purchase" class="form-label">Minimum Purchase Amount</label>
            <input type="number" name="minimum_purchase" id="minimum_purchase" class="form-control" 
                step="0.01" min="0" 
                value="{{ isset($coupon) ? $coupon->minimum_purchase : old('minimum_purchase') }}" 
                placeholder="e.g., 100">
            <small class="text-muted">Minimum order amount in PKR to use this coupon (optional)</small>
        </div>

        <!-- Maximum Discount (for percentage only) -->
        <div class="col-md-6" id="max-discount-field" style="{{ (isset($coupon) && $coupon->discount_type == 'percentage') || old('discount_type') == 'percentage' ? '' : 'display: none;' }}">
            <label for="maximum_discount" class="form-label">Maximum Discount Amount</label>
            <input type="number" name="maximum_discount" id="maximum_discount" class="form-control" 
                step="0.01" min="0" 
                value="{{ isset($coupon) ? $coupon->maximum_discount : old('maximum_discount') }}" 
                placeholder="e.g., 50">
            <small class="text-muted">Maximum discount cap in PKR for percentage coupons (optional)</small>
        </div>

        <!-- Usage Limit -->
        <div class="col-md-6">
            <label for="usage_limit" class="form-label">Usage Limit</label>
            <input type="number" name="usage_limit" id="usage_limit" class="form-control" 
                min="1" 
                value="{{ isset($coupon) ? $coupon->usage_limit : old('usage_limit') }}" 
                placeholder="e.g., 100">
            <small class="text-muted">Maximum number of times this coupon can be used (optional)</small>
        </div>

        <!-- Valid From -->
        <div class="col-md-6">
            <label for="valid_from" class="form-label">Valid From</label>
            <input type="datetime-local" name="valid_from" id="valid_from" class="form-control" 
                value="{{ isset($coupon) && $coupon->valid_from ? $coupon->valid_from->format('Y-m-d\TH:i') : old('valid_from') }}">
            <small class="text-muted">Start date and time (optional)</small>
        </div>

        <!-- Valid Until -->
        <div class="col-md-6">
            <label for="valid_until" class="form-label">Valid Until</label>
            <input type="datetime-local" name="valid_until" id="valid_until" class="form-control" 
                value="{{ isset($coupon) && $coupon->valid_until ? $coupon->valid_until->format('Y-m-d\TH:i') : old('valid_until') }}">
            <small class="text-muted">End date and time (optional)</small>
        </div>

        <!-- Status -->
        <div class="col-md-12">
            <label class="form-label">Status</label>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status" 
                    @checked(isset($coupon) ? $coupon->status == 1 : true)>
                <label class="form-check-label" for="status">
                    <span id="status-label">{{ isset($coupon) ? ($coupon->status == 1 ? 'Active' : 'Inactive') : 'Active' }}</span>
                </label>
            </div>
            <input type="hidden" name="status" id="status-hidden" value="{{ isset($coupon) ? $coupon->status : 1 }}">
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <button type="submit" id="coupon-btn" class="btn btn-light mt-3 px-5">
                {{ isset($coupon) ? 'Update' : 'Save' }}
            </button>
        </div>
    </form>
</div>
<script>
    $(function() {
        // Status switch handler
        $('#status').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('#status-hidden').val(isChecked ? 1 : 0);
            $('#status-label').text(isChecked ? 'Active' : 'Inactive');
        });

        // Discount type change handler
        $('#discount_type').on('change', function() {
            const type = $(this).val();
            if (type === 'percentage') {
                $('#discount-hint').text('Enter percentage (e.g., 20 for 20%)');
                $('#max-discount-field').show();
            } else {
                $('#discount-hint').text('Enter fixed amount (e.g., 20 for PKR 20)');
                $('#max-discount-field').hide();
                $('#maximum_discount').val('');
            }
        });
    });
</script>

