<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TzOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_toyota_vehicle(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(route('furever.products.store'), [
            'name' => 'Toyota Raize',
            'year' => 2026,
            'price' => 1063000,
            'color' => 'Scarlet SE',
            'transmission' => 'CVT',
            'fuel_type' => 'Gasoline',
            'stock' => 3,
            'image_url' => 'https://example.com/toyota-raize.jpg',
            'description' => 'A compact crossover for city driving.',
        ]);

        $response->assertRedirect(route('furever.products'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('products', [
            'name' => 'Toyota Raize',
            'category' => 'Toyota',
            'year' => 2026,
            'color' => 'Scarlet SE',
            'transmission' => 'CVT',
            'fuel_type' => 'Gasoline',
            'stock' => 3,
            'image' => 'https://example.com/toyota-raize.jpg',
        ]);
    }

    public function test_inventory_rejects_a_non_toyota_model(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('furever.products.create'))
            ->post(route('furever.products.store'), [
                'name' => 'Honda Civic',
                'year' => 2026,
                'price' => 1583000,
                'color' => 'Red',
                'transmission' => 'CVT',
                'fuel_type' => 'Gasoline',
                'stock' => 1,
            ]);

        $response->assertRedirect(route('furever.products.create'));
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_can_update_a_toyota_vehicle(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
        $vehicle = Product::create([
            'name' => 'Toyota Vios',
            'category' => 'Toyota',
            'year' => 2024,
            'price' => 732000,
            'color' => 'Silver Metallic',
            'transmission' => 'Manual',
            'fuel_type' => 'Gasoline',
            'stock' => 2,
        ]);

        $response = $this->actingAs($admin)->put(
            route('furever.products.update', $vehicle),
            [
                'name' => 'Toyota Corolla Cross',
                'year' => 2026,
                'price' => 1917000,
                'color' => 'Metal Stream',
                'transmission' => 'CVT',
                'fuel_type' => 'Hybrid',
                'stock' => 4,
                'image_url' => 'https://example.com/corolla-cross.jpg',
                'description' => 'An efficient hybrid crossover.',
            ]
        );

        $response->assertRedirect(route('furever.products'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('products', [
            'id' => $vehicle->id,
            'name' => 'Toyota Corolla Cross',
            'year' => 2026,
            'color' => 'Metal Stream',
            'transmission' => 'CVT',
            'fuel_type' => 'Hybrid',
            'stock' => 4,
            'image' => 'https://example.com/corolla-cross.jpg',
        ]);
    }

    public function test_inventory_renders_the_tz_carshop_vehicle_carousel(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
        Product::create([
            'name' => 'Toyota Hilux',
            'category' => 'Toyota',
            'year' => 2026,
            'price' => 2076000,
            'color' => 'Emotional Red',
            'transmission' => 'Automatic',
            'fuel_type' => 'Diesel',
            'stock' => 5,
            'description' => 'A dependable pickup for work and weekends.',
        ]);

        $this->actingAs($admin)
            ->get(route('furever.products'))
            ->assertOk()
            ->assertSee('TZ Carshop')
            ->assertSee('Choose Your Toyota')
            ->assertSee('Toyota Hilux')
            ->assertSee('data-car-carousel', false)
            ->assertSee('data-carousel-previous', false)
            ->assertSee('data-carousel-next', false)
            ->assertSee('View Details');
    }

    public function test_create_vehicle_form_shows_all_inventory_fields(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('furever.products.create'))
            ->assertOk()
            ->assertSee('Add New Car')
            ->assertSee('Model name')
            ->assertSee('name="year"', false)
            ->assertSee('name="color"', false)
            ->assertSee('name="transmission"', false)
            ->assertSee('name="fuel_type"', false)
            ->assertSee('name="image_url"', false);
    }

    public function test_public_showroom_lists_toyota_inventory_under_tz_carshop_branding(): void
    {
        Product::create([
            'name' => 'Toyota Fortuner',
            'category' => 'Toyota',
            'year' => 2026,
            'price' => 2650000,
            'color' => 'Attitude Black',
            'transmission' => 'Automatic',
            'fuel_type' => 'Diesel',
            'stock' => 2,
        ]);

        $this->get(route('furever'))
            ->assertOk()
            ->assertSee('TZ Carshop')
            ->assertSee('Choose Your Toyota')
            ->assertSee('Toyota Fortuner')
            ->assertSee('Attitude Black')
            ->assertDontSee('Pet boutique');
    }

    public function test_public_showroom_renders_premium_home_sections_with_live_vehicle_data(): void
    {
        Product::create([
            'name' => 'Toyota Camry',
            'category' => 'Toyota',
            'year' => 2026,
            'price' => 2651000,
            'color' => 'Attitude Black Mica',
            'transmission' => 'CVT',
            'fuel_type' => 'Hybrid',
            'stock' => 3,
            'image' => 'https://images.unsplash.com/photo-toyota-camry',
        ]);

        $this->get(route('furever'))
            ->assertSee('css/tz-home.css', false)
            ->assertSee('Drive Your Next Chapter')
            ->assertSee('Find Your Perfect Ride')
            ->assertSee('Why Choose TZ Carshop')
            ->assertSee('A Simple Process')
            ->assertSee('Your Next Adventure Is Waiting')
            ->assertSee('class="tz-home-benefits__vehicle"', false)
            ->assertSee('Toyota Camry')
            ->assertSee('Attitude Black Mica');
    }

    public function test_vehicle_browse_route_uses_the_tz_carshop_showroom(): void
    {
        $this->get(route('furever.shop'))
            ->assertOk()
            ->assertSee('TZ Carshop')
            ->assertSee('Choose Your Toyota')
            ->assertDontSee('Shop by category');
    }

    public function test_inventory_counts_distinct_vehicle_transmissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        foreach (['Manual', 'CVT'] as $transmission) {
            Product::create([
                'name' => $transmission === 'Manual' ? 'Toyota Wigo' : 'Toyota Raize',
                'category' => 'Toyota',
                'transmission' => $transmission,
                'price' => 900000,
                'stock' => 1,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('furever.products'))
            ->assertOk()
            ->assertSee('data-total-transmissions>2<', false);
    }

    public function test_inventory_search_matches_vehicle_color_and_fuel_type(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        Product::create(['name' => 'Toyota Hilux', 'category' => 'Toyota', 'color' => 'Bronze Oxide', 'fuel_type' => 'Diesel', 'price' => 1500000, 'stock' => 1]);
        Product::create(['name' => 'Toyota Vios', 'category' => 'Toyota', 'color' => 'Super White', 'fuel_type' => 'Gasoline', 'price' => 800000, 'stock' => 1]);

        $this->actingAs($admin)
            ->get(route('furever.products', ['search' => 'Bronze']))
            ->assertOk()
            ->assertSee('Toyota Hilux')
            ->assertDontSee('Toyota Vios');

        $this->actingAs($admin)
            ->get(route('furever.products', ['search' => 'Gasoline']))
            ->assertOk()
            ->assertSee('Toyota Vios')
            ->assertDontSee('Toyota Hilux');
    }

    public function test_admin_can_delete_a_vehicle_from_the_inventory(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $vehicle = Product::create([
            'name' => 'Toyota Innova',
            'category' => 'Toyota',
            'price' => 1600000,
            'stock' => 1,
            'image' => 'https://example.com/innova.jpg',
        ]);

        $this->actingAs($admin)
            ->delete(route('furever.products.destroy', $vehicle))
            ->assertRedirect(route('furever.products'))
            ->assertSessionHas('success', 'Car deleted successfully!');

        $this->assertDatabaseMissing('products', ['id' => $vehicle->id]);
    }

    public function test_customer_checkout_creates_an_order_and_reduces_stock(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Travel Bowl',
            'category' => 'Accessories',
            'description' => 'A collapsible bowl.',
            'price' => 199.50,
            'stock' => 4,
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => 2,
                        'stock' => $product->stock,
                    ],
                ],
            ])
            ->post(route('furever.checkout'), [
                'selected_items' => [$product->id],
            ]);

        $response->assertRedirect(route('furever.customer-orders'));
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'total' => 399.00,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'product_name' => 'Travel Bowl',
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 2,
        ]);
    }

    public function test_customer_can_checkout_only_selected_cart_products(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $selectedProduct = Product::create([
            'name' => 'Selected Toy',
            'category' => 'Toys',
            'price' => 150.00,
            'stock' => 5,
        ]);
        $savedProduct = Product::create([
            'name' => 'Saved Treat',
            'category' => 'Food',
            'price' => 80.00,
            'stock' => 4,
        ]);

        $cart = [
            $selectedProduct->id => [
                'id' => $selectedProduct->id,
                'name' => $selectedProduct->name,
                'price' => $selectedProduct->price,
                'quantity' => 2,
                'stock' => $selectedProduct->stock,
            ],
            $savedProduct->id => [
                'id' => $savedProduct->id,
                'name' => $savedProduct->name,
                'price' => $savedProduct->price,
                'quantity' => 1,
                'stock' => $savedProduct->stock,
            ],
        ];

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post(route('furever.checkout'), [
                'selected_items' => [$selectedProduct->id],
            ]);

        $response->assertRedirect(route('furever.customer-orders'));
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'total' => 300.00,
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $selectedProduct->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseMissing('order_items', [
            'product_id' => $savedProduct->id,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $selectedProduct->id,
            'stock' => 3,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $savedProduct->id,
            'stock' => 4,
        ]);
        $response->assertSessionMissing('cart.'.$selectedProduct->id);
        $response->assertSessionHas('cart.'.$savedProduct->id.'.quantity', 1);
    }

    public function test_customer_must_select_a_cart_product_before_checkout(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Harness',
            'category' => 'Accessories',
            'price' => 250.00,
            'stock' => 3,
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => 1,
                        'stock' => $product->stock,
                    ],
                ],
            ])
            ->from(route('furever.cart'))
            ->post(route('furever.checkout'));

        $response->assertRedirect(route('furever.cart'));
        $response->assertSessionHasErrors('selected_items');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);
    }

    public function test_admin_can_view_and_progress_customer_orders(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
        $customer = User::factory()->create([
            'name' => 'Order Customer',
            'role' => 'customer',
            'status' => 'active',
        ]);
        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 199.50,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('furever.orders'))
            ->assertOk()
            ->assertSee('Customer orders')
            ->assertSee('Order Customer');

        $response = $this->actingAs($admin)->patch(route('furever.orders.status', $order), [
            'status' => 'processing',
        ]);

        $response->assertRedirect(route('furever.orders'));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }

    public function test_admin_can_only_move_an_order_to_its_next_valid_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 199.50,
            'status' => Order::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('furever.orders.status', $order), ['status' => Order::STATUS_READY_FOR_COLLECTION])
            ->assertRedirect(route('furever.orders'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('furever.orders.status', $order), ['status' => Order::STATUS_PROCESSING])
            ->assertRedirect(route('furever.orders'));

        $this->actingAs($admin)
            ->patch(route('furever.orders.status', $order), ['status' => Order::STATUS_COMPLETED])
            ->assertRedirect(route('furever.orders'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_PROCESSING,
        ]);

        $this->actingAs($admin)
            ->patch(route('furever.orders.status', $order), ['status' => Order::STATUS_READY_FOR_COLLECTION])
            ->assertRedirect(route('furever.orders'));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_READY_FOR_COLLECTION,
        ]);
    }

    public function test_customer_can_view_only_their_orders_and_mark_an_order_received(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $otherCustomer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 199.50,
            'status' => Order::STATUS_READY_FOR_COLLECTION,
        ]);
        $otherOrder = Order::create([
            'user_id' => $otherCustomer->id,
            'total' => 499.50,
            'status' => 'pending',
        ]);

        $this->actingAs($customer)
            ->get(route('furever.customer-orders'))
            ->assertOk()
            ->assertSee('Your orders')
            ->assertSee('#'.$order->id)
            ->assertDontSee('#'.$otherOrder->id)
            ->assertSee('Order Received')
            ->assertDontSee('Cancel Order')
            ->assertSee(route('furever.customer-orders'));

        $response = $this->actingAs($customer)
            ->patch(route('furever.customer-orders.received', $order));

        $response->assertRedirect(route('furever.customer-orders'));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
        $this->assertNotNull($order->refresh()->received_at);
    }

    public function test_customer_can_cancel_their_active_order(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 199.50,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($customer)
            ->patch(route('furever.customer-orders.cancel', $order));

        $response->assertRedirect(route('furever.customer-orders'));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cancelling_a_pending_order_restores_its_reserved_stock_once(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Restockable Treat',
            'category' => 'Food',
            'description' => 'A treat reserved by the order.',
            'price' => 75.00,
            'stock' => 0,
        ]);
        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 150.00,
            'status' => Order::STATUS_PENDING,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 2,
        ]);

        $this->actingAs($customer)
            ->patch(route('furever.customer-orders.cancel', $order))
            ->assertRedirect(route('furever.customer-orders'));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_CANCELLED,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 2,
        ]);

        $this->actingAs($customer)
            ->patch(route('furever.customer-orders.cancel', $order))
            ->assertRedirect(route('furever.customer-orders'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 2,
        ]);
    }

    public function test_customer_can_only_confirm_a_ready_order_and_can_only_cancel_a_pending_order(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $processingOrder = Order::create([
            'user_id' => $customer->id,
            'total' => 199.50,
            'status' => Order::STATUS_PROCESSING,
        ]);

        $this->actingAs($customer)
            ->patch(route('furever.customer-orders.received', $processingOrder))
            ->assertRedirect(route('furever.customer-orders'))
            ->assertSessionHas('error');

        $this->actingAs($customer)
            ->patch(route('furever.customer-orders.cancel', $processingOrder))
            ->assertRedirect(route('furever.customer-orders'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', [
            'id' => $processingOrder->id,
            'status' => Order::STATUS_PROCESSING,
        ]);
    }

    public function test_admin_cancelling_a_processing_order_restores_reserved_stock(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Cancelled by staff',
            'category' => 'Food',
            'description' => 'A product whose order cannot be fulfilled.',
            'price' => 100.00,
            'stock' => 0,
        ]);
        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 100.00,
            'status' => Order::STATUS_PROCESSING,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
        ]);

        $this->actingAs($admin)
            ->patch(route('furever.orders.status', $order), ['status' => Order::STATUS_CANCELLED])
            ->assertRedirect(route('furever.orders'));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_CANCELLED,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 1,
        ]);
    }

    public function test_customer_cannot_cancel_someone_elses_order(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $otherCustomer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $order = Order::create([
            'user_id' => $otherCustomer->id,
            'total' => 199.50,
            'status' => 'pending',
        ]);

        $this->actingAs($customer)
            ->patch(route('furever.customer-orders.cancel', $order))
            ->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_customer_cannot_mark_someone_elses_order_received(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $otherCustomer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
        $order = Order::create([
            'user_id' => $otherCustomer->id,
            'total' => 199.50,
            'status' => 'processing',
        ]);

        $this->actingAs($customer)
            ->patch(route('furever.customer-orders.received', $order))
            ->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }
}
