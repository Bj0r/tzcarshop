<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TZ Carshop — Home</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('img/tz-mark.svg') }}">
    <link rel="alternate icon" href="{{ asset('img/tz-mark.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/tz-mark.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="{{ asset('css/cinematic.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tz-home.css') }}">
</head>
<body>

    @php
        // The car in the hero film is a white GR86; show its live name and price from the database.
        $heroVehicle = $products->firstWhere('name', 'Toyota GR86');

        // Four cars for the "Choose Your Toyota" section (newest first, from TzController@index).
        $featuredVehicles = $products->take(4);

        $vehicleImage = fn ($vehicle) => $vehicle->image
            ? (filter_var($vehicle->image, FILTER_VALIDATE_URL) ? $vehicle->image : asset('storage/'.$vehicle->image))
            : null;

        $modelName = fn ($vehicle) => \Illuminate\Support\Str::after($vehicle->name, 'Toyota ');
    @endphp

    {{-- Shared navigation (same menu as the shop, cart, and orders pages). --}}
    @include('pages.partials.tz-nav', ['overlay' => true])

    <main>

        {{-- ==================================================================
             HERO — Scroll-driven film. Each scroll step draws the next video
             frame, so the car "drives" as the customer scrolls.
             Frames: public/img/hero-frames/{desktop,mobile}/frame_001..120.webp
             ================================================================== --}}
        <section class="scene-wrapper hero-scrub" id="hero"
                 data-hero-scrub
                 data-frame-count="120"
                 data-frames-desktop="{{ asset('img/hero-frames/desktop') }}"
                 data-frames-mobile="{{ asset('img/hero-frames/mobile') }}">
            <div class="scene hero-scrub__stage">
                {{-- Still image shown first, and kept for visitors who prefer reduced motion. --}}
                <img src="{{ asset('img/hero-frames/desktop/frame_001.webp') }}" alt="Toyota GR86 in a dark studio" class="hero-scrub__poster" data-hero-poster>
                <canvas class="hero-scrub__canvas" data-hero-canvas aria-hidden="true"></canvas>
                <div class="hero-scrub__shade" aria-hidden="true"></div>

                {{-- Opening text: fades out as the car comes to life. --}}
                <div class="scene-inner hero-scrub__intro" data-hero-intro>
                    <div class="scene-content">
                        <p class="scene-label">TZ Carshop <span>/</span> Toyota</p>
                        <h1 class="scene-heading">BEYOND MOTION</h1>
                        <p class="scene-sub">
                            Toyota performance, curated by TZ Carshop. Scroll to bring it to life.
                        </p>
                        <div class="hero-cue">
                            <span class="hero-cue-line" aria-hidden="true"></span>
                            <span>Scroll to start the engine</span>
                        </div>
                    </div>
                </div>

                {{-- Closing text: fades in when the full car is revealed. --}}
                @if($heroVehicle)
                    <div class="scene-inner hero-scrub__outro" data-hero-outro>
                        <div class="scene-content">
                            <p class="scene-label">{{ $heroVehicle->year }} <span>/</span> {{ $heroVehicle->transmission }} <span>/</span> {{ $heroVehicle->fuel_type }}</p>
                            <h2 class="scene-heading hero-scrub__name">{{ $heroVehicle->name }}</h2>
                            <div class="hero-scrub__buy">
                                <p class="hero-scrub__price">&#8369;{{ number_format($heroVehicle->price, 2) }}</p>
                                <a href="{{ route('furever.shop', ['search' => $modelName($heroVehicle)]) }}" class="cta-cinema-primary hero-scrub__cta">
                                    View in collection <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="hero-scrub__progress" aria-hidden="true"><span data-hero-progress></span></div>
            </div>
        </section>

        <div class="tz-landing">

            {{-- ==============================================================
                 SEARCH — sends the customer to the shop with the search filled in.
                 ============================================================== --}}
            <section class="tz-landing__section tz-landing__search">
                <div class="tz-landing__shell">
                    <span class="tz-landing__eyebrow">TZ Carshop</span>
                    <h2 class="tz-landing__title">Find Your Perfect Ride</h2>
                    <form action="{{ route('furever.shop') }}" method="GET" class="tz-landing__search-form" role="search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input type="search" name="search" placeholder="Search model, color, or fuel type..." aria-label="Search cars">
                        <button type="submit" class="cta-cinema-primary">Search</button>
                    </form>
                </div>
            </section>

            {{-- ==============================================================
                 FEATURED — four cars straight from the products table.
                 ============================================================== --}}
            <section class="tz-landing__section">
                <div class="tz-landing__shell">
                    <div class="tz-landing__heading-row">
                        <div>
                            <span class="tz-landing__eyebrow">Drive Your Next Chapter</span>
                            <h2 class="tz-landing__title">Choose Your Toyota</h2>
                        </div>
                        <a href="{{ route('furever.shop') }}" class="tz-landing__link">View the full collection <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                    </div>

                    <div class="tz-landing__cars">
                        @forelse($featuredVehicles as $vehicle)
                            <article class="tz-landing-car">
                                <div class="tz-landing-car__media">
                                    @if($vehicleImage($vehicle))
                                        <img src="{{ $vehicleImage($vehicle) }}" alt="{{ $vehicle->name }}" loading="lazy">
                                    @else
                                        <i class="fa fa-car" aria-hidden="true"></i>
                                    @endif
                                </div>
                                <div class="tz-landing-car__body">
                                    <h3>{{ $vehicle->name }}</h3>
                                    <p>{{ $vehicle->color }} &middot; {{ $vehicle->fuel_type }}</p>
                                    <strong>&#8369;{{ number_format($vehicle->price, 2) }}</strong>
                                    <a href="{{ route('furever.shop', ['search' => $modelName($vehicle)]) }}" class="tz-landing__link">View car <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                                </div>
                            </article>
                        @empty
                            <p class="tz-landing__muted">New cars are on the way. Check back soon.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- ==============================================================
                 WHY US + HOW IT WORKS
                 ============================================================== --}}
            <section class="tz-landing__section">
                <div class="tz-landing__shell tz-landing__split">
                    <div>
                        <h2 class="tz-landing__title">Why Choose TZ Carshop</h2>
                        <ul class="tz-landing__list">
                            <li><i class="fa fa-database" aria-hidden="true"></i><div><strong>Real stock</strong><span>Every car and price comes live from our inventory.</span></div></li>
                            <li><i class="fa fa-lock" aria-hidden="true"></i><div><strong>Secure checkout</strong><span>Your order is saved safely and the car is reserved for you.</span></div></li>
                            <li><i class="fa fa-map-marker" aria-hidden="true"></i><div><strong>Track every order</strong><span>Follow it from Pending to Ready for collection.</span></div></li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="tz-landing__title">A Simple Process</h2>
                        <ol class="tz-landing__steps">
                            <li><span>1</span><div><strong>Browse</strong><span>Search and compare Toyota models.</span></div></li>
                            <li><span>2</span><div><strong>Add to cart</strong><span>Pick your car and quantity.</span></div></li>
                            <li><span>3</span><div><strong>Place your order</strong><span>Check out and get a confirmation email.</span></div></li>
                        </ol>
                    </div>
                </div>
            </section>

            {{-- ==============================================================
                 CLOSING CALL TO ACTION
                 ============================================================== --}}
            <section class="tz-landing__section tz-landing__cta">
                <div class="tz-landing__shell">
                    <h2 class="tz-landing__title">Your Next Adventure Is Waiting</h2>
                    <div class="tz-landing__cta-actions">
                        <a href="{{ route('furever.shop') }}" class="cta-cinema-primary">Shop now</a>
                        @guest
                            <a href="{{ route('furever.register') }}" class="cta-cinema-ghost">Create an account</a>
                        @endguest
                        @auth
                            @if(auth()->user()->isFureverCustomer())
                                <a href="{{ route('furever.customer-orders') }}" class="cta-cinema-ghost">My orders</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </section>

        </div>
    </main>

    @include('pages.partials.tz-footer')

    <script src="{{ asset('js/hero-scrub.js') }}"></script>
    <script src="{{ asset('js/tz-api-form.js') }}"></script>
</body>
</html>
