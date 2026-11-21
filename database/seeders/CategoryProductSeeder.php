<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'Bakery' => [['Bread Pack', 1.00], ['Croissant', 1.50], ['Manakish', 2.00]],
            'Drinks' => [['Water Bottle', 0.50], ['Orange Juice', 1.50], ['Coffee', 2.00]],
            'Snacks' => [['Chips', 1.00], ['Chocolate Bar', 1.25], ['Biscuits', 0.75]],
            'Groceries' => [['Eggs (10)', 3.00], ['Milk 1L', 1.80], ['Cheese 200g', 2.50]],
        ];

        foreach ($catalog as $categoryName => $products) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            foreach ($products as [$name, $price]) {
                Product::firstOrCreate(
                    ['category_id' => $category->id, 'name' => $name],
                    ['price' => $price, 'stock_quantity' => fake()->numberBetween(50, 200)]
                );
            }
        }
    }
}