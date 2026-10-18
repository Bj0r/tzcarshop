<x-layout>
    @push('styles')<link rel="stylesheet" href="{{ asset('css/tz.css') }}">@endpush

    <main class="tz-auth tz-auth--register" data-tz-auth>
        <section class="tz-auth__showcase" aria-labelledby="tz-register-brand">
            <a href="{{ route('furever') }}" class="tz-auth__wordmark" aria-label="TZ ATELIER Showroom">
                <img src="{{ asset('img/tz-mark.svg') }}" alt="TZ Mark" class="tz-auth-logo-mark">
                <strong id="tz-register-brand">TZ ATELIER &bull; TZ Carshop</strong>
            </a>

            <div class="tz-auth__showcase-copy">
                <span class="tz-auth__eyebrow">Your Toyota journey</span>
                <h1>Find the Toyota<br>that fits your drive.</h1>
                <p>Create an account to explore available models and keep your vehicle activity in one place.</p>
            </div>

            <div class="tz-auth__car" aria-hidden="true"><span class="tz-auth__road-line"></span><i class="fa fa-car"></i></div>
            <ul class="tz-auth__features" aria-label="Account benefits">
                <li><i class="fa fa-check" aria-hidden="true"></i> Browse current availability</li>
                <li><i class="fa fa-check" aria-hidden="true"></i> Simple, secure access</li>
            </ul>
        </section>

        <section class="tz-auth__panel">
            <div class="tz-auth__form-wrap">
                <a href="{{ route('furever') }}" class="tz-auth__mobile-brand"><img src="{{ asset('img/tz-mark.svg') }}" alt="TZ Mark" class="tz-auth-logo-mark"><strong>TZ ATELIER &bull; TZ Carshop</strong></a>
                <header class="tz-auth__heading">
                    <span class="tz-auth__eyebrow">Join the showroom</span>
                    <h2>Create your account</h2>
                    <p>Set up your TZ Carshop access in a few steps.</p>
                </header>

                <form action="{{ route('furever.register.store') }}" method="POST" class="tz-auth__form">
                    @csrf
                    <div class="tz-auth__field">
                        <label for="name">Full name</label>
                        <div class="tz-auth__input"><i class="fa fa-id-card-o" aria-hidden="true"></i><input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" placeholder="Enter your full name" required autofocus></div>
                        @error('name')<p class="tz-auth__error">{{ $message }}</p>@enderror
                    </div>
                    <div class="tz-auth__form-row">
                        <div class="tz-auth__field">
                            <label for="username">Username</label>
                            <div class="tz-auth__input"><i class="fa fa-user-o" aria-hidden="true"></i><input id="username" type="text" name="username" value="{{ old('username') }}" autocomplete="username" placeholder="Choose a username" required></div>
                            @error('username')<p class="tz-auth__error">{{ $message }}</p>@enderror
                        </div>
                        <div class="tz-auth__field">
                            <label for="email">Email address</label>
                            <div class="tz-auth__input"><i class="fa fa-envelope-o" aria-hidden="true"></i><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="you@example.com" required></div>
                            @error('email')<p class="tz-auth__error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="tz-auth__form-row">
                        <div class="tz-auth__field">
                            <label for="password">Password</label>
                            <div class="tz-auth__input"><i class="fa fa-lock" aria-hidden="true"></i><input id="password" type="password" name="password" autocomplete="new-password" placeholder="At least 8 characters" required><button type="button" data-password-toggle aria-controls="password" aria-label="Show password" aria-pressed="false"><i class="fa fa-eye" aria-hidden="true"></i></button></div>
                            @error('password')<p class="tz-auth__error">{{ $message }}</p>@enderror
                        </div>
                        <div class="tz-auth__field">
                            <label for="password_confirmation">Confirm password</label>
                            <div class="tz-auth__input"><i class="fa fa-lock" aria-hidden="true"></i><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Repeat password" required><button type="button" data-password-toggle aria-controls="password_confirmation" aria-label="Show password" aria-pressed="false"><i class="fa fa-eye" aria-hidden="true"></i></button></div>
                        </div>
                    </div>
                    <button type="submit" class="tz-auth__submit">Create account <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                </form>

                <p class="tz-auth__switch">Already have an account? <a href="{{ route('furever.login') }}">Sign in</a></p>
                <a href="{{ route('furever') }}" class="tz-auth__back"><i class="fa fa-arrow-left" aria-hidden="true"></i> Return to showroom</a>
            </div>
        </section>
    </main>
</x-layout>
