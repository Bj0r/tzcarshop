<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TzAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Same-origin browser requests send a Referer, which makes Sanctum use the session.
        $this->withHeader('Referer', 'http://localhost');
    }

    public function test_login_page_uses_the_tz_carshop_authentication_design(): void
    {
        $this->get(route('furever.login'))
            ->assertOk()
            ->assertSee('TZ Carshop')
            ->assertSee('Welcome back')
            ->assertSee('Customers and administrators can sign in')
            ->assertSee('data-tz-auth', false)
            ->assertSee('img/tz-mark.svg', false)
            ->assertSee(route('api.tz.login'));
    }

    public function test_guest_navigation_offers_customer_sign_in_and_registration(): void
    {
        $this->get(route('furever'))
            ->assertSee('<strong>TZ</strong>', false)
            ->assertSee('img/tz-mark.svg', false)
            ->assertSee('>Sign in</a>', false)
            ->assertSee('>Create account</a>', false)
            ->assertSee(route('furever.login'))
            ->assertSee(route('furever.register'))
            ->assertDontSee('Admin sign in');
    }

    public function test_registration_page_uses_the_tz_carshop_authentication_design(): void
    {
        $this->get(route('furever.register'))
            ->assertOk()
            ->assertSee('TZ Carshop')
            ->assertSee('Create your account')
            ->assertSee('data-tz-auth', false)
            ->assertSee('img/tz-mark.svg', false)
            ->assertSee(route('api.tz.register'));
    }

    public function test_admin_can_sign_in_with_a_database_username(): void
    {
        $admin = $this->makeUser('admin', 'active');

        $response = $this->postJson(route('api.tz.login'), [
            'login' => $admin->username,
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.role', 'admin')
            ->assertJsonPath('redirect', route('furever.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_active_administrator_can_open_the_dashboard(): void
    {
        $admin = $this->makeUser('admin', 'active');

        $response = $this->actingAs($admin)->get(route('furever.dashboard'));

        $response->assertOk();
        $response->assertSee('Admin dashboard');
    }

    public function test_admin_dashboard_exposes_every_rubric_management_module(): void
    {
        $admin = $this->makeUser('admin', 'active');
        $this->makeUser('customer', 'active');
        $this->makeUser('customer', 'inactive');

        $response = $this->actingAs($admin)->get(route('furever.dashboard'));

        $response
            ->assertOk()
            ->assertSee('Admin dashboard')
            ->assertSee('Manage your Toyota inventory, customers, carts, and orders.')
            ->assertSee(route('furever.products'))
            ->assertSee(route('furever.customers'))
            ->assertSee(route('furever.orders'))
            ->assertSee('Total customers')
            ->assertSee('Active customers');
    }

    public function test_admin_storefront_shows_a_back_to_dashboard_link(): void
    {
        $admin = $this->makeUser('admin', 'active');

        $response = $this->actingAs($admin)->get(route('furever'));

        $response->assertOk();
        $response->assertSee('Back to Dashboard');
        $response->assertSee(route('furever.dashboard'));
        $response->assertDontSee('>Log out</button>', false);
    }

    public function test_customer_can_register_and_is_sent_to_the_shop(): void
    {
        $response = $this->postJson(route('api.tz.register'), [
            'name' => 'Shop Customer',
            'username' => 'shop.customer',
            'email' => 'shop.customer@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.username', 'shop.customer')
            ->assertJsonPath('redirect', route('furever.shop'))
            ->assertJsonMissingPath('user.password');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'username' => 'shop.customer',
            'role' => 'customer',
            'status' => 'active',
        ]);
    }

    public function test_customer_login_with_email_returns_to_the_shop(): void
    {
        $customer = $this->makeUser('customer', 'active');

        $response = $this->postJson(route('api.tz.login'), [
            'login' => $customer->email,
            'password' => 'secret123',
        ]);

        $response->assertOk()->assertJsonPath('redirect', route('furever.shop'));
        $this->assertAuthenticatedAs($customer);
    }

    public function test_inactive_account_cannot_sign_in(): void
    {
        $customer = $this->makeUser('customer', 'inactive');

        $this->postJson(route('api.tz.login'), [
            'login' => $customer->username,
            'password' => 'secret123',
        ])->assertUnprocessable()->assertJsonValidationErrors('login');

        $this->assertGuest();
    }

    public function test_wrong_password_cannot_sign_in(): void
    {
        $customer = $this->makeUser('customer', 'active');

        $this->postJson(route('api.tz.login'), [
            'login' => $customer->username,
            'password' => 'wrong-password',
        ])->assertUnprocessable()->assertJsonValidationErrors('login');

        $this->assertGuest();
    }

    public function test_registration_validates_input_and_unique_accounts(): void
    {
        $this->makeUser('customer', 'active');

        $this->postJson(route('api.tz.register'), [
            'name' => '',
            'username' => 'customer.active',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'username', 'email', 'password']);

        $this->assertGuest();
    }

    public function test_signed_in_customer_can_read_their_account_and_log_out(): void
    {
        $customer = $this->makeUser('customer', 'active');

        $this->actingAs($customer)
            ->getJson(route('api.tz.user'))
            ->assertOk()
            ->assertJsonPath('user.email', $customer->email)
            ->assertJsonMissingPath('user.password');

        $this->actingAs($customer)
            ->postJson(route('api.tz.logout'))
            ->assertOk()
            ->assertJsonPath('redirect', route('furever.login'));

        $this->assertGuest('web');
    }

    public function test_guest_cannot_read_account_api(): void
    {
        $this->getJson(route('api.tz.user'))->assertUnauthorized();
    }

    public function test_customer_cannot_open_admin_management(): void
    {
        $customer = $this->makeUser('customer', 'active');

        $response = $this->actingAs($customer)->get(route('furever.products'));

        $response->assertRedirect(route('furever.login'));
        $this->assertGuest();
    }

    public function test_guest_must_sign_in_before_using_the_cart(): void
    {
        $response = $this->get(route('furever.cart'));

        $response->assertRedirect(route('furever.login'));
    }

    public function test_active_administrator_can_open_the_cart_management_page(): void
    {
        $admin = $this->makeUser('admin', 'active');

        $response = $this->actingAs($admin)->get(route('furever.cart'));

        $response
            ->assertOk()
            ->assertSee('data-tz-cart', false)
            ->assertSee('Your Toyota Cart')
            ->assertSee('Your garage is waiting')
            ->assertDontSee('pet essentials');
    }

    public function test_populated_cart_uses_vehicle_content_and_supports_remote_images(): void
    {
        $customer = $this->makeUser('customer', 'active');

        $product = Product::create([
            'name' => 'Toyota GR Supra',
            'category' => 'Toyota',
            'price' => 5396000,
            'stock' => 2,
            'image' => 'https://example.com/toyota-gr-supra.jpg',
        ]);

        $this->actingAs($customer)
            ->get(route('furever.cart'))
            ->assertOk()
            ->assertSee('Toyota vehicle')
            ->assertDontSee('FurEver Product');

        // Cart items are loaded by the page from the cart API.
        $this->actingAs($customer)
            ->withSession(['cart' => [$product->id => ['id' => $product->id, 'quantity' => 1]]])
            ->getJson(route('api.tz.cart.index'))
            ->assertOk()
            ->assertJsonPath('data.items.0.name', 'Toyota GR Supra')
            ->assertJsonPath('data.items.0.image', 'https://example.com/toyota-gr-supra.jpg');
    }

    private function makeUser(string $role, string $status): User
    {
        return User::factory()->create([
            'username' => $role.'.'.$status,
            'email' => $role.'.'.$status.'@example.com',
            'password' => Hash::make('secret123'),
            'role' => $role,
            'status' => $status,
        ]);
    }
}
