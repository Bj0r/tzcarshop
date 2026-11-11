<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendOrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * GET /api/tz/orders
     * The signed-in customer's order history only.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with('items')
            ->latest()
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $orders->map(fn (Order $order) => $this->format($order)),
        ]);
    }

    /**
     * GET /api/tz/orders/{order}
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        $this->ensureOwner($request, $order);

        return response()->json(['data' => $this->format($order->load('items'))]);
    }

    /**
     * POST /api/tz/orders
     * Checkout the selected cart products.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'selected_items' => ['required', 'array', 'min:1'],
            'selected_items.*' => ['integer', 'distinct'],
        ], [
            'selected_items.required' => 'Select at least one product to check out.',
            'selected_items.min' => 'Select at least one product to check out.',
        ]);

        $cart = session()->get('cart', []);
        $selectedCart = array_intersect_key($cart, array_flip($validated['selected_items']));

        if (empty($selectedCart)) {
            throw ValidationException::withMessages([
                'selected_items' => 'Select a product that is still in your cart to check out.',
            ]);
        }

        $order = DB::transaction(function () use ($request, $selectedCart) {
            $purchasedItems = [];
            $total = 0;

            foreach ($selectedCart as $productId => $item) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->find($productId);

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

                $purchasedItems[] = ['product' => $product, 'quantity' => $item['quantity']];
                $total += (float) $product->price * $item['quantity'];
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'total' => round($total, 2),
                'status' => Order::STATUS_PENDING,
            ]);

            foreach ($purchasedItems as $purchasedItem) {
                $product = $purchasedItem['product'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $purchasedItem['quantity'],
                ]);

                $product->decrement('stock', $purchasedItem['quantity']);
            }

            return $order;
        });

        session()->put('cart', array_diff_key($cart, $selectedCart));

        SendOrderConfirmation::dispatch($order);

        return response()->json([
            'message' => 'Order #'.$order->id.' has been placed successfully.',
            'data' => $this->format($order->load('items')),
        ], 201);
    }

    /**
     * PATCH /api/tz/orders/{order}/cancel
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        $this->ensureOwner($request, $order);

        $cancelled = DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

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
            return response()->json(['message' => 'Orders can only be cancelled while they are pending.'], 422);
        }

        return response()->json([
            'message' => 'Order #'.$order->id.' has been cancelled.',
            'data' => $this->format($order->refresh()->load('items')),
        ]);
    }

    /**
     * PATCH /api/tz/orders/{order}/received
     */
    public function received(Request $request, Order $order): JsonResponse
    {
        $this->ensureOwner($request, $order);

        $updated = DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

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
            return response()->json(['message' => 'You can confirm receipt after the order is ready for collection.'], 422);
        }

        return response()->json([
            'message' => 'Order #'.$order->id.' is now marked as completed.',
            'data' => $this->format($order->refresh()->load('items')),
        ]);
    }

    private function ensureOwner(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 403, 'This order does not belong to your account.');
    }

    private function format(Order $order): array
    {
        return [
            'id' => $order->id,
            'total' => (float) $order->total,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'can_cancel' => $order->canBeCancelledByCustomer(),
            'can_mark_received' => $order->canBeMarkedReceivedByCustomer(),
            'created_at' => $order->created_at?->toIso8601String(),
            'received_at' => $order->received_at?->toIso8601String(),
            'items' => $order->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'price' => (float) $item->price,
                'quantity' => $item->quantity,
            ])->values(),
        ];
    }
}
