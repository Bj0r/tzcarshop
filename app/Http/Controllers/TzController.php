<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TzController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOP HOME
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Product::query();

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {

                $q->where(
                    'name',
                    'LIKE',
                    '%'.$request->search.'%'
                )
                    ->orWhere(
                        'description',
                        'LIKE',
                        '%'.$request->search.'%'
                    );

            });

        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {

            $query->where(
                'category',
                $request->category
            );

        }

        $sort = $request->input('sort', 'latest');

        match ($sort) {
            'name' => $query->orderBy('name'),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        $products = $query->get();

        $categories = Product::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $cart = session()->get('cart', []);

        $cartCount = collect($cart)->sum('quantity');

        return view(
            'pages.tz',
            compact(
                'products',
                'categories',
                'cartCount'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SHOP PAGE
    |--------------------------------------------------------------------------
    */

    public function shop(Request $request)
    {
        // Products, search, and filters are loaded by the page from GET /api/tz/products.
        return view('pages.tz-shop');
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $totalCategories = Product::distinct('transmission')
            ->count('transmission');
        $totalCustomers = User::where('role', 'customer')->count();
        $activeCustomers = User::where('role', 'customer')
            ->where('status', 'active')
            ->count();
        $lowStockProducts = Product::query()
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        return view(
            'pages.tz-dashboard',
            compact(
                'totalProducts',
                'totalStock',
                'totalCategories',
                'totalCustomers',
                'activeCustomers',
                'lowStockProducts'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function manageProducts(Request $request)
    {
        $products = Product::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($productQuery) use ($request) {
                    $productQuery
                        ->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('description', 'like', '%'.$request->search.'%')
                        ->orWhere('color', 'like', '%'.$request->search.'%')
                        ->orWhere('transmission', 'like', '%'.$request->search.'%')
                        ->orWhere('fuel_type', 'like', '%'.$request->search.'%')
                        ->orWhere('year', 'like', '%'.$request->search.'%');
                });
            })
            ->latest()
            ->get();

        $totalProducts = Product::count();

        $totalStock = Product::sum('stock');

        $totalCategories = Product::distinct('transmission')
            ->count('transmission');

        return view(
            'pages.tz-products',
            compact(
                'products',
                'totalProducts',
                'totalStock',
                'totalCategories'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function manageCustomers(Request $request)
    {
        $customers = User::query()
            ->where('role', 'customer')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($customerQuery) use ($request) {
                    $customerQuery
                        ->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('username', 'like', '%'.$request->search.'%')
                        ->orWhere('email', 'like', '%'.$request->search.'%');
                });
            })
            ->latest()
            ->get();

        $totalCustomers = User::where('role', 'customer')->count();
        $activeCustomers = User::where('role', 'customer')
            ->where('status', 'active')
            ->count();

        return view(
            'pages.tz-customers',
            compact('customers', 'totalCustomers', 'activeCustomers')
        );
    }

    public function updateCustomerStatus(Request $request, User $user)
    {
        abort_unless($user->role === 'customer', 404);

        $validated = $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $user->update($validated);

        return redirect()
            ->route('furever.customers')
            ->with('success', 'Customer status updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function manageOrders(Request $request)
    {
        $orders = Order::query()
            ->with(['customer', 'items'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->value();

                $query->where(function ($orderQuery) use ($search) {
                    $orderQuery
                        ->where('id', 'like', '%'.$search.'%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery
                                ->where('name', 'like', '%'.$search.'%')
                                ->orWhere('username', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%');
                        });
                });
            })
            ->latest()
            ->get();

        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)->count();
        $completedOrders = Order::where('status', Order::STATUS_COMPLETED)->count();

        return view(
            'pages.tz-orders',
            compact('orders', 'totalOrders', 'pendingOrders', 'completedOrders')
        );
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', Order::statuses())],
        ]);

        $updated = DB::transaction(function () use ($order, $validated) {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if (! $lockedOrder->canBeUpdatedByAdminTo($validated['status'])) {
                return false;
            }

            if ($validated['status'] === Order::STATUS_CANCELLED) {
                $lockedOrder->restoreReservedStock();
            }

            $lockedOrder->update([
                'status' => $validated['status'],
                'received_at' => null,
            ]);

            return true;
        });

        if (! $updated) {
            return redirect()
                ->route('furever.orders')
                ->with('error', 'That status change is not allowed for this order.');
        }

        return redirect()
            ->route('furever.orders')
            ->with('success', 'Order #'.$order->id.' status updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER ORDERS
    |--------------------------------------------------------------------------
    */

    public function customerOrders(Request $request)
    {
        abort_unless($request->user()?->isFureverCustomer(), 403);

        // Order history and actions are loaded from /api/tz/orders.
        return view('pages.tz-customer-orders');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE PRODUCT PAGE
    |--------------------------------------------------------------------------
    */

    public function createProduct()
    {
        return view(
            'pages.tz-create-product'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function storeProduct(Request $request)
    {
        $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^Toyota\s+[A-Za-z0-9][A-Za-z0-9 .-]*$/',
            ],

            'year' => ['required', 'integer', 'between:1990,2100'],

            'color' => ['required', 'string', 'max:100'],

            'transmission' => ['required', 'in:Manual,Automatic,CVT'],

            'fuel_type' => ['required', 'in:Gasoline,Diesel,Hybrid'],

            'description' => [
                'nullable',
                'string',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_url' => ['nullable', 'url:http,https', 'max:2048'],

        ]);

        $imagePath = $request->input('image_url');

        /*
        |--------------------------------------------------------------------------
        | IMAGE UPLOAD
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            $imagePath = $request
                ->file('image')
                ->store(
                    'products',
                    'public'
                );

        }

        /*
        |--------------------------------------------------------------------------
        | CREATE PRODUCT
        |--------------------------------------------------------------------------
        */

        Product::create([

            'name' => $request->name,

            'description' => $request->description,

            'category' => 'Toyota',

            'year' => $request->year,

            'color' => $request->color,

            'transmission' => $request->transmission,

            'fuel_type' => $request->fuel_type,

            'price' => $request->price,

            'stock' => $request->stock,

            'image' => $imagePath,

        ]);

        return redirect()
            ->route('furever.products')
            ->with(
                'success',
                'Car added successfully!'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT PRODUCT
    |--------------------------------------------------------------------------
    */

    public function editProduct(Product $product)
    {
        return view(
            'pages.tz-edit-product',
            compact('product')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function updateProduct(
        Request $request,
        Product $product
    ) {

        $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^Toyota\s+[A-Za-z0-9][A-Za-z0-9 .-]*$/',
            ],

            'year' => ['required', 'integer', 'between:1990,2100'],

            'color' => ['required', 'string', 'max:100'],

            'transmission' => ['required', 'in:Manual,Automatic,CVT'],

            'fuel_type' => ['required', 'in:Gasoline,Diesel,Hybrid'],

            'description' => [
                'nullable',
                'string',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_url' => ['nullable', 'url:http,https', 'max:2048'],

        ]);

        $imagePath = $request->filled('image_url')
            ? $request->input('image_url')
            : $product->image;

        /*
        |--------------------------------------------------------------------------
        | REPLACE IMAGE
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            if (
                $product->image &&
                Storage::disk('public')
                    ->exists($product->image)
            ) {

                Storage::disk('public')
                    ->delete($product->image);

            }

            $imagePath = $request
                ->file('image')
                ->store(
                    'products',
                    'public'
                );

        }

        $product->update([

            'name' => $request->name,

            'description' => $request->description,

            'category' => 'Toyota',

            'year' => $request->year,

            'color' => $request->color,

            'transmission' => $request->transmission,

            'fuel_type' => $request->fuel_type,

            'price' => $request->price,

            'stock' => $request->stock,

            'image' => $imagePath,

        ]);

        return redirect()
            ->route('furever.products')
            ->with(
                'success',
                'Car updated successfully!'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function destroyProduct(Product $product)
    {

        if (
            $product->image &&
            Storage::disk('public')
                ->exists($product->image)
        ) {

            Storage::disk('public')
                ->delete($product->image);

        }

        $product->delete();

        return redirect()
            ->route('furever.products')
            ->with(
                'success',
                'Car deleted successfully!'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CART PAGE
    |--------------------------------------------------------------------------
    */

    public function cart()
    {
        // Cart items, quantity updates, and checkout use /api/tz/cart and /api/tz/orders.
        return view('pages.tz-cart');
    }
}
