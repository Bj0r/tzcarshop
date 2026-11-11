<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Session cart. The session stores only product ids and quantities;
 * names, prices, and stock are always read fresh from the database.
 */
class CartController extends Controller
{
    /**
     * GET /api/tz/cart
     */
    public function index(): JsonResponse
    {
        return $this->cartResponse();
    }

    /**
     * POST /api/tz/cart
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $cart = $this->cart();
        $quantity = ($cart[$product->id]['quantity'] ?? 0) + ($validated['quantity'] ?? 1);

        if ($quantity > $product->stock) {
            return $this->stockError($product);
        }

        $cart[$product->id] = ['id' => $product->id, 'quantity' => $quantity];
        session()->put('cart', $cart);

        return $this->cartResponse($product->name.' added to cart.', 201);
    }

    /**
     * PATCH /api/tz/cart/{product}
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->cart();

        if (! isset($cart[$product->id])) {
            return response()->json(['message' => 'This product is not in your cart.'], 404);
        }

        if ($validated['quantity'] > $product->stock) {
            return $this->stockError($product);
        }

        $cart[$product->id]['quantity'] = $validated['quantity'];
        session()->put('cart', $cart);

        return $this->cartResponse('Cart updated.');
    }

    /**
     * DELETE /api/tz/cart/{product}
     */
    public function destroy(Product $product): JsonResponse
    {
        $cart = $this->cart();
        unset($cart[$product->id]);
        session()->put('cart', $cart);

        return $this->cartResponse('Item removed from cart.');
    }

    private function cart(): array
    {
        return session()->get('cart', []);
    }

    private function stockError(Product $product): JsonResponse
    {
        return response()->json([
            'message' => 'Only '.$product->stock.' '.$product->name.' available.',
            'errors' => ['quantity' => ['Only '.$product->stock.' '.$product->name.' available.']],
        ], 422);
    }

    private function cartResponse(?string $message = null, int $status = 200): JsonResponse
    {
        $cart = $this->cart();
        $products = Product::query()->whereIn('id', array_keys($cart))->get()->keyBy('id');

        $items = collect($cart)
            ->filter(fn ($item, $id) => $products->has($id))
            ->map(function ($item, $id) use ($products) {
                $product = $products[$id];

                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'price' => (float) $product->price,
                    'stock' => $product->stock,
                    'quantity' => $item['quantity'],
                    'subtotal' => round((float) $product->price * $item['quantity'], 2),
                ];
            })
            ->values();

        return response()->json([
            'message' => $message,
            'data' => [
                'items' => $items,
                'count' => $items->sum('quantity'),
                'total' => round($items->sum('subtotal'), 2),
            ],
        ], $status);
    }
}
