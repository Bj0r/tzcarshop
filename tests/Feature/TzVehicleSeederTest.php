<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TzVehicleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_the_toyota_showroom(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('products', 11);
        $this->assertDatabaseHas('products', [
            'name' => 'Toyota GR Supra',
            'category' => 'Toyota',
            'transmission' => 'Automatic',
            'fuel_type' => 'Gasoline',
        ]);
        $this->assertDatabaseHas('products', ['name' => 'Toyota GR86']);
        $this->assertDatabaseHas('products', ['name' => 'Toyota GR Yaris']);
        $this->assertDatabaseHas('products', ['name' => 'Toyota Fortuner']);
        $this->assertSame(0, Product::query()->whereNull('image')->count());
        $this->assertSame(11, Product::query()->where('image', 'like', 'https://images.unsplash.com/%')->count());
    }

    public function test_vehicle_seeding_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('products', 11);
    }
}
