<div class="card-body">
    <div class="card-title d-flex align-items-center">
        <h5 class="mb-0 text-white">Change Password</h5>
    </div>
    <hr>
    <form class="row g-3" id="password-change-form" action="{{ route('admin.changepassword.post') }}" method="POST">
        @csrf
        <div class="col-md-12">
            <label for="current-password" class="form-label">Current Password</label>
            <div class="input-group show_hide_password">
                <input type="password" class="form-control border-end-0" id="current-password" name="current_password" placeholder="Current Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
            </div>
        </div>
        <div class="col-md-6">
            <label for="password" class="form-label">New Password</label>
            <div class="input-group show_hide_password">
                <input type="password" class="form-control border-end-0" id="password" name="password" placeholder="New Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
            </div>
        </div>
        <div class="col-md-6">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group show_hide_password">
                <input type="password" class="form-control border-end-0" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" id="btnChangePassword" class="btn btn-light px-5">Save</button>
        </div>
    </form>
</div>