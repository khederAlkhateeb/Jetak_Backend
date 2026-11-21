<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Hall>
 */
class HallFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['study', 'playstation', 'billiards', 'tennis', 'event']);

        return [
            'name' => ucfirst($type) . ' Hall ' . fake()->numberBetween(1, 9),
            'type' => $type,
        ];
    }
}