<?php

namespace Tests\Feature;

use App\Jobs\SendOrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TzCustomerApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Same-origin browser requests send a Referer, which makes Sanctum use the session.
        $this->withHeader('Referer', 'http://localhost');
    }

    private function customer(): User
    {
        return User::factory()->create(['role' => 'customer', 'status' => 'active']);
    }

    private function vehicle(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Toyota Vios',
            'category' => 'Toyota',
            'year' => 2026,
            'color' => 'Silver Metallic',
            'transmission' => 'CVT',
            'fuel_type' => 'Gasoline',
            'description' => 'A dependable city sedan.',
            'price' => 1092000,
            'stock' => 5,
        ], $attributes));
    }

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    public function test_products_api_lists_products_from_the_database(): void
    {
        $this->vehicle(['name' => 'Toyota Vios']);
        $this->vehicle(['name' => 'Toyota Hilux', 'fuel_type' => 'Diesel', 'transmission' => 'Automatic']);

        $this->getJson(route('api.tz.products.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('filters.fuel_types', ['Diesel', 'Gasoline'])
            ->assertJsonPath('filters.transmissions', ['Automatic', 'CVT']);
    }

    public function test_products_api_searches_and_filters(): void
    {
        $this->vehicle(['name' => 'Toyota Vios']);
        $this->vehicle(['name' => 'Toyota Hilux', 'color' => 'Emotional Red', 'fuel_type' => 'Diesel']);

        $this->getJson(route('api.tz.products.index', ['search' => 'Red']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Toyota Hilux');

        $this->getJson(route('api.tz.products.index', ['fuel_type' => 'Gasoline']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Toyota Vios');
    }

    public function test_products_api_shows_product_details(): void
    {
        $product = $this->vehicle();

        $this->getJson(route('api.tz.products.show', $product))
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', 'Toyota Vios')
            ->assertJsonPath('data.description', 'A dependable city sedan.');

        $this->getJson(route('api.tz.products.show', 999))->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | Cart
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_use_the_cart_api(): void
    {
        $product = $this->vehicle();

        $this->getJson(route('api.tz.cart.index'))->assertUnauthorized();
        $this->postJson(route('api.tz.cart.store'), ['product_id' => $product->id])->assertUnauthorized();
        $this->postJson(route('api.tz.orders.store'), ['selected_items' => [$product->id]])->assertUnauthorized();
    }

    public function test_api_answers_in_json_when_opened_in_a_browser_tab(): void
    {
        // A browser address bar asks for HTML, not JSON.
        $this->get(route('api.tz.orders.index'), ['Accept' => 'text/html'])
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);

        $this->get(route('api.tz.products.show', 999), ['Accept' => 'text/html'])
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_admin_cannot_use_the_customer_cart_api(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($admin)
            ->getJson(route('api.tz.cart.index'))
            ->assertForbidden();
    }

    public function test_customer_can_add_update_and_remove_cart_items(): void
    {
        $customer = $this->customer();
        $product = $this->vehicle(['price' => 1000, 'stock' => 3]);

        $this->actingAs($customer)
            ->postJson(route('api.tz.cart.store'), ['product_id' => $product->id])
            ->assertCreated()
            ->assertJsonPath('data.count', 1)
            ->assertJsonPath('data.items.0.product_id', $product->id)
            ->assertJsonPath('data.items.0.quantity', 1);

        $this->actingAs($customer)
            ->patchJson(route('api.tz.cart.update', $product), ['quantity' => 3])
            ->assertOk()
            ->assertJsonPath('data.items.0.quantity', 3)
            ->assertJsonPath('data.total', 3000);

        $this->actingAs($customer)
            ->getJson(route('api.tz.cart.index'))
            ->assertOk()
            ->assertJsonPath('data.count', 3);

        $this->actingAs($customer)
            ->deleteJson(route('api.tz.cart.destroy', $product))
            ->assertOk()
            ->assertJsonPath('data.count', 0)
            ->assertJsonCount(0, 'data.items');
    }

    public function test_cart_rejects_quantities_above_stock(): void
    {
        $customer = $this->customer();
        $product = $this->vehicle(['stock' => 2]);

        $this->actingAs($customer)
            ->postJson(route('api.tz.cart.store'), ['product_id' => $product->id, 'quantity' => 3])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');

        $this->actingAs($customer)
            ->postJson(route('api.tz.cart.store'), ['product_id' => 999])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('product_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Checkout
    |--------------------------------------------------------------------------
    */

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

    public function test_customer_can_checkout_only_selected_cart_products(): void
    {
        Queue::fake();

        $customer = $this->customer();
        $selected = $this->vehicle(['name' => 'Toyota Raize', 'price' => 150, 'stock' => 5]);
        $saved = $this->vehicle(['name' => 'Toyota Wigo', 'price' => 80, 'stock' => 4]);

        $response = $this->actingAs($customer)
            ->withSession(['cart' => [
                $selected->id => ['id' => $selected->id, 'quantity' => 2],
                $saved->id => ['id' => $saved->id, 'quantity' => 1],
            ]])
            ->postJson(route('api.tz.orders.store'), ['selected_items' => [$selected->id]]);

        $response->assertCreated();
        $this->assertDatabaseHas('orders', ['user_id' => $customer->id, 'total' => 300.00]);
        $this->assertDatabaseMissing('order_items', ['product_id' => $saved->id]);
        $this->assertDatabaseHas('products', ['id' => $selected->id, 'stock' => 3]);
        $this->assertDatabaseHas('products', ['id' => $saved->id, 'stock' => 4]);
        $response->assertSessionMissing('cart.'.$selected->id);
        $response->assertSessionHas('cart.'.$saved->id.'.quantity', 1);
    }

    public function test_checkout_requires_a_selected_cart_product(): void
    {
        Queue::fake();

        $customer = $this->customer();
        $product = $this->vehicle(['stock' => 3]);

        $this->actingAs($customer)
            ->withSession(['cart' => [$product->id => ['id' => $product->id, 'quantity' => 1]]])
            ->postJson(route('api.tz.orders.store'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('selected_items');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
        Queue::assertNothingPushed();
    }

    public function test_checkout_rolls_back_when_stock_is_too_low(): void
    {
        Queue::fake();

        $customer = $this->customer();
        $enough = $this->vehicle(['name' => 'Toyota Raize', 'stock' => 5]);
        $tooLow = $this->vehicle(['name' => 'Toyota Hilux', 'stock' => 1]);

        $this->actingAs($customer)
            ->withSession(['cart' => [
                $enough->id => ['id' => $enough->id, 'quantity' => 1],
                $tooLow->id => ['id' => $tooLow->id, 'quantity' => 2],
            ]])
            ->postJson(route('api.tz.orders.store'), ['selected_items' => [$enough->id, $tooLow->id]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseHas('products', ['id' => $enough->id, 'stock' => 5]);
        Queue::assertNothingPushed();
    }

    public function test_order_confirmation_job_emails_the_customer(): void
    {
        $customer = $this->customer();
        $order = Order::create(['user_id' => $customer->id, 'total' => 1000, 'status' => Order::STATUS_PENDING]);
        $order->items()->create(['product_name' => 'Toyota Vios', 'price' => 1000, 'quantity' => 1]);

        (new SendOrderConfirmation($order))->handle();

        // phpunit.xml uses the "array" mailer, which keeps sent messages in memory.
        $messages = Mail::mailer()->getSymfonyTransport()->messages();

        $this->assertCount(1, $messages);
        $email = $messages->first()->getOriginalMessage();
        $this->assertSame($customer->email, $email->getTo()[0]->getAddress());
        $this->assertSame('TZ Carshop Order #'.$order->id.' Confirmation', $email->getSubject());
        $this->assertStringContainsString('1 x Toyota Vios', $email->getTextBody());
    }

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    public function test_customer_sees_only_their_own_orders(): void
    {
        $customer = $this->customer();
        $other = $this->customer();
        $mine = Order::create(['user_id' => $customer->id, 'total' => 199.50, 'status' => Order::STATUS_PENDING]);
        $theirs = Order::create(['user_id' => $other->id, 'total' => 499.50, 'status' => Order::STATUS_PENDING]);

        $this->actingAs($customer)
            ->getJson(route('api.tz.orders.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $mine->id)
            ->assertJsonPath('data.0.can_cancel', true);

        $this->actingAs($customer)
            ->getJson(route('api.tz.orders.show', $mine))
            ->assertOk()
            ->assertJsonPath('data.id', $mine->id);

        $this->actingAs($customer)
            ->getJson(route('api.tz.orders.show', $theirs))
            ->assertForbidden();
    }

    public function test_customer_can_mark_a_ready_order_received(): void
    {
        $customer = $this->customer();
        $order = Order::create(['user_id' => $customer->id, 'total' => 199.50, 'status' => Order::STATUS_READY_FOR_COLLECTION]);

        $this->actingAs($customer)
            ->patchJson(route('api.tz.orders.received', $order))
            ->assertOk()
            ->assertJsonPath('data.status', Order::STATUS_COMPLETED);

        $this->assertNotNull($order->refresh()->received_at);
    }

    public function test_cancelling_a_pending_order_restores_stock_once(): void
    {
        $customer = $this->customer();
        $product = $this->vehicle(['stock' => 0]);
        $order = Order::create(['user_id' => $customer->id, 'total' => 150, 'status' => Order::STATUS_PENDING]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'price' => 75, 'quantity' => 2]);

        $this->actingAs($customer)
            ->patchJson(route('api.tz.orders.cancel', $order))
            ->assertOk()
            ->assertJsonPath('data.status', Order::STATUS_CANCELLED);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 2]);

        $this->actingAs($customer)
            ->patchJson(route('api.tz.orders.cancel', $order))
            ->assertUnprocessable();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 2]);
    }

    public function test_customer_can_only_confirm_ready_orders_and_cancel_pending_orders(): void
    {
        $customer = $this->customer();
        $order = Order::create(['user_id' => $customer->id, 'total' => 199.50, 'status' => Order::STATUS_PROCESSING]);

        $this->actingAs($customer)->patchJson(route('api.tz.orders.received', $order))->assertUnprocessable();
        $this->actingAs($customer)->patchJson(route('api.tz.orders.cancel', $order))->assertUnprocessable();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => Order::STATUS_PROCESSING]);
    }

    public function test_customer_cannot_change_someone_elses_order(): void
    {
        $customer = $this->customer();
        $other = $this->customer();
        $pending = Order::create(['user_id' => $other->id, 'total' => 199.50, 'status' => Order::STATUS_PENDING]);
        $ready = Order::create(['user_id' => $other->id, 'total' => 199.50, 'status' => Order::STATUS_READY_FOR_COLLECTION]);

        $this->actingAs($customer)->patchJson(route('api.tz.orders.cancel', $pending))->assertForbidden();
        $this->actingAs($customer)->patchJson(route('api.tz.orders.received', $ready))->assertForbidden();

        $this->assertDatabaseHas('orders', ['id' => $pending->id, 'status' => Order::STATUS_PENDING]);
        $this->assertDatabaseHas('orders', ['id' => $ready->id, 'status' => Order::STATUS_READY_FOR_COLLECTION]);
    }

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    public function test_customer_pages_render_and_call_the_api(): void
    {
        $customer = $this->customer();

        $this->get(route('furever.shop'))
            ->assertOk()
            ->assertSee(json_encode(route('api.tz.products.index'), 15), false);

        $this->actingAs($customer)
            ->get(route('furever.cart'))
            ->assertOk()
            ->assertSee(json_encode(route('api.tz.cart.index'), 15), false);

        $this->actingAs($customer)
            ->get(route('furever.customer-orders'))
            ->assertOk()
            ->assertSee(json_encode(route('api.tz.orders.index'), 15), false);
    }
}
