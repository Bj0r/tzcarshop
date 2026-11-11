{{-- Small helpers for calling the TZ Carshop JSON API from customer pages. --}}
<script>
    window.tzApi = async function (url, options = {}) {
        const response = await fetch(url, {
            method: options.method || 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': @json(csrf_token()),
            },
            body: options.body ? JSON.stringify(options.body) : undefined,
        });

        const data = await response.json().catch(function () { return {}; });

        if (response.status === 401) {
            window.location.href = @json(route('furever.login'));
            throw new Error('Please sign in first.');
        }

        if (!response.ok) {
            const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
            throw new Error(firstError || data.message || 'Something went wrong. Please try again.');
        }

        return data;
    };

    window.tzMoney = function (value) {
        return '₱' + Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    window.tzImage = function (path) {
        if (!path) {
            return null;
        }

        return /^https?:\/\//.test(path) ? path : @json(asset('storage')) + '/' + path;
    };

    window.tzEscape = function (value) {
        return String(value ?? '').replace(/[&<>"']/g, function (character) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character];
        });
    };

    window.tzFlash = function (element, message, isSuccess) {
        if (!element) {
            return;
        }

        element.hidden = false;
        element.style.borderColor = isSuccess ? 'rgba(45, 138, 94, 0.4)' : 'rgba(196, 77, 90, 0.4)';
        element.style.background = isSuccess ? 'rgba(45, 138, 94, 0.1)' : 'rgba(196, 77, 90, 0.1)';
        element.innerHTML = '<span style="color:' + (isSuccess ? '#4ade80' : '#f87171') + '; font-size: 13px; font-weight: 600;">'
            + '<i class="fa ' + (isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle') + '"></i> '
            + window.tzEscape(message) + '</span>';
    };

    window.tzSetCartCount = function (count) {
        document.querySelectorAll('.nav-cart-count').forEach(function (badge) {
            badge.textContent = count;
        });
    };
</script>
