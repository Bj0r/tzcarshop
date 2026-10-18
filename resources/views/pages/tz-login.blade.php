<x-layout>
    <x-slot:title>TZ ATELIER — Client &amp; Atelier Access</x-slot:title>
    @push('styles')<link rel="stylesheet" href="{{ asset('css/tz.css') }}">@endpush

    <main class="tz-auth" data-tz-auth>
        {{-- LEFT VISUAL SHOWCASE: CINEMATIC ATELIER MONUMENT --}}
        <section class="tz-auth__showcase" aria-labelledby="tz-login-brand">
            <header>
                <a href="{{ route('furever') }}" class="tz-auth__wordmark" aria-label="TZ ATELIER Showroom">
                    <img src="{{ asset('img/tz-mark.svg') }}" alt="TZ Mark" class="tz-auth-logo-mark">
                    <strong id="tz-login-brand">
                        <span style="color:#fff;">TZ</span> <span style="letter-spacing:0.16em;">ATELIER</span>
                        <small style="display:block; font-size:10px; font-weight:600; color:var(--accent-gold); letter-spacing:0.18em; margin-top:2px;">TZ CARSHOP &bull; HAUTE AUTOMOTIVE</small>
                    </strong>
                </a>
            </header>

            <div class="tz-auth__showcase-copy">
                <span class="tz-auth__eyebrow">
                    <i class="fa fa-circle" style="color: #4ade80; font-size: 8px; vertical-align: middle; margin-right: 6px;"></i>
                    Client &bull; Admin Vault Access
                </span>
                <h1>The Key to Your<br><span style="color: var(--accent-gold);">Commission.</span></h1>
                <p>Customers and administrators can sign in to browse vehicles, continue shopping, or manage the showroom.</p>

                {{-- UNIQUE FLOATING SPEC CARD --}}
                <div class="tz-auth__car-card" aria-label="Featured Vehicle Allocation">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <div>
                            <span style="font-size: 10px; font-weight: 800; letter-spacing: 0.16em; text-transform: uppercase; color: var(--accent-gold); display: block; margin-bottom: 2px;">FLAGSHIP ALLOCATION</span>
                            <strong style="font-size: 16px; color: #FFFFFF; font-weight: 700;">Toyota GR Supra 3.0T &bull; Atelier Spec</strong>
                        </div>
                        <span class="cinematic-pill-tag cinematic-pill-tag--gold" style="font-size: 10px;">SLOT 09 / 12</span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; padding-top: 12px; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                        <div>
                            <span style="font-size: 9px; color: var(--dim-white); letter-spacing: 0.1em; text-transform: uppercase; display: block; margin-bottom: 2px;">POWERTRAIN</span>
                            <strong style="font-size: 14px; color: #FFFFFF; font-weight: 700;">840 HP</strong>
                        </div>
                        <div>
                            <span style="font-size: 9px; color: var(--dim-white); letter-spacing: 0.1em; text-transform: uppercase; display: block; margin-bottom: 2px;">ACCELERATION</span>
                            <strong style="font-size: 14px; color: var(--accent-blue-ice); font-weight: 700;">3.4s 0–100</strong>
                        </div>
                        <div>
                            <span style="font-size: 9px; color: var(--dim-white); letter-spacing: 0.1em; text-transform: uppercase; display: block; margin-bottom: 2px;">FINISH</span>
                            <strong style="font-size: 14px; color: #FFFFFF; font-weight: 700;">Void Black</strong>
                        </div>
                    </div>
                </div>

                {{-- HIDDEN CAR WRAPPER FOR BACKWARD COMPATIBILITY --}}
                <div class="tz-auth__car" aria-hidden="true" style="display:none;">
                    <span class="tz-auth__road-line"></span>
                    <i class="fa fa-car"></i>
                </div>
            </div>

            <ul class="tz-auth__features" aria-label="Platform features">
                <li><i class="fa fa-shield" aria-hidden="true"></i> 256-Bit Encrypted Vault</li>
                <li><i class="fa fa-check" aria-hidden="true"></i> Toyota-only catalog</li>
                <li><i class="fa fa-check" aria-hidden="true"></i> Simple stock control</li>
            </ul>
        </section>

        {{-- RIGHT ACCESS PANEL --}}
        <section class="tz-auth__panel">
            <div class="tz-auth__form-wrap">
                <a href="{{ route('furever') }}" class="tz-auth__mobile-brand">
                    <img src="{{ asset('img/tz-mark.svg') }}" alt="TZ Mark" class="tz-auth-logo-mark">
                    <strong>TZ ATELIER &bull; TZ Carshop</strong>
                </a>

                <header class="tz-auth__heading">
                    <span class="tz-auth__eyebrow">Secure Identification</span>
                    <h2>Welcome back</h2>
                    <p>Enter your account credentials to access your vehicle allocation portfolio.</p>
                </header>

                {{-- QUICK-FILL ROLE SHORTCUTS (UX DELIGHT FOR EVALUATOR / TESTING) --}}
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; padding: 10px 14px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08);">
                    <span style="font-size: 10px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: var(--dim-white);">
                        <i class="fa fa-bolt" style="color: var(--accent-gold); margin-right: 4px;"></i> Demo Access:
                    </span>
                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="quick-fill-btn" onclick="document.getElementById('login').value='tz.customer'; document.getElementById('password').value='Customer123454';" style="background: rgba(201,169,110,0.12); border: 1px solid rgba(201,169,110,0.3); color: var(--accent-gold); font-size: 10px; font-weight: 700; padding: 4px 10px; cursor: pointer; text-transform: uppercase; letter-spacing: 0.06em;">
                            <i class="fa fa-user"></i> Client
                        </button>
                        <button type="button" class="quick-fill-btn" onclick="document.getElementById('login').value='tz.admin'; document.getElementById('password').value='Admin123454';" style="background: rgba(74,144,217,0.12); border: 1px solid rgba(74,144,217,0.3); color: var(--accent-blue-ice); font-size: 10px; font-weight: 700; padding: 4px 10px; cursor: pointer; text-transform: uppercase; letter-spacing: 0.06em;">
                            <i class="fa fa-shield"></i> Admin
                        </button>
                    </div>
                </div>

                @if(session('error'))
                    <div class="tz-auth__message is-error" role="alert">
                        <i class="fa fa-exclamation-circle" style="margin-right: 6px;"></i> {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="tz-auth__message is-success" role="status">
                        <i class="fa fa-check-circle" style="margin-right: 6px;"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('furever.login.store') }}" method="POST" class="tz-auth__form">
                    @csrf
                    <div class="tz-auth__field">
                        <label for="login">Username or email</label>
                        <div class="tz-auth__input">
                            <i class="fa fa-user-o" aria-hidden="true"></i>
                            <input id="login" type="text" name="login" value="{{ old('login') }}" autocomplete="username" placeholder="Enter username or email" required autofocus>
                        </div>
                        @error('login')<p class="tz-auth__error">{{ $message }}</p>@enderror
                    </div>

                    <div class="tz-auth__field">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label for="password" style="margin-bottom: 0;">Password</label>
                            <span style="font-size: 11px; color: var(--whisper); letter-spacing: 0.04em;">Required</span>
                        </div>
                        <div class="tz-auth__input">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                            <input id="password" type="password" name="password" autocomplete="current-password" placeholder="Enter password" required>
                            <button type="button" data-password-toggle aria-controls="password" aria-label="Show password" aria-pressed="false" title="Toggle password visibility">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')<p class="tz-auth__error">{{ $message }}</p>@enderror
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label class="tz-auth__remember">
                            <input type="checkbox" name="remember" value="1">
                            <span>Keep me signed in</span>
                        </label>
                        <a href="#concierge" onclick="alert('For credential recovery, contact the TZ Atelier Concierge Desk.'); return false;" style="font-size: 11px; color: var(--dim-white); text-decoration: none;" onmouseover="this.style.color='var(--accent-gold)'" onmouseout="this.style.color='var(--dim-white)'">
                            Concierge Help
                        </a>
                    </div>

                    <button type="submit" class="tz-auth__submit">
                        <span>Sign In to Atelier</span>
                        <i class="fa fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                <p class="tz-auth__switch">
                    New to TZ Carshop? <a href="{{ route('furever.register') }}">Create an account</a>
                </p>

                <a href="{{ route('furever') }}" class="tz-auth__back">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                    <span>Return to showroom</span>
                </a>
            </div>
        </section>
    </main>
</x-layout>
