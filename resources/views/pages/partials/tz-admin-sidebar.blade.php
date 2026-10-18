<nav class="cinematic-nav cinematic-subpage-nav cinematic-admin-nav" aria-label="Administrator Navigation">
    <a href="{{ route('furever.dashboard') }}" class="nav-logo" aria-label="TZ ATELIER Admin">
        <img src="{{ asset('img/tz-mark.svg') }}" alt="TZ Mark" class="tz-nav-logo-mark">
        <strong>TZ</strong><span style="letter-spacing:0.14em; font-weight:800; font-size:15px;"> ATELIER</span>
        <span class="nav-badge-pill">Atelier Desk</span>
    </a>

    <div class="nav-links">
        <a href="{{ route('furever.dashboard') }}" class="{{ request()->routeIs('furever.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('furever.products') }}" class="{{ request()->routeIs('furever.products*') ? 'active' : '' }}">Product </a>
        <a href="{{ route('furever.customers') }}" class="{{ request()->routeIs('furever.customers*') ? 'active' : '' }}">Customer </a>
        <a href="{{ route('furever.orders') }}" class="{{ request()->routeIs('furever.orders*') ? 'active' : '' }}"> Order </a>
        <a href="{{ route('furever') }}" target="_blank" title="Open Public Showroom">Home <i class="fa fa-external-link" style="font-size:10px; margin-left:4px;"></i></a>
    </div>

    <div class="nav-actions">
        <a href="{{ route('furever.products.create') }}" class="nav-cta-button" style="text-decoration:none;">
            <i class="fa fa-plus" aria-hidden="true"></i> Add Car
        </a>
        <form action="{{ route('furever.logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="nav-badge-link tz-nav-logout" style="background:none; border:1px solid rgba(255,255,255,0.1); cursor:pointer;">
                <i class="fa fa-sign-out" aria-hidden="true"></i>
                <span>Log out</span>
            </button>
        </form>
    </div>
</nav>
