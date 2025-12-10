{{-- Larage Modal Display center top --}}

<div class="modal fade" id="custom-lg-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btnSuccessText">Save changes</button>
            </div> --}}
        </div>
    </div>
</div>
{{-- Large Modal End --}}




{{-- Image Preview Modal --}}
<div id="imageModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
            </div>
            <div class="modal-body text-center">
                {{-- <img id="modalImage" src="" class="img-fluid"> --}}
            </div>
        </div>
    </div>
</div>
<script>
    function openImageModal(imageSrc) {
        const $modal = $('#imageModal');
        $modal.find('.modal-title').html("Image Preview");
        $modal.find('.modal-body').html(`<img id="modalImage" src="${imageSrc}" class="img-fluid">`);
        $modal.modal('show');
    }
</script>
