<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /api/tz/products
     * List products with optional search, filters, and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'transmission' => ['nullable', 'string', 'max:50'],
            'fuel_type' => ['nullable', 'string', 'max:50'],
            'sort' => ['nullable', 'in:latest,name,price_low,price_high'],
        ]);

        $query = Product::query()
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhere('color', 'like', '%'.$search.'%')
                        ->orWhere('fuel_type', 'like', '%'.$search.'%');
                });
            })
            ->when($validated['transmission'] ?? null, fn ($query, $transmission) => $query->where('transmission', $transmission))
            ->when($validated['fuel_type'] ?? null, fn ($query, $fuelType) => $query->where('fuel_type', $fuelType));

        match ($validated['sort'] ?? 'latest') {
            'name' => $query->orderBy('name'),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            default => $query->latest()->orderByDesc('id'),
        };

        return response()->json([
            'data' => $query->get(),
            'filters' => [
                'transmissions' => Product::query()->distinct()->orderBy('transmission')->pluck('transmission'),
                'fuel_types' => Product::query()->distinct()->orderBy('fuel_type')->pluck('fuel_type'),
            ],
        ]);
    }

    /**
     * GET /api/tz/products/{product}
     * Show one product's details.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $product]);
    }
}
