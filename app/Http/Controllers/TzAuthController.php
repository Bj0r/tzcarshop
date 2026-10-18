<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TzAuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->status !== 'active') {
                return $this->logoutInactiveUser(request());
            }

            return redirect()->route($this->destinationFor(Auth::user()));
        }

        return view('pages.tz-login');
    }

    public function createRegistration(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->status !== 'active') {
                return $this->logoutInactiveUser(request());
            }

            return redirect()->route($this->destinationFor(Auth::user()));
        }

        return view('pages.tz-register');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('username', $credentials['login'])
            ->orWhere('email', $credentials['login'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => 'The supplied username or password is incorrect.']);
        }

        if ($user->status !== 'active') {
            return back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => 'This TZ Carshop account is inactive. Please contact the administrator.']);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()
            ->route($this->destinationFor($user))
            ->with('success', 'Welcome back, '.$user->name.'!');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'regex:/^[A-Za-z0-9._-]+$/', 'min:3', 'max:50', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $customer = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'customer',
            'status' => 'active',
        ]);

        Auth::login($customer);
        $request->session()->regenerate();

        return redirect()
            ->route('furever.shop')
            ->with('success', 'Your TZ Carshop account is ready. Welcome, '.$customer->name.'!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('furever.login')
            ->with('success', 'You have been signed out.');
    }

    private function destinationFor(User $user): string
    {
        return $user->isFureverAdmin()
            ? 'furever.dashboard'
            : 'furever.shop';
    }

    private function logoutInactiveUser(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('furever.login')
            ->with('error', 'This TZ Carshop account is inactive. Please contact the administrator.');
    }
}
