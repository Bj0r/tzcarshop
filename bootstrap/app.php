<?php

use App\Http\Middleware\EnsureTzActiveUser;
use App\Http\Middleware\EnsureTzApiCustomer;
use App\Http\Middleware\EnsureTzAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Let the customer pages call /api routes with the browser session (Sanctum SPA auth).
        $middleware->statefulApi();

        // Guests on API routes get a 401 JSON answer; elsewhere they go to the shop login page.
        $middleware->redirectGuestsTo(
            fn (Request $request) => $request->is('api/*') ? null : route('furever.login')
        );

        $middleware->alias([
            'furever.admin' => EnsureTzAdmin::class,
            'furever.authenticated' => EnsureTzActiveUser::class,
            'tz.customer' => EnsureTzApiCustomer::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API routes always answer in JSON, even when opened in a browser tab
        // (for example 401 instead of redirecting to a login route).
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson()
        );
    })->create();
