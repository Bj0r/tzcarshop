<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->text(),
            'country_id' => $this->faker->numberBetween(1, 100),
            'stock' => $this->faker->numberBetween(1, 1000),
            'amount' => $this->faker->randomFloat(2, 10, 9999),
            'photo' => null,
        ];
    }
}