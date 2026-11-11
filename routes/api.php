<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::apiResource('post', PostController::class);

/*
|--------------------------------------------------------------------------
| TZ Carshop customer API
|--------------------------------------------------------------------------
|
| Used by the customer shop, cart, and orders pages (JSON only).
| Auth uses Sanctum with the browser session (statefulApi).
|
*/

Route::prefix('tz')->name('api.tz.')->group(function () {
    // Customer account
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('user', [AuthController::class, 'user'])->name('user');
    });

    // Products
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::middleware(['auth:sanctum', 'tz.customer'])->group(function () {
        Route::get('cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('cart', [CartController::class, 'store'])->name('cart.store');
        Route::patch('cart/{product}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::patch('orders/{order}/received', [OrderController::class, 'received'])->name('orders.received');
    });
});
