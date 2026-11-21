<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => ucfirst(fake()->words(2, true)),
            'price' => fake()->randomFloat(2, 1, 25),
            'stock_quantity' => fake()->numberBetween(20, 200),
            'image' => null,
        ];
    }
}