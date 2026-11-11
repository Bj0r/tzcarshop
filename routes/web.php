<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\BookQueryController;
use App\Http\Controllers\BookQueryControllerStock_Limit;
use App\Http\Controllers\TzAuthController;
use App\Http\Controllers\TzController;

/*
|--------------------------------------------------------------------------
| Class activities
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/person', function () {
    return view('person');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard');
});

Route::get('/p1', [MovieController::class, 'index']);
Route::get('/movie-form', [MovieController::class, 'show_add_form']);
Route::post('/add-movie', [MovieController::class, 'do_add']);
Route::get('/edit-movie/{id}', [MovieController::class, 'show_edit_form']);
Route::post('/update-movie', [MovieController::class, 'do_update']);
Route::get('/delete-movie/{id}', [MovieController::class, 'do_delete']);

Route::get('/p2', [BookController::class, 'index']);
Route::get('/book-form', [BookController::class, 'show_add_form']);
Route::post('/add-book', [BookController::class, 'do_add']);
Route::get('/edit-book/{id}', [BookController::class, 'show_edit_form']);
Route::post('/update-book', [BookController::class, 'do_update']);
Route::get('/delete-book/{id}', [BookController::class, 'do_delete']);

Route::get('/p4', fn () => view('pages.placeholder', ['heading' => 'Page 4']));

/*
|--------------------------------------------------------------------------
| FurEver Main Shop Page
|--------------------------------------------------------------------------
|
| URL:
| http://127.0.0.1:8000/p3
|
*/

Route::get('/p3', [TzController::class, 'index'])
    ->name('furever');

/*
|--------------------------------------------------------------------------
| FurEver Shop Page
|--------------------------------------------------------------------------
*/

Route::get('/p3/shop', [TzController::class, 'shop'])
    ->name('furever.shop');

/*
|--------------------------------------------------------------------------
| FurEver Authentication pages
|--------------------------------------------------------------------------
|
| Only the pages live here. Login, register, and logout are submitted to
| the JSON API (POST /api/tz/login, /api/tz/register, /api/tz/logout).
|
*/

Route::get('/p3/login', [TzAuthController::class, 'create'])
    ->name('furever.login');

Route::get('/p3/register', [TzAuthController::class, 'createRegistration'])
    ->name('furever.register');

Route::post('/p3/reserve', [TzController::class, 'reserve'])
    ->name('furever.reserve');

/*
|--------------------------------------------------------------------------
| PRODUCT MANAGEMENT
|--------------------------------------------------------------------------
*/

Route::middleware('furever.admin')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/p3/dashboard', [TzController::class, 'dashboard'])
        ->name('furever.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Display All Products
    |--------------------------------------------------------------------------
    */

    Route::get('/p3/manage-products', [TzController::class, 'manageProducts'])
        ->name('furever.products');

    /*
    |--------------------------------------------------------------------------
    | Create Product Form
    |--------------------------------------------------------------------------
    */

    Route::get('/p3/manage-products/create', [TzController::class, 'createProduct'])
        ->name('furever.products.create');

    /*
    |--------------------------------------------------------------------------
    | Store New Product
    |--------------------------------------------------------------------------
    */

    Route::post('/p3/manage-products', [TzController::class, 'storeProduct'])
        ->name('furever.products.store');

    /*
    |--------------------------------------------------------------------------
    | Edit Product Form
    |--------------------------------------------------------------------------
    */

    Route::get('/p3/manage-products/{product}/edit', [TzController::class, 'editProduct'])
        ->name('furever.products.edit');

    /*
    |--------------------------------------------------------------------------
    | Update Product
    |--------------------------------------------------------------------------
    */

    Route::put('/p3/manage-products/{product}', [TzController::class, 'updateProduct'])
        ->name('furever.products.update');

    /*
    |--------------------------------------------------------------------------
    | Delete Product
    |--------------------------------------------------------------------------
    */

    Route::delete('/p3/manage-products/{product}', [TzController::class, 'destroyProduct'])
        ->name('furever.products.destroy');

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::get('/p3/manage-customers', [TzController::class, 'manageCustomers'])
        ->name('furever.customers');

    Route::patch('/p3/manage-customers/{user}/status', [TzController::class, 'updateCustomerStatus'])
        ->name('furever.customers.status');

    /*
    |--------------------------------------------------------------------------
    | ORDER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::get('/p3/manage-orders', [TzController::class, 'manageOrders'])
        ->name('furever.orders');

    Route::patch('/p3/manage-orders/{order}/status', [TzController::class, 'updateOrderStatus'])
        ->name('furever.orders.status');

});

/*
|--------------------------------------------------------------------------
| CUSTOMER CART & ORDERS PAGES
|--------------------------------------------------------------------------
|
| These pages only render the layout. Add to cart, quantity updates,
| checkout, and order actions go through the JSON API in routes/api.php.
|
*/

Route::middleware('furever.authenticated')->group(function () {
    Route::get('/p3/cart', [TzController::class, 'cart'])
        ->name('furever.cart');

    Route::get('/p3/orders', [TzController::class, 'customerOrders'])
        ->name('furever.customer-orders');
});

/*
|--------------------------------------------------------------------------
| Activity Story
|--------------------------------------------------------------------------
*/

Route::get('/story', function () {
    return view('pages.story');
});

Route::get('/book-query-comparison', [BookQueryController::class, 'index']);
Route::get('/book-query-comparison-stock-limit', [BookQueryControllerStock_Limit::class, 'index']);
