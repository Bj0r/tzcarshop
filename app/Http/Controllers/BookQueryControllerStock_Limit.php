<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Book;

// Part F version of BookQueryController.
// Differences from BookQueryController are marked with "DIFFERENT:".
class BookQueryControllerStock_Limit extends Controller
{
    public function index(Request $request)
    {
        // DIFFERENT: validate the threshold before using it.
        // nullable = box can be empty, integer = whole number only,
        // between:0,1000 = reasonable range. Invalid input returns to the
        // page with an error, so the query never runs with a bad value.
        $validated = $request->validate([
            'stock_limit' => 'nullable|integer|between:0,1000',
        ]);

        $search = $request->input('search', '');
        $lowStock = $request->boolean('low_stock');
        // DIFFERENT: new variable; uses 10 (the original fixed value) when empty
        $stockLimit = (int) ($validated['stock_limit'] ?? 10);

        // Laravel Query Builder
        $queryBuilderBooks = DB::table('books')
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            // DIFFERENT: was where('stock', '<', 10).
            // $stockLimit is passed through where(), so Laravel sends it as a
            // binding (stock < ?). User input is never concatenated into raw SQL.
            ->when($lowStock, function ($query) use ($stockLimit) {
                $query->where('stock', '<', $stockLimit);
            })
            ->orderBy('amount')
            ->limit(20)
            ->get();

        // Eloquent ORM
        $eloquentBooks = Book::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            // DIFFERENT: same validated $stockLimit as the Query Builder query
            ->when($lowStock, function ($query) use ($stockLimit) {
                $query->where('stock', '<', $stockLimit);
            })
            ->orderBy('amount')
            ->limit(20)
            ->get();

        // DIFFERENT: new view name, and 'stockLimit' is also sent to the view
        return view('pages.book-query-comparison-stock-limit', compact(
            'search', 'lowStock', 'stockLimit',
            'queryBuilderBooks', 'eloquentBooks'
        ));
    }
}
