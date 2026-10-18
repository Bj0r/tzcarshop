<footer class="cinematic-footer" aria-label="TZ ATELIER Footer">
    <div class="cinematic-footer-inner">
        <a href="{{ route('furever') }}" class="cinematic-footer-brand">
            <img src="{{ asset('img/tz-mark.svg') }}" alt="TZ Mark" class="tz-footer-logo-mark">
            <span>TZ ATELIER</span>
        </a>

        <nav class="cinematic-footer-links" aria-label="Footer navigation">
            <a href="{{ route('furever') }}">Exhibition</a>
            <a href="{{ route('furever.shop') }}">Collection</a>
            @auth
                @if(auth()->user()->isFureverAdmin())
                    <a href="{{ route('furever.dashboard') }}">Atelier Desk</a>
                    <a href="{{ route('furever.products') }}">Inventory</a>
                @else
                    <a href="{{ route('furever.customer-orders') }}">Commissions</a>
                    <a href="{{ route('furever.cart') }}">Acquisition Cart</a>
                @endif
            @else
                <a href="{{ route('furever.login') }}">Sign In</a>
                <a href="{{ route('furever.register') }}">Join Atelier</a>
            @endauth
        </nav>

        <span class="cinematic-footer-copy">&copy; {{ date('Y') }} TZ ATELIER. Haute Automotive Precision. All rights reserved.</span>
    </div>
</footer>
