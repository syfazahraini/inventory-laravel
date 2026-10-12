<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $cost = fake()->numberBetween(5, 400) * 1000;

        return [
            'category_id' => Category::factory(),
            'sku' => fake()->unique()->bothify('SKU-####'),
            'name' => ucfirst(fake()->words(3, true)),
            'description' => fake()->sentence(6),
            'unit' => fake()->randomElement(Product::UNITS),
            'cost_price' => $cost,
            'price' => $cost + fake()->numberBetween(1, 50) * 1000,
            'stock' => fake()->numberBetween(0, 100),
            'minimum_stock' => 5,
        ];
    }
}
