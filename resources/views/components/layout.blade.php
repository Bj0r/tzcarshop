<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'TZ ATELIER — Luxury Automotive Atelier' }}</title>

    <!-- Web Logo / Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/tz-mark.svg') }}">
    <link rel="alternate icon" href="{{ asset('img/tz-mark.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/tz-mark.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/tz-reference.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tz-boutique.css') }}" rel="stylesheet">
    <link href="{{ asset('css/cinematic.css') }}" rel="stylesheet">
    @stack('styles')
    <link href="{{ asset('css/tz-palette.css') }}" rel="stylesheet">
</head>
<body class="cinematic-body">
    {{ $slot }}
    <script>
        document.addEventListener('click', function (event) {
            var toggle = event.target.closest('[data-password-toggle]');

            if (!toggle) {
                return;
            }

            var passwordInput = document.getElementById(toggle.getAttribute('aria-controls'));

            if (!passwordInput) {
                return;
            }

            var isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', String(isHidden));
            toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            toggle.querySelector('.fa').classList.toggle('fa-eye', !isHidden);
            toggle.querySelector('.fa').classList.toggle('fa-eye-slash', isHidden);
        });
    </script>
</body>
</html>
