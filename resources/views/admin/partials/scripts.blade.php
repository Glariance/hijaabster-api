<!-- Bootstrap JS -->
<script src="{{ asset('adminassets/js/bootstrap.bundle.min.js') }}"></script>
<!--plugins-->
<script src="{{ asset('adminassets/js/jquery.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('adminassets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/chartjs/js/Chart.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('adminassets/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/sparkline-charts/jquery.sparkline.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/jquery-knob/excanvas.js') }}"></script>
<script src="{{ asset('adminassets/plugins/jquery-knob/jquery.knob.js') }}"></script>


<!--notification js -->
<script src="{{ asset('adminassets/plugins/notifications/js/lobibox.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/notifications/js/notifications.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/notifications/js/notification-custom-script.js') }}"></script>


{{-- datatable --}}
<script src="{{ asset('adminassets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminassets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>


{{-- select2 --}}
<script src="{{ asset('adminassets/plugins/select2/js/select2.min.js') }}"></script>

{{-- sweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (request()->is('admin/dashboard'))
    {{-- UnComment When chart uses --}}
    {{-- <script src="{{ asset('adminassets/js/index.js') }}"></script> --}}
@endif

<!--app JS-->

<script src="{{ asset('adminassets/js/custom.js') }}"></script>

<script src="{{ asset('adminassets/js/app.js') }}"></script>


<script>
    $(function() {
        $(".knob").knob();


        // For CMS Page Form Submition
        ajaxPost("#cms-page-form", "#cms-page-btn", function(response) {
            successMessage(response.success);
            $('#custom-lg-modal').modal('hide');
            let slug = $('#page_slug').val(); // Get slug value from input
            let url = "{{ route('admin.cms.index', ['slug' => ':slug']) }}"; // Placeholder for slug
            url = url.replace(':slug', slug); // Replace placeholder with actual slug
            window.location.href = url;
        }, function(xhr) {
            // error code here
        });

        //  For Send Custom Mails
        ajaxPost('#custom-send-mail-form', '#custom-send-mail-btn', function(response) {
            successMessage(response.success);

            if (Array.isArray(response.failed) && response.failed.length > 0) {
                if (response.failed.length === 1) {
                    errorMessage(`Failed to send email to: ${response.failed[0]}`);
                } else {
                    errorMessage(
                        "Some emails were not sent due to invalid email addresses or issues with the email server."
                        );
                }
            }

            $('#custom-lg-modal').modal('hide');
        });



    });
</script>
