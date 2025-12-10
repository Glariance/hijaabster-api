<div class="card-body">
    <div class="card-title d-flex align-items-center">
        <h5 class="mb-0 text-white">SMTP Settings</h5>
    </div>
    <hr>
    <form class="row g-3" id="smtp-settings-form" action="{{ route('admin.smtp.post') }}" method="POST">
        @csrf
        <div class="col-md-4">
            <label for="mail-driver" class="form-label">Mail Driver</label>
            <input type="text" class="form-control" id="mail-driver" name="mail_driver" value="{{isset($smtp->mail_driver) ? $smtp->mail_driver : ''}}">
        </div>
        <div class="col-md-4">
            <label for="mail-host" class="form-label">Mail Host</label>
            <input type="text" class="form-control" id="mail-host" name="mail_host" value="{{isset($smtp->mail_host) ? $smtp->mail_host : ''}}">
        </div>
        <div class="col-md-4">
            <label for="mail-port" class="form-label">Mail Port</label>
            <input type="number" class="form-control" id="mail-port" name="mail_port" value="{{isset($smtp->mail_port) ? $smtp->mail_port : ''}}">
        </div>
        <div class="col-md-6">
            <label for="mail-username" class="form-label">Mail Username</label>
            <input type="email" class="form-control" id="mail-username" name="mail_username" value="{{isset($smtp->mail_username) ? $smtp->mail_username : ''}}">
        </div>
        <div class="col-md-6">
            <label for="mail-password" class="form-label">Mail Password</label>
            <div class="input-group show_hide_password">
                <input type="password" class="form-control" id="mail-password" name="mail_password"
                    placeholder="Enter Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
            </div>
        </div>
        <div class="col-md-6">
            <label for="mail-encryption" class="form-label">Mail Encryption</label>
            {{-- <input type="text" class="form-control" id="mail-encryption" name="mail_encryption" value="{{isset($smtp->mail_encryption) ? $smtp->mail_encryption : ''}}"> --}}
            <select class="form-select mb-3" id="mail-encryption" name="mail_encryption">
                <option value="">Selec Any</option>
                <option value="SSL" @selected((isset($smtp->mail_encryption)? $smtp->mail_encryption == "SSL" : false))>SSL</option>
                <option value="TLS" @selected((isset($smtp->mail_encryption)? $smtp->mail_encryption == "TLS" : false))>TLS</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="mail-from-address" class="form-label">Mail From Address</label>
            <input type="email" class="form-control" id="mail-from-address" name="mail_from_address" value="{{isset($smtp->mail_from_address) ? $smtp->mail_from_address : ''}}">
        </div>
        <div class="col-12">
            <button type="submit" id="btnSmtp" class="btn btn-light px-5">Save</button>
        </div>
    </form>
</div>

