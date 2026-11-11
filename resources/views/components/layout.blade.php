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

        // Forms marked data-api-form (login, register, logout) are sent as JSON to the API.
        document.addEventListener('submit', async function (event) {
            var form = event.target.closest('form[data-api-form]');

            if (!form) {
                return;
            }

            event.preventDefault();

            var button = form.querySelector('[type="submit"]');
            var data = Object.fromEntries(new FormData(form));

            if (button) {
                button.disabled = true;
            }

            form.querySelectorAll('[data-api-error]').forEach(function (element) {
                element.textContent = '';
                element.hidden = true;
            });

            try {
                var response = await fetch(form.action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': data._token || '',
                    },
                    body: JSON.stringify(data),
                });
                var json = await response.json().catch(function () { return {}; });

                if (response.ok) {
                    window.location.href = json.redirect || window.location.href;
                    return;
                }

                // Expired session or CSRF token: reload to get a fresh page.
                if (response.status === 401 || response.status === 419) {
                    window.location.reload();
                    return;
                }

                var errors = json.errors || {};
                var shown = false;

                Object.keys(errors).forEach(function (field) {
                    var element = form.querySelector('[data-api-error="' + field + '"]');

                    if (element) {
                        element.textContent = errors[field][0];
                        element.hidden = false;
                        shown = true;
                    }
                });

                if (!shown) {
                    var general = form.querySelector('[data-api-error="general"]');
                    var message = json.message || 'Something went wrong. Please try again.';

                    if (general) {
                        general.textContent = message;
                        general.hidden = false;
                    } else {
                        alert(message);
                    }
                }
            } catch (error) {
                alert('Network error. Please try again.');
            }

            if (button) {
                button.disabled = false;
            }
        });
    </script>
</body>
</html>
