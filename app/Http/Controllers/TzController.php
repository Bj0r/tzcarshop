<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('color', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('fuel_type', 'LIKE', '%'.$request->search.'%');
            });
        }

        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }

        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'name' => $query->orderBy('name'),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        $products = $query->get();
        $transmissions = Product::distinct('transmission')->pluck('transmission')->filter();
        $fuelTypes = Product::distinct('fuel_type')->pluck('fuel_type')->filter();

        $cart = session()->get('cart', []);
        $cartCount = collect($cart)->sum('quantity');

        return view(
            'pages.tz-shop',
            compact(
                'products',
                'transmissions',
                'fuelTypes',
                'cartCount'
            )
        );
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

        $orders = $request->user()
            ->orders()
            ->with('items')
            ->latest()
            ->get();

        return view('pages.tz-customer-orders', compact('orders'));
    }

    public function confirmOrderReceived(Request $request, Order $order)
    {
        abort_unless(
            $request->user()?->isFureverCustomer()
                && $order->user_id === $request->user()->id,
            403
        );

        if (! $order->canBeMarkedReceivedByCustomer()) {
            return redirect()
                ->route('furever.customer-orders')
                ->with('error', 'You can confirm receipt after the order is ready for collection.');
        }

        $updated = DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if (! $lockedOrder->canBeMarkedReceivedByCustomer()) {
                return false;
            }

            $lockedOrder->update([
                'status' => Order::STATUS_COMPLETED,
                'received_at' => now(),
            ]);

            return true;
        });

        if (! $updated) {
            return redirect()
                ->route('furever.customer-orders')
                ->with('error', 'This order is no longer ready to be confirmed.');
        }

        return redirect()
            ->route('furever.customer-orders')
            ->with('success', 'Order #'.$order->id.' is now marked as completed.');
    }

    public function cancelCustomerOrder(Request $request, Order $order)
    {
        abort_unless(
            $request->user()?->isFureverCustomer()
                && $order->user_id === $request->user()->id,
            403
        );

        if (! $order->canBeCancelledByCustomer()) {
            return redirect()
                ->route('furever.customer-orders')
                ->with('error', 'Orders can only be cancelled while they are pending.');
        }

        $cancelled = DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if (! $lockedOrder->canBeCancelledByCustomer()) {
                return false;
            }

            $lockedOrder->restoreReservedStock();
            $lockedOrder->update([
                'status' => Order::STATUS_CANCELLED,
                'received_at' => null,
            ]);

            return true;
        });

        if (! $cancelled) {
            return redirect()
                ->route('furever.customer-orders')
                ->with('error', 'This order can no longer be cancelled.');
        }

        return redirect()
            ->route('furever.customer-orders')
            ->with('success', 'Order #'.$order->id.' has been cancelled.');
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
    | ADD TO CART
    |--------------------------------------------------------------------------
    */

    public function addToCart($id)
    {
        $product = Product::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | CHECK STOCK
        |--------------------------------------------------------------------------
        */

        if ($product->stock <= 0) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'This product is currently out of stock.'
                );

        }

        $cart = session()->get('cart', []);

        /*
        |--------------------------------------------------------------------------
        | PRODUCT ALREADY EXISTS
        |--------------------------------------------------------------------------
        */

        if (isset($cart[$id])) {

            if (
                $cart[$id]['quantity']
                < $product->stock
            ) {

                $cart[$id]['quantity']++;

            }

        } else {

            $cart[$id] = [

                'id' => $product->id,

                'name' => $product->name,

                'price' => $product->price,

                'image' => $product->image,

                'quantity' => 1,

                'stock' => $product->stock,

            ];

        }

        session()->put(
            'cart',
            $cart
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Product added to cart!'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CART PAGE
    |--------------------------------------------------------------------------
    */

    public function cart()
    {
        $cart = session()->get(
            'cart',
            []
        );

        $total = 0;

        foreach ($cart as $item) {

            $total +=
                $item['price']
                *
                $item['quantity'];

        }

        return view(
            'pages.tz-cart',
            compact(
                'cart',
                'total'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE CART
    |--------------------------------------------------------------------------
    */

    public function updateCart(
        Request $request,
        $id
    ) {

        $request->validate([

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

        ]);

        $cart = session()->get(
            'cart',
            []
        );

        if (isset($cart[$id])) {

            if (
                $request->quantity
                <= $cart[$id]['stock']
            ) {

                $cart[$id]['quantity']
                    = $request->quantity;

            }

        }

        session()->put(
            'cart',
            $cart
        );

        return redirect()
            ->route('furever.cart');
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE CART ITEM
    |--------------------------------------------------------------------------
    */

    public function removeCart($id)
    {
        $cart = session()->get(
            'cart',
            []
        );

        if (isset($cart[$id])) {

            unset($cart[$id]);

        }

        session()->put(
            'cart',
            $cart
        );

        return redirect()
            ->route('furever.cart')
            ->with(
                'success',
                'Item removed from cart.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT
    |--------------------------------------------------------------------------
    */

    public function checkout(Request $request)
    {
        abort_unless(auth()->user()?->isFureverCustomer(), 403);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('furever.cart')
                ->with('error', 'Your cart is empty. Add a product before checking out.');
        }

        $validated = $request->validate([
            'selected_items' => ['required', 'array', 'min:1'],
            'selected_items.*' => ['integer', 'distinct'],
        ], [
            'selected_items.required' => 'Select at least one product to check out.',
            'selected_items.min' => 'Select at least one product to check out.',
        ]);

        $selectedItemIds = array_flip($validated['selected_items']);
        $selectedCart = array_intersect_key($cart, $selectedItemIds);

        if (empty($selectedCart)) {
            return redirect()
                ->route('furever.cart')
                ->with('error', 'Select a product that is still in your cart to check out.');
        }

        $order = DB::transaction(function () use ($selectedCart) {
            $purchasedItems = [];
            $total = 0;

            foreach ($selectedCart as $item) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->find($item['id']);

                if (! $product) {
                    throw ValidationException::withMessages([
                        'cart' => 'One of the products in your cart is no longer available.',
                    ]);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => $product->name.' no longer has enough stock for this order.',
                    ]);
                }

                $purchasedItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                ];
                $total += (float) $product->price * $item['quantity'];
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => round($total, 2),
                'status' => Order::STATUS_PENDING,
            ]);

            foreach ($purchasedItems as $purchasedItem) {
                $product = $purchasedItem['product'];
                $quantity = $purchasedItem['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                ]);

                $product->decrement('stock', $quantity);
            }

            return $order;
        });

        session()->put('cart', array_diff_key($cart, $selectedCart));

        return redirect()
            ->route('furever.customer-orders')
            ->with('success', 'Order #'.$order->id.' has been placed successfully. You can track its status here.');
    }

    /*
    |--------------------------------------------------------------------------
    | BESPOKE VEHICLE RESERVATION (CINEMATIC EXPERIENCE)
    |--------------------------------------------------------------------------
    */

    public function reserve(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'finish' => ['nullable', 'string', 'max:100'],
            'guest_name' => ['nullable', 'string', 'max:255'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'guest_password' => ['nullable', 'string', 'min:6'],
        ]);

        $user = auth()->user();

        // If guest reservation, handle customer authentication effortlessly
        if (! $user) {
            if ($request->filled('guest_email')) {
                $email = $request->guest_email;
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $request->input('guest_name', 'Bespoke Client'),
                        'username' => strtolower(explode('@', $email)[0]).'_'.rand(100, 999),
                        'password' => bcrypt($request->input('guest_password', 'password123')),
                        'role' => 'customer',
                        'status' => 'active',
                    ]
                );

                auth()->login($user);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide your name and email address to reserve this vehicle.',
                ], 422);
            }
        }

        $orderData = DB::transaction(function () use ($request, $user) {
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($request->product_id);

            if ($product->stock <= 0) {
                throw ValidationException::withMessages([
                    'product_id' => 'This vehicle is currently allocated or out of stock.',
                ]);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total' => round((float) $product->price, 2),
                'status' => Order::STATUS_PENDING,
            ]);

            $finishSuffix = $request->filled('finish') ? ' ('.$request->finish.')' : '';

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name.$finishSuffix,
                'price' => $product->price,
                'quantity' => 1,
            ]);

            $product->decrement('stock', 1);

            return [
                'order' => $order,
                'product' => $product,
            ];
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'order_id' => $orderData['order']->id,
                'product_name' => $orderData['product']->name.($request->filled('finish') ? ' — '.$request->finish : ''),
                'total' => number_format($orderData['order']->total, 2),
                'message' => 'Commission confirmed. Order #'.$orderData['order']->id.' has been secured.',
                'redirect' => route('furever.customer-orders'),
            ]);
        }

        return redirect()
            ->route('furever.customer-orders')
            ->with('success', 'Commission confirmed! Order #'.$orderData['order']->id.' has been secured.');
    }
}
