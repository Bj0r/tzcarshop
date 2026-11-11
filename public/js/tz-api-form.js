/*
 * Forms marked data-api-form (login, register, logout) are sent as JSON to the
 * TZ Carshop API instead of a normal page submit. Used by the shared layout and
 * by the landing page (resources/views/pages/tz.blade.php).
 */
(function () {
    'use strict';

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
})();
