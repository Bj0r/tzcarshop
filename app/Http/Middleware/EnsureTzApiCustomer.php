<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTzApiCustomer
{
    /**
     * Only active customer accounts may use the cart and order API.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isFureverCustomer() || $user->status !== 'active') {
            return response()->json([
                'message' => 'Only active customer accounts can use the cart and orders.',
            ], 403);
        }

        return $next($request);
    }
}
