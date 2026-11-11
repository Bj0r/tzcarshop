<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
