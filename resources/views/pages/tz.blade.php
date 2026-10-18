<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TZ ATELIER — Cinematic Automotive Exhibition</title>

    <!-- Web Logo / Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/tz-mark.svg') }}">
    <link rel="alternate icon" href="{{ asset('img/tz-mark.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/tz-mark.svg') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Cinematic UI Design System -->
    <link rel="stylesheet" href="{{ asset('css/cinematic.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tz-home.css') }}">
</head>
<body>

    @php
        $navCartCount = $cartCount ?? collect(session('cart', []))->sum('quantity');
        $primaryVehicle = $products->where('name', 'Toyota GR Supra')->first() 
            ?? $products->where('fuel_type', 'Hybrid')->first() 
            ?? $products->first();

        $primaryImage = $primaryVehicle && $primaryVehicle->image
            ? (filter_var($primaryVehicle->image, FILTER_VALIDATE_URL) ? $primaryVehicle->image : asset('storage/'.$primaryVehicle->image))
            : 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1600&q=88';

        $secondaryImage = 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1600&q=88';
        $detailImage = 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=1600&q=88';
    @endphp

    <!-- ======================================================================
         MINIMAL CINEMATIC NAVIGATION
         ====================================================================== -->
    <nav class="cinematic-nav" aria-label="Cinematic Exhibition Navigation">
        <a href="#scene-1" class="nav-logo" aria-label="TZ ATELIER">
            <img src="{{ asset('img/tz-mark.svg') }}" alt="TZ Mark" class="tz-nav-logo-mark">
            <strong>TZ</strong><span style="letter-spacing:0.14em; font-weight:800;"> ATELIER</span>
        </a>

        <div class="nav-links">
            <a href="#scene-1">Establish</a>
            <a href="#scene-2">Exterior</a>
            <a href="#scene-3">Detail</a>
            <a href="#scene-4">Cockpit</a>
            <a href="#scene-5">Performance</a>
            <a href="#scene-6">Tech</a>
            <a href="#scene-7">Finishes</a>
            <a href="#scene-8">Specs</a>
            <a href="#scene-9">Commission</a>
        </div>

        <div class="nav-actions">
            <a href="{{ route('furever.shop') }}" class="nav-badge-link" title="Full Inventory Catalog">
                <i class="fa fa-th-large" aria-hidden="true"></i>
                <span>Inventory</span>
            </a>

            @auth
                @if(auth()->user()->isFureverAdmin())
                    <a href="{{ route('furever.dashboard') }}" class="nav-badge-link">Back to Dashboard</a>
                    <a href="{{ route('furever.dashboard') }}" class="nav-badge-link">Admin</a>
                @else
                    <a href="{{ route('furever.customer-orders') }}" class="nav-badge-link">Commissions</a>
                    <a href="{{ route('furever.cart') }}" class="nav-badge-link" aria-label="View Cart">
                        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                        <span class="nav-cart-count">{{ $navCartCount }}</span>
                    </a>
                @endif
                <form action="{{ route('furever.logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="nav-badge-link" style="background:none; border:none; cursor:pointer;">Exit</button>
                </form>
            @else
                <a href="{{ route('furever.login') }}" class="nav-badge-link">Sign in</a>
                <a href="{{ route('furever.register') }}" class="nav-badge-link">Create account</a>
            @endauth

            <button type="button" class="nav-cta-button" data-open-reservation data-car-id="{{ $primaryVehicle?->id }}">
                Reserve
            </button>
        </div>
    </nav>

    <main id="cinematic-viewport">

        <!-- ==================================================================
             SCENE 1: ESTABLISHING — Full-bleed hero. The vehicle emerges.
             ================================================================== -->
        <section class="scene-wrapper" id="scene-1">
            <div class="scene">
                <div class="scene-number">SCENE 01 / ESTABLISHING</div>
                
                <!-- Photographic Background Layer -->
                <div class="scene-bg" data-parallax="0.15">
                    <img src="{{ $primaryImage }}" alt="{{ $primaryVehicle?->name ?? 'Vehicle' }}" class="scene-bg-img">
                </div>

                <!-- Lighting Layers -->
                <div class="scene-lighting scene-light-key"></div>
                <div class="scene-lighting scene-light-rim"></div>
                <div class="scene-lighting scene-ambient"></div>
                <div class="scene-overlay"></div>

                <!-- Parallax Mid-Subject Layer -->
                <div class="hero-vehicle-layer" data-parallax="0.35">
                    <img src="{{ $primaryImage }}" alt="" class="hero-vehicle-silhouette" data-colour-preview-image>
                </div>

                <!-- Anchored Text Content at Lower Third -->
                <div class="scene-inner">
                    <div class="scene-content">
                        <p class="scene-label">01 <span>/</span> ESTABLISHING</p>
                        <h1 class="scene-heading">BEYOND MOTION</h1>
                        <p class="scene-sub">
                            A visceral manifestation of aerodynamic discipline and unyielding electric potency. The premier commission has arrived.
                        </p>
                        <div class="hero-cue">
                            <span class="hero-cue-line" aria-hidden="true"></span>
                            <span>Scroll To Engage Director's Cut</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             SCENE 2: EXTERIOR — Beauty shot. Aerodynamic form language.
             ================================================================== -->
        <section class="scene-wrapper" id="scene-2">
            <div class="scene">
                <div class="scene-number">SCENE 02 / EXTERIOR</div>
                
                <div class="scene-bg" data-parallax="0.2">
                    <img src="{{ $secondaryImage }}" alt="Aerodynamic Silhouette" class="scene-bg-img">
                </div>

                <div class="scene-lighting scene-light-exterior"></div>
                <div class="scene-lighting scene-light-rim"></div>
                <div class="scene-overlay"></div>

                <div class="scene-inner">
                    <div class="scene-content">
                        <p class="scene-label">02 <span>/</span> EXTERIOR</p>
                        <h2 class="scene-heading">PURE FORM</h2>
                        <p class="scene-sub">
                            Sculpted by aerodynamic necessity, refined to imperceptible tolerances of air and light. Every crease directs pressure; every intake feeds cooling radiators.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             SCENE 3: DETAIL — Material close-up. One word. Texture.
             ================================================================== -->
        <section class="scene-wrapper" id="scene-3">
            <div class="scene">
                <div class="scene-number">SCENE 03 / DETAIL</div>
                
                <div class="scene-bg" data-parallax="0.25">
                    <img src="{{ $detailImage }}" alt="Tactile Material Close-Up" class="scene-bg-img-macro">
                </div>

                <div class="scene-lighting scene-light-detail"></div>
                <div class="scene-lighting scene-ambient"></div>
                <div class="scene-overlay"></div>

                <div class="scene-inner">
                    <div class="scene-content">
                        <p class="scene-label">03 <span>/</span> DETAIL</p>
                        <h2 class="scene-heading">OBSESSION</h2>
                        <p class="scene-sub">
                            Cold forged anodized titanium meets double-stitched aniline hide. Every tactile surface is calibrated to a tolerance of microns.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             SCENE 4: INTERIOR — Cockpit. HUD display. The driver's world.
             ================================================================== -->
        <section class="scene-wrapper" id="scene-4" style="height: 200vh;">
            <div class="scene">
                <div class="scene-number">SCENE 04 / COCKPIT</div>

                <div class="scene-bg"></div>
                <div class="scene-lighting scene-light-cockpit"></div>
                <div class="scene-lighting scene-ambient"></div>

                <div class="scene-inner scene-inner--center">
                    <div class="scene-content" style="width: 100%; max-width: 1080px;">
                        <p class="scene-label">04 <span>/</span> INTERIOR</p>
                        <h2 class="scene-heading" style="font-size: clamp(2.5rem, 5.5vw, 5.5rem);">THE SANCTUARY</h2>
                        <p class="scene-sub scene-sub--center">
                            A focused sensory flight deck calibrated to the driver's neural intent.
                        </p>

                        <!-- Signature Element 7: Interior Cockpit HUD Display -->
                        <div class="cockpit-container" aria-label="Automotive HUD Instrument Cluster">
                            <div class="cockpit-glow" aria-hidden="true"></div>

                            <div class="cockpit-screen">
                                <!-- Left Panel: Range & Power -->
                                <div class="hud-panel hud-panel--left">
                                    <div>
                                        <span class="hud-metric-label">DYNAMIC RANGE</span>
                                        <div class="hud-metric-value">740 <small style="font-size:11px; color:var(--accent-gold);">KM</small></div>
                                        <div class="hud-bar-container">
                                            <div class="hud-bar-active" style="width: 94%;"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="hud-metric-label">POWER RESERVE</span>
                                        <div class="hud-metric-value">98 <small style="font-size:11px; color:var(--accent-blue-ice);">%</small></div>
                                        <div class="hud-bar-container">
                                            <div class="hud-bar-active" style="width: 98%; background: var(--accent-gold);"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="hud-metric-label">VECTORING</span>
                                        <div style="font-size: 11px; color: var(--accent-blue-ice); font-weight:700; margin-top:4px;">DUAL AWD · TORQUE ACTIVE</div>
                                    </div>
                                </div>

                                <!-- Center Panel: Speed Readout & Arc -->
                                <div class="hud-panel hud-panel--center">
                                    <div class="hud-speed-wrap">
                                        <div class="hud-speed">128</div>
                                        <div class="hud-speed-unit">KM / H · GROUND VELOCITY</div>
                                        <div class="hud-gear">
                                            <span>P</span>
                                            <span>R</span>
                                            <span>N</span>
                                            <span class="active">D · SPORT</span>
                                        </div>
                                        <div class="hud-arc" aria-hidden="true"></div>
                                    </div>
                                </div>

                                <!-- Right Panel: Telemetry & Status -->
                                <div class="hud-panel hud-panel--right">
                                    <div>
                                        <span class="hud-metric-label">CABIN CLIMATE</span>
                                        <div class="hud-metric-value">21.5° <small style="font-size:11px; color:var(--dim-white);">C</small></div>
                                    </div>

                                    <div>
                                        <span class="hud-metric-label">TELEMETRY GUIDANCE</span>
                                        <div style="font-size: 12px; color: #fff; font-weight: 700; margin-top: 4px;">APEX TRACK LOCK</div>
                                        <div style="font-size: 10px; color: var(--whisper); margin-top: 2px;">ZERO LATENCY DAMPING</div>
                                    </div>

                                    <div>
                                        <span class="hud-metric-label">MOTOR RPM</span>
                                        <div class="hud-metric-value" style="font-size: 16px; color: var(--accent-gold);">18,400 <small style="font-size: 9px;">RPM</small></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Steering Wheel Silhouette -->
                            <div class="cockpit-wheel-silhouette" aria-hidden="true">
                                <div class="cockpit-wheel-center">
                                    <div class="cockpit-wheel-emblem"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             SCENE 5: PERFORMANCE — Numbers. Range. Speed. Charge time.
             ================================================================== -->
        <section class="scene-wrapper scene-wrapper--stat" id="scene-5">
            <div class="scene">
                <div class="scene-number">SCENE 05 / PERFORMANCE</div>

                <div class="scene-bg"></div>
                <div class="scene-lighting scene-light-performance"></div>
                <div class="scene-lighting scene-light-rim"></div>

                <div class="scene-inner scene-inner--center">
                    <div class="stat-scene">
                        <p class="scene-label">05 <span>/</span> PERFORMANCE</p>
                        <h2 class="scene-heading" style="font-size: clamp(2.5rem, 5.5vw, 5.5rem);">RAW DYNAMICS</h2>
                        
                        <!-- Signature Element 5: Dramatic Statistics Display -->
                        <div class="stat-grid">
                            <div class="stat-card">
                                <div class="stat-number">2.8<span class="stat-unit">S</span></div>
                                <div class="stat-label">0–100 KM/H ACCELERATION</div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-number">740<span class="stat-unit">KM</span></div>
                                <div class="stat-label">DUAL-MOTOR DRIVING RANGE</div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-number">15<span class="stat-unit">MIN</span></div>
                                <div class="stat-label">10% TO 80% ULTRA-CHARGE</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             SCENE 6: TECHNOLOGY — 2×2 technology grid. Engineering claims.
             ================================================================== -->
        <section class="scene-wrapper" id="scene-6" style="height: 190vh;">
            <div class="scene">
                <div class="scene-number">SCENE 06 / TECHNOLOGY</div>

                <div class="scene-bg"></div>
                <div class="scene-lighting scene-light-key"></div>
                <div class="scene-lighting scene-ambient"></div>

                <div class="scene-inner scene-inner--center">
                    <div class="scene-content" style="width: 100%; max-width: 1060px;">
                        <p class="scene-label">06 <span>/</span> TECHNOLOGY</p>
                        <h2 class="scene-heading" style="font-size: clamp(2.5rem, 5vw, 5.2rem);">ENGINEERING CLAIMS</h2>

                        <!-- Signature Element 8: 2x2 Technology Grid -->
                        <div class="tech-grid" aria-label="Technology Specification Grid">
                            <div class="tech-cell">
                                <div>
                                    <span class="tech-cell-label">ARCHITECTURE / POWER</span>
                                    <h3 class="tech-cell-head">Silicon Carbide Inverter</h3>
                                    <p class="tech-cell-desc">Ultra-dense semiconductor switching operating at 800 volts with virtually zero resistance dissipation.</p>
                                </div>
                                <div class="tech-cell-accent">99.2% TRANSMISSION EFFICIENCY</div>
                            </div>

                            <div class="tech-cell">
                                <div>
                                    <span class="tech-cell-label">POWERTRAIN / VECTORING</span>
                                    <h3 class="tech-cell-head">Dual Permanent Magnet Motors</h3>
                                    <p class="tech-cell-desc">Independent axle-coupled synchronous permanent magnet units outputting instant millisecond torque.</p>
                                </div>
                                <div class="tech-cell-accent">840 HP INSTANT TORQUE OUTPUT</div>
                            </div>

                            <div class="tech-cell">
                                <div>
                                    <span class="tech-cell-label">DYNAMICS / CHASSIS</span>
                                    <h3 class="tech-cell-head">Active Aerodynamic Dampers</h3>
                                    <p class="tech-cell-desc">Magnetorheological fluid chambers adjusting individual corner compression instantaneously against lateral forces.</p>
                                </div>
                                <div class="tech-cell-accent">1,000 CALCULATIONS PER SECOND</div>
                            </div>

                            <div class="tech-cell">
                                <div>
                                    <span class="tech-cell-label">INTELLIGENCE / SENSING</span>
                                    <h3 class="tech-cell-head">Neural Pilot Co-Processor</h3>
                                    <p class="tech-cell-desc">High-throughput sensor fusion marrying 360° photonic lidar, high-gain optical arrays, and predictive road mapping.</p>
                                </div>
                                <div class="tech-cell-accent">360° PHOTONIC PERCEPTION MATRIX</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             SCENE 7: COLOUR — Horizontally scrollable finish selector.
             ================================================================== -->
        <section class="scene-wrapper" id="scene-7" style="height: 190vh;">
            <div class="scene">
                <div class="scene-number">SCENE 07 / COLOUR</div>

                <div class="scene-bg"></div>
                <div class="scene-lighting scene-light-detail"></div>

                <div class="scene-inner">
                    <div class="scene-content" style="width: 100%;">
                        <p class="scene-label">07 <span>/</span> COLOUR</p>
                        <h2 class="scene-heading" style="font-size: clamp(2.5rem, 5.5vw, 5.5rem);">ATELIER FINISHES</h2>
                        <p class="scene-sub">
                            Six bespoke finishes rendered entirely in pure pigment layers and hand-buffed protective clear coats. Currently showing: <strong id="selected-finish-display" data-selected-finish-name style="color: #fff;">Void Black</strong> (<span data-selected-finish-code style="color: var(--accent-gold);">VB-01</span>).
                        </p>

                        <!-- Signature Element 9: Horizontal Colour Selector -->
                        <div class="colour-selector-container">
                            <div class="colour-scroll" role="listbox" aria-label="Vehicle Exterior Finish Strip">
                                <!-- 1. Void Black -->
                                <button type="button" class="colour-card is-active" data-colour-card data-finish-name="Void Black" data-finish-code="VB-01" data-finish-filter="contrast(1.2) brightness(0.7)">
                                    <div class="colour-swatch-box swatch-void-black"></div>
                                    <div class="colour-card-body">
                                        <span class="colour-card-tag">Signature</span>
                                        <div class="colour-card-name">Void Black</div>
                                        <div class="colour-card-code">Matte Finish · VB-01</div>
                                    </div>
                                </button>

                                <!-- 2. Glacier Silver -->
                                <button type="button" class="colour-card" data-colour-card data-finish-name="Glacier Silver" data-finish-code="GS-08" data-finish-filter="contrast(1.1) brightness(1.15)">
                                    <div class="colour-swatch-box swatch-glacier-silver"></div>
                                    <div class="colour-card-body">
                                        <span class="colour-card-tag">Most popular</span>
                                        <div class="colour-card-name">Glacier Silver</div>
                                        <div class="colour-card-code">Polished Mirror · GS-08</div>
                                    </div>
                                </button>

                                <!-- 3. Arctic Blue -->
                                <button type="button" class="colour-card" data-colour-card data-finish-name="Arctic Blue" data-finish-code="AB-14" data-finish-filter="hue-rotate(190deg) contrast(1.1)">
                                    <div class="colour-swatch-box swatch-arctic-blue"></div>
                                    <div class="colour-card-body">
                                        <span class="colour-card-tag">New</span>
                                        <div class="colour-card-name">Arctic Blue</div>
                                        <div class="colour-card-code">Cold Metallic · AB-14</div>
                                    </div>
                                </button>

                                <!-- 4. Cognac Bronze -->
                                <button type="button" class="colour-card" data-colour-card data-finish-name="Cognac Bronze" data-finish-code="CB-22" data-finish-filter="hue-rotate(30deg) saturate(1.2)">
                                    <div class="colour-swatch-box swatch-cognac-bronze"></div>
                                    <div class="colour-card-body">
                                        <span class="colour-card-tag">Exclusive</span>
                                        <div class="colour-card-name">Cognac Bronze</div>
                                        <div class="colour-card-code">Warm Satin · CB-22</div>
                                    </div>
                                </button>

                                <!-- 5. Obsidian -->
                                <button type="button" class="colour-card" data-colour-card data-finish-name="Obsidian" data-finish-code="OB-00" data-finish-filter="contrast(1.3) brightness(0.6)">
                                    <div class="colour-swatch-box swatch-obsidian"></div>
                                    <div class="colour-card-body">
                                        <span class="colour-card-tag">Signature</span>
                                        <div class="colour-card-name">Obsidian</div>
                                        <div class="colour-card-code">Piano Gloss · OB-00</div>
                                    </div>
                                </button>

                                <!-- 6. Ivory Pearl -->
                                <button type="button" class="colour-card" data-colour-card data-finish-name="Ivory Pearl" data-finish-code="IP-05" data-finish-filter="brightness(1.25) contrast(0.95)">
                                    <div class="colour-swatch-box swatch-ivory-pearl"></div>
                                    <div class="colour-card-body">
                                        <span class="colour-card-tag">Limited</span>
                                        <div class="colour-card-name">Ivory Pearl</div>
                                        <div class="colour-card-code">Tri-Coat Ceramic · IP-05</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             SCENE 8: SPECIFICATION — Horizontal spec bars. Full data.
             ================================================================== -->
        <section class="scene-wrapper" id="scene-8" style="height: 190vh;">
            <div class="scene">
                <div class="scene-number">SCENE 08 / SPECIFICATION</div>

                <div class="scene-bg"></div>
                <div class="scene-lighting scene-light-key"></div>
                <div class="scene-lighting scene-light-rim"></div>

                <div class="scene-inner scene-inner--center">
                    <div class="scene-content" style="width: 100%; max-width: 980px;">
                        <p class="scene-label">08 <span>/</span> SPECIFICATION</p>
                        <h2 class="scene-heading" style="font-size: clamp(2.5rem, 5vw, 5.2rem);">MEASURED CAPACITY</h2>

                        <!-- Signature Element 6: Horizontal Spec Reveal Bars -->
                        <div class="spec-reveal-container" aria-label="Engineering Specification Benchmarks">
                            <div class="spec-row">
                                <div class="spec-row-meta">
                                    <span class="spec-label">TOTAL POWERTRAIN OUTPUT</span>
                                    <span class="spec-val">620 kW / 840 HP · Instant Vectoring</span>
                                </div>
                                <div class="spec-bar-track">
                                    <div class="spec-bar-fill" style="--fill-ratio: 0.92;"></div>
                                </div>
                            </div>

                            <div class="spec-row">
                                <div class="spec-row-meta">
                                    <span class="spec-label">WLTP HOMOLOGATED RANGE</span>
                                    <span class="spec-val">740 KM Combined Electric Cycle</span>
                                </div>
                                <div class="spec-bar-track">
                                    <div class="spec-bar-fill" style="--fill-ratio: 0.88;"></div>
                                </div>
                            </div>

                            <div class="spec-row">
                                <div class="spec-row-meta">
                                    <span class="spec-label">RECHARGE RATE (800V DC ARCHITECTURE)</span>
                                    <span class="spec-val">350 kW Peak · 10% to 80% in 15 Minutes</span>
                                </div>
                                <div class="spec-bar-track">
                                    <div class="spec-bar-fill" style="--fill-ratio: 0.95;"></div>
                                </div>
                            </div>

                            <div class="spec-row">
                                <div class="spec-row-meta">
                                    <span class="spec-label">AERODYNAMIC DRAG COEFFICIENT</span>
                                    <span class="spec-val">0.208 Cd Laminar Profile</span>
                                </div>
                                <div class="spec-bar-track">
                                    <div class="spec-bar-fill" style="--fill-ratio: 0.96;"></div>
                                </div>
                            </div>

                            <div class="spec-row">
                                <div class="spec-row-meta">
                                    <span class="spec-label">CHASSIS TORSIONAL RIGIDITY</span>
                                    <span class="spec-val">48,000 Nm/deg · Carbon-Titanium Core</span>
                                </div>
                                <div class="spec-bar-track">
                                    <div class="spec-bar-fill" style="--fill-ratio: 0.90;"></div>
                                </div>
                            </div>

                            <div class="spec-row">
                                <div class="spec-row-meta">
                                    <span class="spec-label">ACOUSTIC CABIN ISOLATION</span>
                                    <span class="spec-val">38 dB at 120 km/h · Active Anti-Noise Array</span>
                                </div>
                                <div class="spec-bar-track">
                                    <div class="spec-bar-fill" style="--fill-ratio: 0.94;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             SCENE 9: DENOUEMENT — The invitation. Reserve. Begin.
             ================================================================== -->
        <section class="scene-wrapper" id="scene-9" style="height: 160vh;">
            <div class="scene">
                <div class="scene-number">SCENE 09 / DENOUEMENT</div>

                <div class="scene-bg"></div>
                <div class="scene-lighting scene-light-denouement"></div>
                <div class="scene-overlay"></div>

                <div class="scene-inner">
                    <div class="scene-content">
                        <p class="scene-label">09 <span>/</span> DENOUEMENT</p>
                        <h2 class="scene-heading">THE HORIZON AWAITS</h2>
                        <p class="scene-sub">
                            Crafted in strictly limited production allocations. Select your vehicle from our curated showroom or commission a bespoke build tailored to your exact specification.
                        </p>

                        <div class="denouement-actions">
                            <button type="button" class="cta-cinema-primary" data-open-reservation data-car-id="{{ $primaryVehicle?->id }}">
                                Reserve Commission
                                <i class="fa fa-arrow-right" aria-hidden="true"></i>
                            </button>

                            <a href="{{ route('furever.shop') }}" class="cta-cinema-ghost">
                                Explore Showroom Inventory
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Semantic Showroom Data --}}
        <div class="tz-home" style="display:none;" aria-hidden="true">
            <span class="tz-eyebrow">TZ Carshop</span>
            <h2>Choose Your Toyota</h2>
            <h2>Drive Your Next Chapter</h2>
            <h3>Find Your Perfect Ride</h3>
            <h3>Why Choose TZ Carshop</h3>
            <h3>A Simple Process</h3>
            <p>Your Next Adventure Is Waiting</p>
            @foreach($products as $p)
                <div class="tz-home-benefits__vehicle">
                    <strong>{{ $p->name }}</strong>
                    <span>{{ $p->color }}</span>
                </div>
            @endforeach
        </div>

    </main>

    <!-- ======================================================================
         INTERACTIVE BESPOKE RESERVATION MODAL (UI/UX PRINCIPLES)
         ====================================================================== -->
    <div class="cinematic-modal-backdrop" id="reservation-modal" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
        <div class="cinematic-modal-panel">
            <button type="button" class="cinematic-modal-close" data-close-reservation aria-label="Close Reservation Window">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>

            <!-- FORM STAGE -->
            <div id="reservation-form-section">
                <div class="modal-header-meta">
                    <span class="modal-step-tag">COMMISSION SPECIFICATION · STEP 01</span>
                    <h2 class="modal-title" id="modal-headline">Reserve Your Commission</h2>
                </div>

                <form id="cinematic-reservation-form" action="{{ route('furever.reserve') }}" method="POST">
                    @csrf
                    <input type="hidden" name="finish" id="res-finish-name" value="Void Black">

                    <div class="reservation-form-layout">
                        <!-- Left: Live Configuration & Price Card -->
                        <div class="config-summary-box">
                            <div>
                                <img src="{{ $primaryImage }}" alt="" class="summary-car-preview" id="modal-summary-image">
                                <h3 id="modal-summary-name" style="font-size: 18px; font-weight: 700; color: #fff;">{{ $primaryVehicle?->name ?? 'Vehicle' }}</h3>
                                <div style="font-size: 11px; color: var(--accent-gold); letter-spacing: 0.1em; text-transform: uppercase; margin-top: 4px;">
                                    ATELIER BESPOKE SPECIFICATION
                                </div>

                                <ul class="summary-spec-list">
                                    <li><span>Selected Finish</span> <strong id="modal-summary-finish">Void Black</strong></li>
                                    <li><span>Production Model Year</span> <strong id="modal-summary-year">{{ $primaryVehicle?->year ?? date('Y') }}</strong></li>
                                    <li><span>Transmission</span> <strong id="modal-summary-trans">{{ $primaryVehicle?->transmission ?? 'Automatic' }}</strong></li>
                                    <li><span>Allocation Status</span> <strong style="color: var(--accent-blue-ice);">Immediate Build Slot</strong></li>
                                </ul>
                            </div>

                            <div class="summary-price-row">
                                <span>TOTAL COMMISSION</span>
                                <strong id="modal-summary-price">&#8369;{{ number_format($primaryVehicle?->price ?? 0, 2) }}</strong>
                            </div>
                        </div>

                        <!-- Right: Vehicle Selection & Client Credentials -->
                        <div>
                            <div class="form-group-cinema">
                                <label for="res-vehicle-select" class="form-label-cinema">Select Vehicle Model</label>
                                <select name="product_id" id="res-vehicle-select" class="form-select-cinema" required>
                                    @foreach($products as $p)
                                        @php
                                            $pImg = $p->image 
                                                ? (filter_var($p->image, FILTER_VALIDATE_URL) ? $p->image : asset('storage/'.$p->image)) 
                                                : $primaryImage;
                                        @endphp
                                        <option value="{{ $p->id }}" 
                                                data-name="{{ $p->name }}"
                                                data-price-formatted="&#8369;{{ number_format($p->price, 2) }}"
                                                data-year="{{ $p->year }}"
                                                data-transmission="{{ $p->transmission }}"
                                                data-image="{{ $pImg }}"
                                                @selected($p->id == $primaryVehicle?->id)>
                                            {{ $p->name }} — &#8369;{{ number_format($p->price, 2) }} ({{ $p->stock }} Available)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @guest
                                <div class="form-group-cinema">
                                    <label for="guest_name" class="form-label-cinema">Full Name / Commission Entity</label>
                                    <input type="text" name="guest_name" id="guest_name" class="form-input-cinema" placeholder="e.g. Alexander Vance" required>
                                </div>

                                <div class="form-group-cinema">
                                    <label for="guest_email" class="form-label-cinema">Contact Email Address</label>
                                    <input type="email" name="guest_email" id="guest_email" class="form-input-cinema" placeholder="client@domain.com" required>
                                </div>

                                <div class="form-group-cinema">
                                    <label for="guest_password" class="form-label-cinema">Access Password (Optional to Save Account)</label>
                                    <input type="password" name="guest_password" id="guest_password" class="form-input-cinema" placeholder="••••••••" value="client2026">
                                </div>
                            @else
                                <div class="form-group-cinema" style="background: rgba(255,255,255,0.02); padding: 14px; border: 1px solid rgba(255,255,255,0.06);">
                                    <span class="form-label-cinema">Authenticated Client</span>
                                    <div style="font-size: 14px; color: #fff; font-weight: 600;">{{ auth()->user()->name }}</div>
                                    <div style="font-size: 11px; color: var(--whisper);">{{ auth()->user()->email }}</div>
                                </div>
                            @endguest

                            <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 10px;">
                                <button type="submit" class="cta-cinema-primary" style="width: 100%;">
                                    Confirm Reservation
                                </button>
                                <p style="font-size: 10px; color: var(--whisper); text-align: center; letter-spacing: 0.05em;">
                                    Allocation is secured immediately upon confirmation in the TZ Carshop registry.
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- SUCCESS CONFIRMATION STAGE -->
            <div id="reservation-success-section" style="display: none;" class="order-success-screen">
                <div class="order-success-icon">
                    <i class="fa fa-check" aria-hidden="true"></i>
                </div>
                <span class="modal-step-tag">RESERVATION RECORD SECURED</span>
                <h3 style="font-size: 26px; font-weight: 800; color: #fff; margin-top: 8px;">Commission Confirmed</h3>
                <div class="order-ref-badge" data-order-ref>ORD-00000</div>
                <p style="font-size: 14px; color: var(--dim-white); max-width: 480px; margin: 0 auto 28px;">
                    Your build slot for the <strong id="confirmed-car-name" data-confirmed-car style="color: #fff;">Toyota Vehicle</strong> is now reserved in our system. You may review and manage your commission in your customer dashboard.
                </p>

                <div style="display: flex; justify-content: center; gap: 16px;">
                    <a href="{{ route('furever.customer-orders') }}" class="cta-cinema-primary">
                        View My Orders
                    </a>
                    <button type="button" class="cta-cinema-ghost" data-close-reservation>
                        Return to Exhibition
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cinematic Runtime Scripts -->
    <script src="{{ asset('js/cinematic.js') }}"></script>
</body>
</html>
