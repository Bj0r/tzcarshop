<?php 
 
namespace App\Http\Controllers; 
 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB; 
use App\Models\Book; 
 
class BookQueryController extends Controller 
{ 
    public function index(Request $request) 
    { 
        $search = $request->input('search', ''); 
        $lowStock = $request->boolean('low_stock'); 
 
        // Laravel Query Builder 
        $queryBuilderBooks = DB::table('books') 
            ->when($search, function ($query, $search) { 
                $query->where('title', 'like', '%' . $search . '%'); 
            }) 
            ->when($lowStock, function ($query) { 
                $query->where('stock', '<', 10); 
            }) 
            ->orderBy('amount') 
            ->limit(20) 
            ->get(); 
 
        // Eloquent ORM 
        $eloquentBooks = Book::query() 
            ->when($search, function ($query, $search) { 
                $query->where('title', 'like', '%' . $search . '%'); 
            }) 
            ->when($lowStock, function ($query) { 
                $query->where('stock', '<', 10); 
            }) 
            ->orderBy('amount') 
            ->limit(20) 
            ->get(); 
 
        return view('pages.book-query-comparison', compact( 
            'search', 'lowStock', 
            'queryBuilderBooks', 'eloquentBooks' 
        )); 
    } 
} 