<div class="card-body">
    <div class="card-title d-flex align-items-center">
        <h5 class="mb-0 text-white">Profile Update</h5>
    </div>
    <hr>
    <form class="row g-3" id="profile-update-form" action="{{ route('admin.profile.post') }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="col-md-6">
            <label for="full-name" class="form-label">Full Name</label>
            <input type="text" name="name" value="{{ Auth::user()->name }}" class="form-control" id="full-name">
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" value="{{ Auth::user()->email }}" class="form-control" id="email"
                readonly>
        </div>
        <div class="col-md-10">
            <label class="form-label">Image</label>
            <div id="dropzone-area" class="dropzone"></div>
        </div>
        
        <div class="col-md-2 text-center">
            <img style="width: 100%;" class="mt-4" src="{{Auth::user()->image}}" alt="">
        </div>
        <div class="col-12">
            <button type="submit" id="btnProfileUpdate" class="btn btn-light px-5">Update</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        Dropzone.autoDiscover = false;
        let selectedFile = null; // Store the selected file

        let myDropzone = new Dropzone("#dropzone-area", {
            url: "#", // Prevent auto-upload
            maxFiles: 1,
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            autoProcessQueue: false,
            init: function() {
                this.on("addedfile", function(file) {
                    selectedFile = file; // Save the selected file
                });
                this.on("removedfile", function() {
                    selectedFile = null; // Remove file reference when deleted
                });
            }
        });
        window.getSelectedFile = function() {
            return selectedFile;
        }
    });
</script>
