<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TzAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_uses_the_tz_carshop_authentication_design(): void
    {
        $this->get(route('furever.login'))
            ->assertOk()
            ->assertSee('TZ Carshop')
            ->assertSee('Welcome back')
            ->assertSee('Customers and administrators can sign in')
            ->assertSee('data-tz-auth', false)
            ->assertDontSee('img/tz-mark.svg', false);
    }

    public function test_guest_navigation_offers_customer_sign_in_and_registration(): void
    {
        $this->get(route('furever'))
            ->assertSee('<strong>TZ</strong>', false)
            ->assertSee('tz-wordmark__badge', false)
            ->assertSee('fa fa-car', false)
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
            ->assertDontSee('img/tz-mark.svg', false);
    }

    public function test_admin_can_sign_in_with_a_database_username(): void
    {
        $admin = $this->makeUser('admin', 'active');

        $response = $this->post(route('furever.login.store'), [
            'login' => $admin->username,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('furever.dashboard'));
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
            ->assertSee('Product Management')
            ->assertSee('Customer Management')
            ->assertSee('Cart & Order Management')
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
        $response = $this->post(route('furever.register.store'), [
            'name' => 'Shop Customer',
            'username' => 'shop.customer',
            'email' => 'shop.customer@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('furever.shop'));
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

        $response = $this->post(route('furever.login.store'), [
            'login' => $customer->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('furever.shop'));
        $this->assertAuthenticatedAs($customer);
    }

    public function test_inactive_account_cannot_sign_in(): void
    {
        $customer = $this->makeUser('customer', 'inactive');

        $response = $this->from(route('furever.login'))
            ->post(route('furever.login.store'), [
                'login' => $customer->username,
                'password' => 'secret123',
            ]);

        $response->assertRedirect(route('furever.login'));
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
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

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    1 => [
                        'id' => 1,
                        'name' => 'Toyota GR Supra',
                        'price' => 5396000,
                        'image' => 'https://example.com/toyota-gr-supra.jpg',
                        'quantity' => 1,
                        'stock' => 2,
                    ],
                ],
            ])
            ->get(route('furever.cart'));

        $response
            ->assertOk()
            ->assertSee('Toyota vehicle')
            ->assertSee('https://example.com/toyota-gr-supra.jpg', false)
            ->assertDontSee('storage/https://example.com', false)
            ->assertDontSee('FurEver Product');
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
