@extends('admin.layouts.app')
@section('title', env('APP_NAME') . ' | Settings')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Settings</div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <!-- Vertical Nav Tabs -->
                        <div class="col-lg-3">
                            <ul class="nav nav-tabs flex-column me-3 p-4 border-0" role="tablist">
                                <li class="nav-item mb-3" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab"
                                        onclick="loadPage(`{{ route('admin.profile.page') }}`, '#admin-profile')"
                                        href="#admin-profile" role="tab" aria-selected="true">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-icon"><i
                                                    class="fadeIn animated bx bx-edit-alt font-18 me-1"></i></div>
                                            <div class="tab-title">Admin Profile</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item mb-3" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#change-password" role="tab"
                                        onclick="loadPage(`{{ route('admin.changepassword.page') }}`, '#change-password')"
                                        aria-selected="true">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-icon"><i class="fadeIn animated me-1 font-18 bx bx-lock"></i>
                                            </div>
                                            <div class="tab-title">Change Password</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item mb-3" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#smtp-settings" role="tab"
                                        onclick="loadPage(`{{ route('admin.smtp.page') }}`, '#smtp-settings')"
                                        aria-selected="false">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-icon"><i class="bx bx-user-pin font-18 me-1"></i></div>
                                            <div class="tab-title">SMTP Setting</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item mb-3" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#general-settings" role="tab"
                                        onclick="loadPage(`{{ route('admin.general.page') }}`, '#general-settings')"
                                        aria-selected="false">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-icon"><i class="bx bx-microphone font-18 me-1"></i>
                                            </div>
                                            <div class="tab-title">General Setting</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- Tab Content -->
                        <div class="col-lg-9 sp-center">
                            <div class="tab-content w-100 ">
                                <div class="tab-pane fade" id="admin-profile" role="tabpanel">

                                </div>
                                <div class="tab-pane fade" id="change-password" role="tabpanel">

                                </div>
                                <div class="tab-pane fade" id="smtp-settings" role="tabpanel">

                                </div>
                                <div class="tab-pane fade" id="general-settings" role="tabpanel">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                // Function to load the page content
                window.loadTabContent = function(tabId) {
                    let route = "";
                    switch (tabId) {
                        case "#admin-profile":
                            route = `{{ route('admin.profile.page') }}`;
                            break;
                        case "#change-password":
                            route = `{{ route('admin.changepassword.page') }}`;
                            break;
                        case "#smtp-settings":
                            route = `{{ route('admin.smtp.page') }}`;
                            break;
                        case "#general-settings":
                            route = `{{ route('admin.general.page') }}`;
                            break;
                    }

                    if (route) {
                        loadPage(route, tabId);
                    }
                }

                // Retrieve active tab from localStorage
                let activeTab = localStorage.getItem("activeTab");

                if (activeTab) {
                    $('.nav-tabs a[href="' + activeTab + '"]').tab('show'); // Show the stored tab
                    loadTabContent(activeTab); // Load the corresponding page
                } else {
                    $('.nav-tabs a:first').tab('show'); // Default to the first tab
                    loadPage(`{{ route('admin.profile.page') }}`, '#admin-profile');
                }

                // On tab click, store the active tab in localStorage
                $('.nav-tabs a').on('click', function(e) {
                    let tabId = $(this).attr('href');
                    localStorage.setItem("activeTab", tabId);
                });

                ajaxPost("#smtp-settings-form", "#btnSmtp", function(response) {
                    successMessage(response.success);
                    $('#mail-password').val('');
                }, function(xhr) {
                    // error code here
                });
                ajaxPost("#password-change-form", "#btnChangePassword", function(response) {
                    successMessage(response.success);
                    $('#current-password').val('');
                    $('#password').val('');
                    $('#password_confirmation').val('');
                }, function(xhr) {
                    // error code here
                });
                ajaxPost("#profile-update-form", "#btnProfileUpdate", function(response) {
                    // alert("hello");
                    loadTabContent('#admin-profile');
                    successMessage(response.success);
                }, function(xhr) {
                    // error code here
                });
                ajaxPost("#general-setting-form", "#btnProfileUpdate", function(response) {
                    // alert("hello");
                    loadTabContent('#general-settings');
                    successMessage(response.success);
                }, function(xhr) {
                    // error code here
                });
            })
        </script>
    @endpush
@endsection
