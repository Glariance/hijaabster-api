
@if(isset($errors) && $errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @foreach ($errors->all() as $error)
                Lobibox.notify('error', {
                    pauseDelayOnHover: true,
                    size: 'mini',
                    icon: 'bx bx-x-circle',
                    continueDelayOnInactiveTab: false,
                    position: 'top center',
                    msg: "{{ $error }}"
                });
            @endforeach
        });
    </script>
@endif
@if (session('success'))
    <script>
        Lobibox.notify('success', {
            pauseDelayOnHover: true,
            size: 'mini',
            icon: 'bx bx-check-circle',
            continueDelayOnInactiveTab: false,
            position: 'top center',
            msg: '{{ session('success') }}'
        });
    </script>
@endif

@if (session('error'))
    <script>
        Lobibox.notify('error', {
            pauseDelayOnHover: true,
            size: 'mini',
            icon: 'bx bx-x-circle',
            continueDelayOnInactiveTab: false,
            position: 'top center',
            msg: '{{ session('error') }}'
        });
    </script>
@endif

@if (session('info'))
    <script>
        Lobibox.notify('info', {
            pauseDelayOnHover: true,
            size: 'mini',
            icon: 'bx bx-info-circle',
            continueDelayOnInactiveTab: false,
            position: 'top center',
            msg: '{{ session('info') }}'
        });
    </script>
@endif

@if (session('warning'))
    <script>
        Lobibox.notify('warning', {
            pauseDelayOnHover: true,
            size: 'mini',
            icon: 'bx bx-error',
            continueDelayOnInactiveTab: false,
            position: 'top center',
            msg: '{{ session('warning') }}'
        });
    </script>
@endif