@extends('admin.layouts.app')
@section('title', env('APP_NAME') . ' | Bundle Management')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Bundle Management</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ isset($bundle) ? 'Edit Bundle' : 'Create Bundle' }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" id="bundle-form"
                        action="{{ isset($bundle) ? route('admin.bundle.update', $bundle->id) : route('admin.bundle.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($bundle))
                            @method('PUT')
                        @endif

                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Bundle Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" 
                                value="{{ isset($bundle) ? $bundle->name : old('name') }}" 
                                placeholder="e.g., Summer Capsule Trio" required>
                        </div>

                        <!-- Slug -->
                        <div class="col-md-6">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" name="slug" id="slug" class="form-control" 
                                value="{{ isset($bundle) ? $bundle->slug : old('slug') }}" 
                                placeholder="auto-generated-from-name">
                            <small class="text-white">Leave empty to auto-generate from name</small>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="myEditor" cols="30" rows="10">{{ isset($bundle) ? $bundle->description : old('description') }}</textarea>
                        </div>

                        <!-- Discount Type -->
                        <div class="col-md-6">
                            <label for="discount_type" class="form-label">Discount Type <span class="text-danger">*</span></label>
                            <select name="discount_type" id="discount_type" class="form-select" required>
                                <option value="percentage" {{ (isset($bundle) && $bundle->discount_type == 'percentage') || old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ (isset($bundle) && $bundle->discount_type == 'fixed') || old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (PKR)</option>
                            </select>
                        </div>

                        <!-- Discount Value -->
                        <div class="col-md-6">
                            <label for="discount_value" class="form-label">Discount Value <span class="text-danger">*</span></label>
                            <input type="number" name="discount_value" id="discount_value" class="form-control" 
                                step="0.01" min="0" 
                                value="{{ isset($bundle) ? $bundle->discount_value : old('discount_value', 0) }}" 
                                placeholder="e.g., 20" required>
                            <small class="text-white" id="discount-hint">Enter percentage (e.g., 20 for 20%)</small>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="status" 
                                    @checked(isset($bundle) ? $bundle->status == 1 : true)>
                                <label class="form-check-label" for="status">
                                    <span id="status-label">{{ isset($bundle) ? ($bundle->status == 1 ? 'Active' : 'Inactive') : 'Active' }}</span>
                                </label>
                            </div>
                            <input type="hidden" name="status" id="status-hidden" value="{{ isset($bundle) ? $bundle->status : 1 }}">
                        </div>

                        <!-- Products Section -->
                        <div class="col-md-12">
                            <hr>
                            <h5>Products in Bundle <span class="text-danger">*</span></h5>
                            <div class="mb-3">
                                <label for="product_search" class="form-label">Search and Add Products</label>
                                <select id="product_search" class="form-select">
                                    <option value="">Select a product to add...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                            data-name="{{ $product->name }}"
                                            data-price="{{ $product->base_price }}"
                                            data-image="{{ $product->mediaFeatured ? asset($product->mediaFeatured->path) : '' }}">
                                            {{ $product->name }} - PKR {{ number_format($product->base_price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="products-container">
                                @if(isset($bundle) && $bundle->products->count() > 0)
                                    @foreach($bundle->products as $index => $product)
                                        <div class="product-item card mb-3" data-product-id="{{ $product->id }}">
                                            <div class="card-body">
                                                <div class="row align-items-end">
                                                    <div class="col-md-1 text-center">
                                                        <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $product->id }}">
                                                        <input type="hidden" name="products[{{ $index }}][sort_order]" value="{{ $product->pivot->sort_order ?? $index }}" class="sort-order">
                                                        <span class="badge bg-secondary d-inline-block mb-2">{{ $index + 1 }}</span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Product Name</label>
                                                        <div class="form-control-plaintext"><strong class="text-white">{{ $product->name }}</strong></div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Quantity</label>
                                                        <div class="input-group">
                                                            <button type="button" class="btn btn-light btn-sm quantity-decrease" style="border-radius: 0.375rem 0 0 0.375rem;">-</button>
                                                            <input type="number" name="products[{{ $index }}][quantity]" 
                                                                class="form-control product-quantity text-center" 
                                                                value="{{ $product->pivot->quantity ?? 1 }}" 
                                                                min="1" required style="border-left: 0; border-right: 0;">
                                                            <button type="button" class="btn btn-light btn-sm quantity-increase" style="border-radius: 0 0.375rem 0.375rem 0;">+</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Price (PKR) <span class="text-white">(Default: {{ number_format($product->base_price, 2) }})</span></label>
                                                        <input type="number" name="products[{{ $index }}][price]" 
                                                            class="form-control product-price" 
                                                            value="{{ $product->pivot->price ?? $product->base_price }}" 
                                                            step="0.01" min="0">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Subtotal</label>
                                                        <input type="text" class="form-control product-subtotal" readonly 
                                                            value="PKR {{ number_format(($product->pivot->price ?? $product->base_price) * ($product->pivot->quantity ?? 1), 2) }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Action</label>
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button" class="btn btn-light btn-sm remove-product d-flex align-items-center justify-content-center" title="Remove Product" style="width: 40px; height: 38px; padding: 0;">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="mt-3">
                                <strong>Total Price: <span id="total-price">PKR 0.00</span></strong><br>
                                <strong>Bundle Price (After Discount): <span id="bundle-price">PKR 0.00</span></strong>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" id="bundle-btn" class="btn btn-light mt-3 px-5">{{ isset($bundle) ? 'Update Bundle' : 'Create Bundle' }}</button>
                            <a href="{{ route('admin.bundle.index') }}" class="btn btn-light mt-3 px-5">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let productIndex = {{ isset($bundle) && $bundle->products->count() > 0 ? $bundle->products->count() : 0 }};

            $(document).ready(function() {
                // CKEditor initialization
                $('.myEditor').each(function(index) {
                    var elementId = $(this).attr('id') || 'editor-' + index;
                    $(this).attr('id', elementId);
                    CKEDITOR.replace(elementId, {
                        width: '100%'
                    });
                });

                // Auto-generate slug from name
                let originalSlug = '{{ isset($bundle) ? $bundle->slug : "" }}';
                let slugManuallyEdited = false;
                
                $('#slug').on('input', function() {
                    slugManuallyEdited = true;
                });
                
                $('#name').on('keyup', function() {
                    if (!slugManuallyEdited) {
                        const nameValue = $(this).val();
                        if (nameValue) {
                            $('#slug').val(slugify(nameValue));
                        } else {
                            $('#slug').val('');
                        }
                    }
                });

                // Status switch handler
                $('#status').on('change', function() {
                    const isChecked = $(this).is(':checked');
                    $('#status-hidden').val(isChecked ? 1 : 0);
                    $('#status-label').text(isChecked ? 'Active' : 'Inactive');
                });

                // Update discount hint
                $('#discount_type').on('change', function() {
                    updateDiscountHint();
                });
                updateDiscountHint();

                // Add product
                $('#product_search').on('change', function() {
                    const productId = $(this).val();
                    if (productId) {
                        const option = $(this).find('option:selected');
                        addProduct(productId, option.data('name'), option.data('price'), option.data('image'));
                        $(this).val('');
                    }
                });

                // Remove product
                $(document).on('click', '.remove-product', function() {
                    $(this).closest('.product-item').remove();
                    updateProductIndexes();
                    calculateTotals();
                });

                // Update quantities and prices
                $(document).on('input', '.product-quantity, .product-price', function() {
                    updateSubtotal($(this).closest('.product-item'));
                    calculateTotals();
                });

                // Quantity increment/decrement buttons
                $(document).on('click', '.quantity-increase', function() {
                    const input = $(this).siblings('.product-quantity');
                    const currentVal = parseInt(input.val()) || 1;
                    input.val(currentVal + 1).trigger('input');
                });

                $(document).on('click', '.quantity-decrease', function() {
                    const input = $(this).siblings('.product-quantity');
                    const currentVal = parseInt(input.val()) || 1;
                    if (currentVal > 1) {
                        input.val(currentVal - 1).trigger('input');
                    }
                });

                // Calculate initial totals
                calculateTotals();

                // Form submission handler
                $('#bundle-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    // Sync CKEditor content before submission
                    $('.myEditor').each(function() {
                        var instance = CKEDITOR.instances[$(this).attr('id')];
                        if (instance) {
                            instance.updateElement();
                        }
                    });
                    
                    // Use the standard ajaxPost function
                    const formData = $(this).serialize();
                    const btn = $('#bundle-btn');
                    const btnText = btn.html();
                    
                    btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Processing...');
                    
                    $.ajax({
                        url: $(this).attr('action'),
                        method: $(this).attr('method'),
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                successMessage(response.success);
                                setTimeout(function() {
                                    window.location.href = "{{ route('admin.bundle.index') }}";
                                }, 1000);
                            } else {
                                errorMessage(response.message || 'An error occurred');
                                btn.prop('disabled', false).html(btnText);
                            }
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                let errorMsg = xhr.responseJSON.message || 'Validation errors occurred';
                                if (xhr.responseJSON.errors) {
                                    $.each(xhr.responseJSON.errors, function(key, value) {
                                        errorMsg += '\n' + value[0];
                                    });
                                }
                                errorMessage(errorMsg);
                            } else {
                                errorMessage(xhr.responseJSON?.message || 'An error occurred while saving the bundle.');
                            }
                            btn.prop('disabled', false).html(btnText);
                        }
                    });
                });
            });

            function slugify(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
            }

            function updateDiscountHint() {
                const type = $('#discount_type').val();
                const hint = type === 'percentage' 
                    ? 'Enter percentage (e.g., 20 for 20%)' 
                    : 'Enter fixed amount in PKR (e.g., 500)';
                $('#discount-hint').text(hint);
                calculateTotals();
            }

            function addProduct(productId, name, price, image) {
                // Check if product already exists
                if ($(`.product-item[data-product-id="${productId}"]`).length > 0) {
                    toastr.warning('Product already added to bundle');
                    return;
                }

                const html = `
                    <div class="product-item card mb-3" data-product-id="${productId}">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-1 text-center">
                                    <input type="hidden" name="products[${productIndex}][product_id]" value="${productId}">
                                    <input type="hidden" name="products[${productIndex}][sort_order]" value="${productIndex}" class="sort-order">
                                    <span class="badge bg-secondary d-inline-block mb-2">${productIndex + 1}</span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Product Name</label>
                                    <div class="form-control-plaintext"><strong class="text-white">${name}</strong></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Quantity</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-light btn-sm quantity-decrease" style="border-radius: 0.375rem 0 0 0.375rem;">-</button>
                                        <input type="number" name="products[${productIndex}][quantity]" 
                                            class="form-control product-quantity text-center" 
                                            value="1" min="1" required style="border-left: 0; border-right: 0;">
                                        <button type="button" class="btn btn-light btn-sm quantity-increase" style="border-radius: 0 0.375rem 0.375rem 0;">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Price (PKR) <span class="text-white">(Default: ${parseFloat(price).toFixed(2)})</span></label>
                                    <input type="number" name="products[${productIndex}][price]" 
                                        class="form-control product-price" 
                                        value="${price}" 
                                        step="0.01" min="0">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Subtotal</label>
                                    <input type="text" class="form-control product-subtotal" readonly 
                                        value="PKR ${parseFloat(price).toFixed(2)}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Action</label>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-light btn-sm remove-product d-flex align-items-center justify-content-center" title="Remove Product" style="width: 40px; height: 38px; padding: 0;">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#products-container').append(html);
                productIndex++;
                calculateTotals();
            }

            function updateProductIndexes() {
                $('.product-item').each(function(index) {
                    $(this).find('.badge').text(index + 1);
                    $(this).find('.sort-order').val(index);
                    // Update input names
                    $(this).find('input, select').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/products\[\d+\]/, `products[${index}]`));
                        }
                    });
                });
                productIndex = $('.product-item').length;
            }

            function updateSubtotal($item) {
                const quantity = parseFloat($item.find('.product-quantity').val()) || 0;
                const price = parseFloat($item.find('.product-price').val()) || 0;
                const subtotal = quantity * price;
                $item.find('.product-subtotal').val('PKR ' + subtotal.toFixed(2));
            }

            function calculateTotals() {
                let totalPrice = 0;
                $('.product-item').each(function() {
                    const quantity = parseFloat($(this).find('.product-quantity').val()) || 0;
                    const price = parseFloat($(this).find('.product-price').val()) || 0;
                    totalPrice += quantity * price;
                });

                $('#total-price').text('PKR ' + totalPrice.toFixed(2));

                const discountType = $('#discount_type').val();
                const discountValue = parseFloat($('#discount_value').val()) || 0;
                let bundlePrice = totalPrice;

                if (discountType === 'percentage') {
                    bundlePrice = totalPrice - (totalPrice * discountValue / 100);
                } else {
                    bundlePrice = Math.max(0, totalPrice - discountValue);
                }

                $('#bundle-price').text('PKR ' + bundlePrice.toFixed(2));
            }

            // Recalculate on discount change
            $('#discount_value').on('input', calculateTotals);
        </script>
    @endpush
@endsection

