<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->text(),
            'genre' => $this->faker->randomElement(['Action', 'Drama', 'Comedy', 'Horror', 'Sci-Fi', 'Romance']),
            'release_year' => $this->faker->numberBetween(1980, 2026),
            'rating' => $this->faker->randomFloat(1, 1, 10),
            'poster' => 'default.jpg',
        ];
    }
}