# TZ Carshop — Technical Questions (Simple Answers with Code)

Only questions about the TZ Carshop. Each answer is short and in plain words, with an easy example, the real code from the project, and the file to show.

---

## Part 1. The project

**1. What is your project?**
A shopping website where customers buy Toyota cars. They can sign up, log in, search cars, put cars in a cart, place an order, and check the order status.
*Example:* Like Lazada or Shopee, but only for Toyota cars.

**2. What did you use to build it?**
Laravel 12 (PHP) for the back-end and MySQL for the database.

**3. How many products are there, and where do they come from?**
20 Toyota cars. They are saved in the database by a seeder, not typed into the page.

*File:* `database/seeders/TzVehicleSeeder.php`

```php
[
    'name' => 'Toyota Vios',
    'year' => 2026,
    'color' => 'Silver Metallic',
    'transmission' => 'CVT',
    'fuel_type' => 'Gasoline',
    'description' => 'A dependable and efficient sedan made for everyday city driving.',
    'price' => 1092000,
    'stock' => 6,
    'image' => 'https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&w=1200&h=800&q=85',
],
// ... 19 more cars

foreach ($vehicles as $vehicle) {
    Product::updateOrCreate(
        ['name' => $vehicle['name']],
        array_merge($vehicle, [
            'category' => 'Toyota',
        ])
    );
}
```

`updateOrCreate` means running the seeder twice does not make duplicate cars.

---

## Part 2. Database

**4. How many tables does the car shop use?**
4 main tables:

| Table | What it keeps | Easy example |
|---|---|---|
| `users` | Customers and admins | The list of members |
| `products` | The 20 cars | The car showroom |
| `orders` | One row per purchase | The receipt |
| `order_items` | Each car inside a purchase | The lines on the receipt |

**5. How are the tables connected?**
One customer can have many orders. One order can have many items. Each item points to one car.
*Example:* One person → many receipts → each receipt has many lines → each line is one car.

```
users → orders → order_items → products
```

*Files:* `app/Models/User.php`, `app/Models/Order.php`, `app/Models/OrderItem.php`

```php
// User.php — one customer has many orders
public function orders(): HasMany
{
    return $this->hasMany(Order::class);
}

// Order.php — one order belongs to one customer, and has many items
public function customer(): BelongsTo
{
    return $this->belongsTo(User::class, 'user_id');
}

public function items(): HasMany
{
    return $this->hasMany(OrderItem::class);
}

// OrderItem.php — each item points to one car
public function product(): BelongsTo
{
    return $this->belongsTo(Product::class);
}
```

**6. How many migration files does the car shop have?**
4 migration files are for the shop (out of 12 in the project). All are in `database/migrations/`.

1. `2026_09_04_022132_create_products_table.php` — makes the `products` table

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('category')->default('Toyota');
    $table->unsignedSmallInteger('year')->default(2025);
    $table->string('color')->default('White');
    $table->string('transmission')->default('Automatic');
    $table->string('fuel_type')->default('Gasoline');
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->integer('stock')->default(0);
    $table->string('image')->nullable();
    $table->timestamps();
});
```

2. `2026_09_04_041500_add_tz_account_fields_to_users_table.php` — adds username, role, and status to `users`

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('username')->nullable()->unique()->after('name');
    $table->string('role')->default('customer')->after('password');
    $table->string('status')->default('active')->after('role');
});
```

3. `2026_09_04_060000_create_orders_tables.php` — makes `orders` and `order_items`

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->decimal('total', 10, 2);
    $table->string('status')->default('pending');
    $table->timestamps();
});

Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
    $table->string('product_name');
    $table->decimal('price', 10, 2);
    $table->unsignedInteger('quantity');
    $table->timestamps();
});
```

4. `2026_09_04_070000_add_received_at_to_orders_table.php` — adds the date the order was received

```php
Schema::table('orders', function (Blueprint $table) {
    $table->timestamp('received_at')->nullable()->after('status');
});
```

**7. What is a migration?**
A PHP file that builds a table. Instead of making tables by hand in MySQL, you run one command and Laravel builds all of them.
*Example:* Like a blueprint for a house. Anyone can build the same house from it.

```bash
php artisan migrate          # build the tables
php artisan migrate:status   # see which migrations ran
```

**8. What is a seeder?**
A PHP file that puts starting data into the tables.
*Example:* Stocking the showroom with cars before opening day.

```bash
php artisan db:seed --class=TzVehicleSeeder
```

**9. What is a foreign key?**
A column that points to a row in another table, like `user_id` in `orders`.
*Example:* The name on a receipt tells you which customer it belongs to.

```php
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
```

`cascadeOnDelete()` means if the customer is deleted, their orders are deleted too.

**10. Why does `order_items` save the car name and price again?**
So the receipt stays correct even if the admin later changes the car's price or deletes the car.
*Example:* A paper receipt still shows the old price, even if the store raises the price next week.

```php
$table->string('product_name');
$table->decimal('price', 10, 2);
$table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
```

`nullOnDelete()` means if the car is deleted, the item stays, and only `product_id` becomes empty.

---

## Part 3. Eloquent ORM

**11. Did you use Eloquent ORM or Query Builder?**
Eloquent ORM.

**12. What is Eloquent ORM?**
A way to work with the database using PHP objects instead of writing SQL.
*Example:* `Product::all()` gets all cars. You don't write `SELECT * FROM products`.

**13. What is the difference between Eloquent and Query Builder?**
Query Builder gives you plain rows of data. Eloquent gives you objects that know their relationships.

```php
DB::table('products')->get();   // Query Builder: plain rows
Product::all();                 // Eloquent: Product objects

$order->items;                  // Eloquent: the cars in this order, no join to write
```

**14. Why did you choose Eloquent?**
- Less code to write.
- Relationships help keep orders private: `$request->user()->orders()` only returns *my* orders.
- It is built on top of Query Builder, so the SQL is the same.

You can see the SQL it makes:

```bash
php artisan tinker
>>> App\Models\Product::where('fuel_type', 'Hybrid')->toSql();
=> "select * from `products` where `fuel_type` = ?"
```

**15. When is Query Builder better?**
For big reports or updating many rows at once, because it is faster.

**16. What is a model?**
A PHP class that stands for one table.
*Example:* The `Product` model stands for the `products` table.

*File:* `app/Models/Product.php`

```php
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'year',
        'color',
        'transmission',
        'fuel_type',
        'description',
        'price',
        'stock',
        'image',
    ];
}
```

**17. What does `$fillable` do?**
It is a list of the only columns that can be saved with `create()` or `update()`. Other fields are ignored.
*Example:* Like a form with fixed blanks. You can't add your own blank.

**18. What are casts?**
They change how a column is read. Money becomes a number with 2 decimals, and a date becomes a date object.

*File:* `app/Models/Order.php`

```php
protected function casts(): array
{
    return [
        'total' => 'decimal:2',
        'received_at' => 'datetime',
    ];
}
```

**19. What is the N+1 problem, and how did you fix it?**
N+1 means asking the database again and again, once for each order. `with('items')` gets everything in 2 queries instead.
*Example:* Buying all the groceries in one trip instead of going back to the store for each item.

*File:* `app/Http/Controllers/Api/OrderController.php`, `index()`

```php
$orders = $request->user()
    ->orders()
    ->with('items')
    ->latest()
    ->orderByDesc('id')
    ->get();
```

---

## Part 4. API

**20. What is an API?**
A set of web addresses that send and receive data (JSON) instead of web pages.
*Example:* A waiter: the page asks the waiter, and the waiter brings the data from the kitchen (the database).

**21. Why does the shop use an API?**
The exam requires it. The pages only show the design. All data, like products, cart, and orders, comes from the API.

**22. How many API routes are there?**
15, all starting with `/api/tz`.

*File:* `routes/api.php`

```php
Route::prefix('tz')->name('api.tz.')->group(function () {
    // Customer account
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('user', [AuthController::class, 'user'])->name('user');
    });

    // Products
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::middleware(['auth:sanctum', 'tz.customer'])->group(function () {
        Route::get('cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('cart', [CartController::class, 'store'])->name('cart.store');
        Route::patch('cart/{product}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::patch('orders/{order}/received', [OrderController::class, 'received'])->name('orders.received');
    });
});
```

```bash
php artisan route:list --path=api/tz
```

**23. What do GET, POST, PATCH, and DELETE mean?**

| Method | Meaning | Example in the shop |
|---|---|---|
| GET | Read | See the cars, see my orders |
| POST | Create | Add to cart, place order, log in |
| PATCH | Change | Change cart quantity, cancel order |
| DELETE | Remove | Remove a car from the cart |

**24. What is JSON?**
A simple text format for data. This is what `GET /api/tz/cart` sends back:

```json
{
  "message": null,
  "data": {
    "items": [
      { "product_id": 12, "name": "Toyota Wigo", "price": 744000, "stock": 8, "quantity": 2, "subtotal": 1488000 }
    ],
    "count": 2,
    "total": 1488000
  }
}
```

**25. How does a page call the API?**
With JavaScript `fetch()`. The page sends the request, gets JSON, and shows it.

*File:* `resources/views/pages/tz-shop.blade.php`

```js
const response = await window.tzApi(cartUrl, {
    method: 'POST',
    body: { product_id: Number(productId), quantity: 1 },
});
```

**26. What do the status codes mean?**

| Code | Simple meaning |
|---|---|
| 200 | OK |
| 201 | Created (new cart item, new order, new account) |
| 401 | You are not logged in |
| 403 | You are logged in but not allowed |
| 404 | Not found |
| 419 | Security token missing |
| 422 | Wrong input (wrong password, too many cars) |
| 429 | Too many tries, wait a minute |

**27. How does the search work?**
It looks for the word in the car's name, description, color, or fuel type. `when()` only adds the search if the customer typed something.
*Example:* Searching `Hybrid` shows all hybrid cars. Try `/api/tz/products?search=Hybrid`.

*File:* `app/Http/Controllers/Api/ProductController.php`, `index()`

```php
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
```

**28. How does "view product details" work?**
Laravel finds the car from the id in the address. If the id doesn't exist, it answers 404 by itself. This is called route model binding.

```php
// GET /api/tz/products/{product}
public function show(Product $product): JsonResponse
{
    return response()->json(['data' => $product]);
}
```

---

## Part 5. Login and security

**29. How does login work?**
1. The customer types a username or email and password.
2. The system finds the account in the database.
3. It checks the password.
4. If the account is active, the customer is logged in.

*File:* `app/Http/Controllers/Api/AuthController.php`, `login()`

```php
$user = User::query()
    ->where('username', $credentials['login'])
    ->orWhere('email', $credentials['login'])
    ->first();

if (! $user || ! Hash::check($credentials['password'], $user->password)) {
    throw ValidationException::withMessages([
        'login' => 'The supplied username or password is incorrect.',
    ]);
}

if ($user->status !== 'active') {
    throw ValidationException::withMessages([
        'login' => 'This TZ Carshop account is inactive. Please contact the administrator.',
    ]);
}

Auth::guard('web')->login($user, $request->boolean('remember'));
$request->session()->regenerate();
```

**30. How does register work?**
It checks the input, saves a new customer, and logs them in. The role is always set to `customer`, so nobody can make themselves an admin.

```php
$validated = $request->validate([
    'name' => ['required', 'string', 'max:255'],
    'username' => ['required', 'string', 'regex:/^[A-Za-z0-9._-]+$/', 'min:3', 'max:50', 'unique:users,username'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
    'password' => ['required', 'confirmed', 'min:8'],
]);

$customer = User::create([
    'name' => $validated['name'],
    'username' => $validated['username'],
    'email' => $validated['email'],
    'password' => $validated['password'],
    'role' => 'customer',
    'status' => 'active',
]);
```

**31. How are passwords saved?**
Scrambled (hashed). Even the admin cannot read them.
*Example:* Like a fingerprint. You can check if it matches, but you can't turn it back into the finger.

*File:* `app/Models/User.php`

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

**32. What is Sanctum?**
Laravel's tool for protecting the API. It checks that the person calling the API is logged in. The shop uses the normal login session, so no token is stored in the browser.

*File:* `bootstrap/app.php`

```php
// Let the customer pages call /api routes with the browser session (Sanctum SPA auth).
$middleware->statefulApi();
```

**33. Can a customer see another customer's orders?**
No. The list only gets the logged-in customer's orders (question 19). For one order, the system checks the owner and answers 403 if it's not yours.

*File:* `app/Http/Controllers/Api/OrderController.php`

```php
private function ensureOwner(Request $request, Order $order): void
{
    abort_unless($order->user_id === $request->user()->id, 403, 'This order does not belong to your account.');
}
```

**34. Can the admin use the customer cart?**
No. Only active customers can use the cart and orders. Others get 403.

*File:* `app/Http/Middleware/EnsureTzApiCustomer.php`

```php
$user = $request->user();

if (! $user || ! $user->isFureverCustomer() || $user->status !== 'active') {
    return response()->json([
        'message' => 'Only active customer accounts can use the cart and orders.',
    ], 403);
}

return $next($request);
```

**35. What is CSRF?**
A trick where a bad website sends a request using your login. Every change sends a secret token, and requests without it are blocked (419).
*Example:* Like a signature: the bank won't accept a check without it.

*File:* `resources/views/pages/partials/tz-api.blade.php`

```js
headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': @json(csrf_token()),
},
```

**36. How do you stop SQL injection?**
Eloquent keeps the search word separate from the SQL command, so the word can never run as a command. The `?` in the SQL is where the word goes in safely.

```sql
select * from `products` where `name` like ?
```

**37. How do you stop password guessing?**
Only 10 login tries per minute. After that the user must wait (429).

```php
Route::middleware('throttle:10,1')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});
```

---

## Part 6. Cart

**38. Where is the cart saved?**
In the session (the customer's visit), which Laravel saves in the `sessions` table.

**39. How does "add to cart" work, and what is saved?**
Only the car's id and the quantity are saved. If the customer asks for more than the stock, the system says no.

*File:* `app/Http/Controllers/Api/CartController.php`, `store()`

```php
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
```

**40. Why not save the price in the cart?**
The price is always read fresh from the `products` table when the cart is shown, so it can never be an old price.

```php
$products = Product::query()->whereIn('id', array_keys($cart))->get()->keyBy('id');
```

**41. What message shows when there isn't enough stock?**
For example: "Only 2 Toyota GR Supra available."

```php
return response()->json([
    'message' => 'Only '.$product->stock.' '.$product->name.' available.',
    'errors' => ['quantity' => ['Only '.$product->stock.' '.$product->name.' available.']],
], 422);
```

**42. How do update and remove work?**
Update changes the quantity (still checked against stock). Remove deletes the car from the cart.

```php
// PATCH /api/tz/cart/{product}
$cart[$product->id]['quantity'] = $validated['quantity'];
session()->put('cart', $cart);

// DELETE /api/tz/cart/{product}
unset($cart[$product->id]);
session()->put('cart', $cart);
```

**43. Why is the cart empty after logout?**
Logout clears the whole session for safety, and the cart is part of the session.

```php
Auth::guard('web')->logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
```

---

## Part 7. Checkout and orders

**44. What happens when the customer clicks Place Order?**
1. The system checks that the stock is still enough.
2. It saves the order and each car in it.
3. It lowers the stock.
4. It removes those cars from the cart.
5. It sends a confirmation email in the background.

*File:* `app/Http/Controllers/Api/OrderController.php`, `store()`

```php
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
```

**45. What is a database transaction?**
A group of saves that all happen, or none happen. That's the `DB::transaction(...)` around the code above.
*Example:* Sending money: the money leaves your account *and* reaches the other account, or nothing happens at all. It never disappears halfway.
In the shop: if one car is out of stock, the `throw` stops everything and nothing is saved.

**46. What does `lockForUpdate()` do?**
It stops two people from buying the last car at the same time. The second person waits until the first is done, then sees it's sold out.
*Example:* Only one person can hold the last item at the counter.

```php
$product = Product::query()
    ->lockForUpdate()
    ->find($productId);
```

**47. How is the total price computed?**
Price from the database × quantity, added up for each car. The customer can't change the price from the page.

```php
$total += (float) $product->price * $item['quantity'];
```

**48. What are the order statuses?**
Pending → Processing → Ready for collection → Completed. An order can also be Cancelled.

*File:* `app/Models/Order.php`

```php
public const STATUS_PENDING = 'pending';
public const STATUS_PROCESSING = 'processing';
public const STATUS_READY_FOR_COLLECTION = 'ready_for_collection';
public const STATUS_COMPLETED = 'completed';
public const STATUS_CANCELLED = 'cancelled';
```

**49. What can the customer do with an order?**
Cancel it only while it's **Pending**, and confirm "Order Received" only when it's **Ready for collection**. The admin moves the order forward.

```php
public function canBeCancelledByCustomer(): bool
{
    return $this->status === self::STATUS_PENDING;
}

public function canBeMarkedReceivedByCustomer(): bool
{
    return $this->status === self::STATUS_READY_FOR_COLLECTION;
}
```

**50. What happens to the stock when an order is cancelled?**
The cars go back to stock.

```php
$lockedOrder->restoreReservedStock();
$lockedOrder->update([
    'status' => Order::STATUS_CANCELLED,
    'received_at' => null,
]);
```

---

## Part 8. Queue

**51. What is a queue?**
A to-do list for slow jobs that run in the background.
*Example:* At a restaurant you get your receipt right away, and the kitchen cooks after. You don't stand at the counter waiting.

**52. What does the queue do in the shop?**
It sends the order confirmation email after checkout. Checkout adds the job with `SendOrderConfirmation::dispatch($order);` (question 44).

*File:* `app/Jobs/SendOrderConfirmation.php`

```php
class SendOrderConfirmation implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function handle(): void
    {
        $order = $this->order->load(['customer', 'items']);

        // ... builds the email text: order number, cars, total, status

        Mail::raw($body, function ($message) use ($order) {
            $message->to($order->customer->email)
                ->subject('TZ Carshop Order #'.$order->id.' Confirmation');
        });
    }
}
```

**53. How do you run the queue?**

```bash
php artisan queue:work
```

**54. Where can you see the email?**
In `storage/logs/laravel.log`, because email is set to save into the log file instead of really sending (`MAIL_MAILER=log` in `.env`).

**55. What if the queue worker is off?**
The order is still saved. The email just waits in the `jobs` table until the worker runs.

---

## Part 9. Testing

**56. How did you test the shop?**
With 55 automatic tests. They check login, search, cart, checkout, privacy, and the queue.

```bash
php artisan test
```

**57. Do the tests change the real database?**
No. Tests use a separate empty database in memory that is reset for each test (`RefreshDatabase`).

**58. Give one example of a test.**
"When a customer checks out 2 cars, an order is saved, the stock goes down by 2, and the email job is queued."

*File:* `tests/Feature/TzCustomerApiTest.php`

```php
public function test_customer_checkout_saves_order_and_items_reduces_stock_and_queues_confirmation(): void
{
    Queue::fake();

    $customer = $this->customer();
    $product = $this->vehicle(['price' => 199.50, 'stock' => 4]);

    $response = $this->actingAs($customer)
        ->withSession(['cart' => [$product->id => ['id' => $product->id, 'quantity' => 2]]])
        ->postJson(route('api.tz.orders.store'), ['selected_items' => [$product->id]]);

    $response->assertCreated()
        ->assertJsonPath('data.total', 399)
        ->assertJsonPath('data.status', Order::STATUS_PENDING)
        ->assertJsonPath('data.items.0.quantity', 2);

    $this->assertDatabaseHas('orders', ['user_id' => $customer->id, 'total' => 399.00, 'status' => 'pending']);
    $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'product_name' => 'Toyota Vios', 'quantity' => 2]);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 2]);
    $response->assertSessionMissing('cart.'.$product->id);

    Queue::assertPushed(SendOrderConfirmation::class, fn ($job) => $job->order->id === $response->json('data.id'));
}
```

`Queue::fake()` catches the job instead of running it, so the test can check it was sent to the queue.
