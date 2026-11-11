{{--
    Shared footer for customer and admin pages. The links follow the signed-in role
    and use the same names as the menus:
      Guest:    Shop (Home, Collection)  · Account (Sign in, Create account)
      Customer: Shop (Home, Collection)  · Account (Commissions, Cart)
      Admin:    Manage (Dashboard, Products, Customers, Orders) · Store (Home, Collection)
--}}
@php
    $footerUser = auth()->user();
    $footerIsAdmin = $footerUser && $footerUser->isFureverAdmin();
@endphp

<footer class="cinematic-footer" aria-label="Site footer">
    <div class="tz-footer">
        <div class="tz-footer__brand">
            <a href="{{ route($footerIsAdmin ? 'furever.dashboard' : 'furever') }}" class="cinematic-footer-brand">
                <img src="{{ asset('img/tz-mark.svg') }}" alt="TZ Mark" class="tz-footer-logo-mark">
                <span>TZ ATELIER</span>
            </a>
            <p>{{ $footerIsAdmin ? 'Admin desk for TZ Carshop products, customers, and orders.' : 'Toyota vehicles, curated by TZ Carshop.' }}</p>
        </div>

        @if($footerIsAdmin)
            <nav class="tz-footer__column" aria-label="Manage">
                <h2>Manage</h2>
                <a href="{{ route('furever.dashboard') }}">Dashboard</a>
                <a href="{{ route('furever.products') }}">Products</a>
                <a href="{{ route('furever.customers') }}">Customers</a>
                <a href="{{ route('furever.orders') }}">Orders</a>
            </nav>

            <nav class="tz-footer__column" aria-label="Store">
                <h2>Store</h2>
                <a href="{{ route('furever') }}">Home</a>
                <a href="{{ route('furever.shop') }}">Collection</a>
            </nav>
        @else
            <nav class="tz-footer__column" aria-label="Shop">
                <h2>Shop</h2>
                <a href="{{ route('furever') }}">Home</a>
                <a href="{{ route('furever.shop') }}">Collection</a>
            </nav>

            <nav class="tz-footer__column" aria-label="Account">
                <h2>Account</h2>
                @auth
                    <a href="{{ route('furever.customer-orders') }}">Commissions</a>
                    <a href="{{ route('furever.cart') }}">Cart</a>
                @else
                    <a href="{{ route('furever.login') }}">Sign in</a>
                    <a href="{{ route('furever.register') }}">Create account</a>
                @endauth
            </nav>
        @endif
    </div>

    <div class="tz-footer__bottom">
        &copy; {{ date('Y') }} TZ Carshop. All rights reserved.
    </div>
</footer>
