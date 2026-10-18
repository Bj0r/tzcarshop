<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookQueryController extends Controller
{
    public function index(Request $request)
    {
        // Validate user input
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'stock_limit' => 'nullable|integer|between:0,1000',
        ]);

        // Get search and filter values
        $search = $validated['search'] ?? '';
        $lowStock = $request->boolean('low_stock');
        $stockLimit = (int) ($validated['stock_limit'] ?? 10);

        // ==================================
        // Laravel Query Builder
        // ==================================

        $queryBuilderBooks = DB::table('books')
            ->when($search, function ($query) use ($search) {
                $query->where(
                    'title',
                    'like',
                    '%' . $search . '%'
                );
            })
            ->when($lowStock, function ($query) use ($stockLimit) {
                $query->where('stock', '<', $stockLimit);
            })
            ->orderBy('amount')
            ->limit(20)
            ->get();

        // ==================================
        // Eloquent ORM
        // ==================================

        $eloquentBooks = Book::query()
            ->when($search, function ($query) use ($search) {
                $query->where(
                    'title',
                    'like',
                    '%' . $search . '%'
                );
            })
            ->when($lowStock, function ($query) use ($stockLimit) {
                $query->where('stock', '<', $stockLimit);
            })
            ->orderBy('amount')
            ->limit(20)
            ->get();

        // ==================================
        // Return comparison page
        // ==================================

        return view('pages.book-query-comparison', compact(
            'search',
            'lowStock',
            'stockLimit',
            'queryBuilderBooks',
            'eloquentBooks'
        ));
    }
}
