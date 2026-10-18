<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTzAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()
                ->route('furever.login')
                ->with('error', 'Please sign in to access FurEver administration.');
        }

        if (
            Auth::user()->role !== 'admin' ||
            Auth::user()->status !== 'active'
        ) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('furever.login')
                ->with('error', 'This account is not permitted to access FurEver administration.');
        }

        return $next($request);
    }
}
