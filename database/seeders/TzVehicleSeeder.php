<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class TzVehicleSeeder extends Seeder
{
    /**
     * Seed the Toyota showroom with repeatable demo inventory.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'name' => 'Toyota Vios',
                'year' => 2026,
                'color' => 'Silver Metallic',
                'transmission' => 'CVT',
                'fuel_type' => 'Gasoline',
                'description' => 'A dependable and efficient sedan made for everyday city driving.',
                'price' => 1092000,
                'stock' => 6,
                'image' => 'https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&w=1200&q=85',
            ],
            [
                'name' => 'Toyota Raize',
                'year' => 2026,
                'color' => 'Turquoise Mica Metallic',
                'transmission' => 'CVT',
                'fuel_type' => 'Gasoline',
                'description' => 'A compact SUV with agile handling and practical urban versatility.',
                'price' => 1074000,
                'stock' => 5,
                'image' => 'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?auto=format&fit=crop&w=1200&q=85',
            ],
            [
                'name' => 'Toyota Corolla Altis',
                'year' => 2026,
                'color' => 'Platinum White Pearl Mica',
                'transmission' => 'CVT',
                'fuel_type' => 'Hybrid',
                'description' => 'A refined sedan combining confident comfort with hybrid efficiency.',
                'price' => 1791000,
                'stock' => 4,
                'image' => 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?auto=format&fit=crop&w=1200&q=85',
            ],
            [
                'name' => 'Toyota Corolla Cross',
                'year' => 2026,
                'color' => 'Metal Stream Metallic',
                'transmission' => 'CVT',
                'fuel_type' => 'Hybrid',
                'description' => 'A spacious crossover designed for efficient family adventures.',
                'price' => 1943000,
                'stock' => 5,
                'image' => 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=1200&q=85',
            ],
            [
                'name' => 'Toyota Camry',
                'year' => 2026,
                'color' => 'Attitude Black Mica',
                'transmission' => 'CVT',
                'fuel_type' => 'Hybrid',
                'description' => 'A premium executive sedan with composed performance and comfort.',
                'price' => 2651000,
                'stock' => 3,
                'image' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=85',
            ],
            [
                'name' => 'Toyota Hilux',
                'year' => 2026,
                'color' => 'Emotional Red',
                'transmission' => 'Automatic',
                'fuel_type' => 'Diesel',
                'description' => 'A durable pickup engineered for demanding work and weekend travel.',
                'price' => 2076000,
                'stock' => 7,
                'image' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=1200&q=85',
            ],
            [
                'name' => 'Toyota Fortuner',
                'year' => 2026,
                'color' => 'Silver Metallic',
                'transmission' => 'Automatic',
                'fuel_type' => 'Diesel',
                'description' => 'A commanding seven-seat SUV built for comfort and capability.',
                'price' => 2656000,
                'stock' => 4,
                'image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=85',
            ],
            [
                'name' => 'Toyota Hiace',
                'year' => 2026,
                'color' => 'Luxury Pearl Toning',
                'transmission' => 'Automatic',
                'fuel_type' => 'Diesel',
                'description' => 'A roomy and reliable van for business, groups, and long journeys.',
                'price' => 3387000,
                'stock' => 3,
                'image' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=85',
            ],
            [
                'name' => 'Toyota GR Supra',
                'year' => 2026,
                'color' => 'Prominence Red',
                'transmission' => 'Automatic',
                'fuel_type' => 'Gasoline',
                'description' => 'A flagship rear-wheel-drive sports car shaped for thrilling performance.',
                'price' => 5396000,
                'stock' => 2,
                'image' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=85',
            ],
            [
                'name' => 'Toyota GR86',
                'year' => 2026,
                'color' => 'Crystal Black Silica',
                'transmission' => 'Manual',
                'fuel_type' => 'Gasoline',
                'description' => 'A lightweight sports coupe with direct handling and driver-first character.',
                'price' => 2740000,
                'stock' => 3,
                'image' => 'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?auto=format&fit=crop&w=1200&q=85',
            ],
            [
                'name' => 'Toyota GR Yaris',
                'year' => 2026,
                'color' => 'Super White II',
                'transmission' => 'Manual',
                'fuel_type' => 'Gasoline',
                'description' => 'A rally-inspired performance hatchback with compact all-road confidence.',
                'price' => 3740000,
                'stock' => 2,
                'image' => 'https://images.unsplash.com/photo-1542362567-b07e54358753?auto=format&fit=crop&w=1200&q=85',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Product::updateOrCreate(
                ['name' => $vehicle['name']],
                array_merge($vehicle, [
                    'category' => 'Toyota',
                ])
            );
        }
    }
}
