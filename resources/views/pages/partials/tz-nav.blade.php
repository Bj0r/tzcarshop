@php($navigationCartCount = $cartCount ?? collect(session('cart', []))->sum('quantity'))

<nav class="cinematic-nav cinematic-subpage-nav" aria-label="TZ ATELIER Navigation">
    <a href="{{ route('furever') }}" class="nav-logo" aria-label="TZ ATELIER">
        <img src="{{ asset('img/tz-mark.svg') }}" alt="TZ Mark" class="tz-nav-logo-mark">
        <strong>TZ</strong><span style="letter-spacing:0.14em; font-weight:800; font-size:15px;"> ATELIER</span>
    </a>

    <div class="nav-links">
        <a href="{{ route('furever') }}" class="{{ request()->routeIs('furever') ? 'active' : '' }}">Exhibition</a>
        <a href="{{ route('furever.shop') }}" class="{{ request()->routeIs('furever.shop*') ? 'active' : '' }}">Collection</a>
        @auth
            @if(auth()->user()->isFureverAdmin())
                <a href="{{ route('furever.dashboard') }}" class="{{ request()->routeIs('furever.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('furever.products') }}" class="{{ request()->routeIs('furever.products*') ? 'active' : '' }}">Inventory</a>
                <a href="{{ route('furever.customers') }}" class="{{ request()->routeIs('furever.customers*') ? 'active' : '' }}">Clients</a>
                <a href="{{ route('furever.orders') }}" class="{{ request()->routeIs('furever.orders*') ? 'active' : '' }}">Orders</a>
            @else
                <a href="{{ route('furever.customer-orders') }}" class="{{ request()->routeIs('furever.customer-orders*') ? 'active' : '' }}">Commissions</a>
            @endif
        @endauth
    </div>

    <div class="nav-actions">
        <a href="{{ route('furever.shop') }}" class="nav-badge-link" title="Vehicle Inventory">
            <i class="fa fa-th-large" aria-hidden="true"></i>
            <span>Inventory</span>
        </a>

        @auth
            @if(auth()->user()->isFureverAdmin())
                <a href="{{ route('furever.dashboard') }}" class="nav-badge-link">Back to Dashboard</a>
            @else
                <a href="{{ route('furever.customer-orders') }}" class="nav-badge-link">Commissions</a>
                <a href="{{ route('furever.cart') }}" class="nav-badge-link" aria-label="View Cart">
                    <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                    <span class="nav-cart-count">{{ $navigationCartCount }}</span>
                </a>
            @endif
            <form action="{{ route('furever.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="nav-badge-link" style="background:none; border:1px solid rgba(255,255,255,0.1); cursor:pointer;">Exit</button>
            </form>
        @else
            <a href="{{ route('furever.login') }}" class="nav-badge-link">Sign in</a>
            <a href="{{ route('furever.register') }}" class="nav-cta-button" style="text-decoration:none;">Create account</a>
        @endauth
    </div>
</nav>
