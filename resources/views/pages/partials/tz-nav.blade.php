{{--
    Main customer navigation, shared by the home page and the shop, cart, and orders pages.
    Pass ['overlay' => true] on the home page so the bar floats over the hero film.

    Pages (center):  Home · Collection · Commissions (customers only)
    Account (right): cart + Logout for customers, Back to Dashboard + Logout for admins,
                     Sign in + Create account for guests.
    On screens 960px and narrower the pages move into the ☰ menu.
--}}
@php
    $navigationCartCount = $cartCount ?? collect(session('cart', []))->sum('quantity');
    $navigationUser = auth()->user();
    $navigationIsCustomer = $navigationUser && $navigationUser->isFureverCustomer();
@endphp

<nav class="cinematic-nav {{ ($overlay ?? false) ? '' : 'cinematic-subpage-nav' }}" aria-label="Main navigation" data-nav>
    <a href="{{ route('furever') }}" class="nav-logo" aria-label="TZ ATELIER home">
        <img src="{{ asset('img/tz-mark.svg') }}" alt="TZ Mark" class="tz-nav-logo-mark">
        <strong>TZ</strong><span class="nav-hide-mobile" style="letter-spacing:0.14em; font-weight:800; font-size:15px;"> ATELIER</span>
    </a>

    <div class="nav-links" id="main-nav-pages">
        <a href="{{ route('furever') }}" class="{{ request()->routeIs('furever') ? 'active' : '' }}">Home</a>
        <a href="{{ route('furever.shop') }}" class="{{ request()->routeIs('furever.shop*') ? 'active' : '' }}">Collection</a>
        @if($navigationIsCustomer)
            <a href="{{ route('furever.customer-orders') }}" class="{{ request()->routeIs('furever.customer-orders*') ? 'active' : '' }}">Commissions</a>
        @endif
    </div>

    <div class="nav-actions">
        @auth
            @if($navigationIsCustomer)
                <a href="{{ route('furever.cart') }}" class="nav-badge-link" aria-label="View cart">
                    <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                    <span class="nav-cart-count">{{ $navigationCartCount }}</span>
                </a>
            @else
                <a href="{{ route('furever.dashboard') }}" class="nav-badge-link">Back to Dashboard</a>
            @endif
            <form action="{{ route('api.tz.logout') }}" method="POST" data-api-form style="display:inline;">
                @csrf
                <button type="submit" class="nav-badge-link" style="cursor:pointer;">Logout</button>
            </form>
        @else
            <a href="{{ route('furever.login') }}" class="nav-badge-link">Sign in</a>
            <a href="{{ route('furever.register') }}" class="nav-cta-button">Create account</a>
        @endauth

        <button type="button" class="nav-menu-toggle" aria-label="Open menu" aria-controls="main-nav-pages" aria-expanded="false" data-nav-toggle>
            <i class="fa fa-bars" aria-hidden="true"></i>
        </button>
    </div>
</nav>

<script>
    // ☰ button: show or hide the page links on small screens.
    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-nav-toggle]');

        if (!toggle) {
            return;
        }

        var isOpen = toggle.closest('[data-nav]').classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
    });
</script>
