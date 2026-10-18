# Worksheet 4.1 — Part D: Compare the Two Approaches

Page tested: `/book-query-comparison`
Both columns run the same filter, the same sort (`amount`, lowest to highest) and the same limit (20).

## Question 6 — Did both approaches return the same records in all five tests? If not, identify the difference and investigate its cause.

Answer: Yes. In all five tests (all books, title search, low stock, combined filter, reset) both columns returned the same records in the same order, and the "Records" count badge was equal on both sides. For example, searching for "dig" gave the same 3 books in both columns, with the same stock and price.

This is expected. The two queries use the same `where` conditions, the same `orderBy('amount')` and the same `limit(20)`, and both end in SQL that MySQL runs the same way. If the results ever differed, the first thing to check would be that one chain has a condition, sort or limit that the other does not.

## Question 7 — Which parts of the code are similar, and which differ?

Answer:

Similar (identical in both chains):
- `->when($search, ...)` with `where('title', 'like', '%' . $search . '%')`
- `->when($lowStock, ...)` with `where('stock', '<', $stockLimit)`
- `->orderBy('amount')`
- `->limit(20)`
- `->get()`

Different:
- The starting point. Query Builder starts with `DB::table('books')`. Eloquent starts with `Book::query()`.
- The type of each returned row. Query Builder returns plain `stdClass` objects. Eloquent returns `Book` model objects.
- The extra features. Only Eloquent rows can use model features such as relationships, casts, accessors and `$book->update()` or `$book->delete()`.

## Question 8 — Why does the Query Builder example not require a Book model, while the Eloquent example does?

Answer: Query Builder works directly on a table name. `DB::table('books')` tells it which table to query, and it builds SQL from that. It does not know or care what the table represents in the application.

Eloquent is an ORM (object-relational mapper). Each model class maps to one table, and each row becomes an object of that class. `Book::query()` needs the `Book` model so Eloquent knows which table to use (`books`, from the class name) and which class to turn the rows into.

## Question 9 — Does Book::all() automatically make a query faster than DB::table('books')->get()? Explain.

Answer: No. Both send essentially the same SQL to the database: `select * from books`. The database does the same work either way, so the query itself is not faster.

Eloquent is usually slightly slower in PHP, because it has to build a `Book` model object for every row (attributes, casts, state tracking), while Query Builder only returns plain objects. With a small table the difference is not noticeable. With very large result sets, Query Builder can use less memory and time. Speed depends on the SQL, indexes and the number of rows returned, not on which tool wrote the query.

## Question 10 — Give one situation where you would prefer Eloquent and one where Query Builder may be suitable.

Answer:

Prefer Eloquent when the data is a normal part of the application and you work with whole records: creating, updating and deleting a book, using relationships (for example a book that belongs to a country), or using casts and accessors. The Books CRUD page is a good example, because the model keeps the code short and readable.

Query Builder may be suitable when you need a quick or complex query that does not map neatly to a model: reports with joins and aggregates (for example total stock by country), bulk updates, or reading a large amount of data where building a model object for every row would be wasteful.
