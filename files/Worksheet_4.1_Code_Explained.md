# Worksheet 4.1 — The Code Explained

This explains the three pieces that make the Query Comparison page work. Open the page at `/book-query-comparison`.

## How the pieces connect

1. The browser asks for `/book-query-comparison`.
2. The route in `routes/web.php` sends the request to `BookQueryController`, method `index`.
3. The controller reads and checks the form values, runs the two queries, and hands the results to the view.
4. The view `pages/book-query-comparison.blade.php` draws the form and the two result columns.

## 1. The route — `routes/web.php`

```php
Route::get('/book-query-comparison', [BookQueryController::class, 'index']);
```

When someone opens that URL with a GET request, Laravel calls the `index` method of `BookQueryController`. The `use App\Http\Controllers\BookQueryController;` line at the top of the file lets the route name the class without its full path.

## 2. The controller — `app/Http/Controllers/BookQueryController.php`

### Reading and validating the form

```php
$validated = $request->validate([
    'search' => 'nullable|string|max:255',
    'stock_limit' => 'nullable|integer|between:0,1000',
]);
```

- `search` is optional text, up to 255 characters.
- `stock_limit` is an optional whole number from 0 to 1000. If someone types 5000 or "abc", Laravel sends them back to the page with an error message instead of running the query. This is the safety step the worksheet asks for in the final challenge.

```php
$search = $validated['search'] ?? '';
$lowStock = $request->boolean('low_stock');
$stockLimit = (int) ($validated['stock_limit'] ?? 10);
```

- `$search` is the title text, or an empty string if nothing was typed.
- `$lowStock` is true when the "Below threshold" checkbox is ticked.
- `$stockLimit` is the threshold number. If the box is empty it defaults to 10, which is the worksheet's original value.

### The Query Builder query

```php
$queryBuilderBooks = DB::table('books')
    ->when($search, function ($query) use ($search) {
        $query->where('title', 'like', '%' . $search . '%');
    })
    ->when($lowStock, function ($query) use ($stockLimit) {
        $query->where('stock', '<', $stockLimit);
    })
    ->orderBy('amount')
    ->limit(20)
    ->get();
```

- `DB::table('books')` starts a query on the `books` table directly.
- `->when($search, ...)` adds the inner condition only if `$search` is not empty. Without it, the filter is skipped and all titles are allowed.
- `where('title', 'like', '%dig%')` matches any title that contains the typed word. The `%` signs mean "anything before or after".
- `->when($lowStock, ...)` adds `stock < threshold` only when the checkbox is ticked.
- `->orderBy('amount')` sorts by price, lowest first.
- `->limit(20)` returns at most 20 rows.
- `->get()` runs the query and returns the rows as plain objects.

User input is never pasted into raw SQL. The values are passed through `where(...)`, and Laravel sends them to MySQL as separate bindings, which protects against SQL injection.

### The Eloquent query

```php
$eloquentBooks = Book::query()
    ->when(...same conditions...)
    ->orderBy('amount')
    ->limit(20)
    ->get();
```

It is the same chain, but it starts with `Book::query()`. That uses the `Book` model in `app/Models/Book.php`, so the rows come back as `Book` objects instead of plain objects. The chain is kept identical on purpose so the comparison is fair.

### Sending the data to the page

```php
return view('pages.book-query-comparison', compact(
    'search', 'lowStock', 'stockLimit', 'queryBuilderBooks', 'eloquentBooks'
));
```

`compact(...)` bundles those variables so the view can use them by the same names.

## 3. The view — `resources/views/pages/book-query-comparison.blade.php`

- `@extends('layouts.app')` and `@section('content')` put the page inside the main layout (sidebar and top bar).
- The form uses `method="GET"` and posts to the same URL, so the search values appear in the address bar and the page reloads with the results.
  - `name="search"` is the title box.
  - `name="stock_limit"` is the number box (`min="0" max="1000"`).
  - `name="low_stock"` is the checkbox. `{{ $lowStock ? 'checked' : '' }}` keeps it ticked after searching.
  - The Search button submits the form. The Reset link goes back to the plain URL, which clears every filter.
- `@error('stock_limit')` shows the validation message under the number box if the value was invalid.
- The blue info bar shows the selected threshold and whether stock filtering is on.
- The two columns each loop with `@forelse($queryBuilderBooks as $book)` and `@forelse($eloquentBooks as $book)`. Each book is drawn as a card with the title, stock and price. If the list is empty, `@empty` shows "No books found."
- `{{ $queryBuilderBooks->count() }} Records` is the badge that lets you compare how many rows each side returned.
- `number_format($book->amount, 2)` shows the price with two decimals.

Because both loops print `$book->title`, `$book->stock` and `$book->amount`, the two columns look identical. That is the point of the worksheet: the same result from two different ways of querying.

## 4. The seeder — `database/seeders/BookSeeder.php`

The page needs book records to show. `BookSeeder` creates 100 books with random titles, descriptions, stock and prices (from `BookFactory`), then writes a small SVG cover image for each book into `public/_uploads/` and saves its file name in the `photo` column. Run it with:

```
php artisan db:seed --class=BookSeeder
```

Do not run `migrate:fresh` for this worksheet, because it deletes existing tables and records.

## 5. Part E — seeing the SQL (optional extra)

To see what Eloquent really sends to MySQL, temporarily replace the Eloquent block in the controller with:

```php
$eloquentQuery = Book::query()
    ->when($search, function ($query) use ($search) {
        $query->where('title', 'like', '%' . $search . '%');
    })
    ->when($lowStock, function ($query) use ($stockLimit) {
        $query->where('stock', '<', $stockLimit);
    })
    ->orderBy('amount')
    ->limit(20);

dd($eloquentQuery->toSql(), $eloquentQuery->getBindings());
```

- `toSql()` prints the SQL template, with `?` where the values go.
- `getBindings()` prints the values that fill the `?` placeholders (for example `%dig%` and the stock number).

Take your screenshot, then remove the `dd(...)` and restore the original Eloquent query. Never leave `dd()` in a deployed application.
