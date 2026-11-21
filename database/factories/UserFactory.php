<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => '09' . fake()->unique()->numerify('########'),
            'status' => 'active',
            'building_number' => null,
            'floor' => null,
            'room_number' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /** Student with housing info, "Student" role and an empty wallet. */
    public function student(): static
    {
        return $this
            ->state(fn () => [
                'building_number' => fake()->numberBetween(1, 30),
                'floor' => fake()->numberBetween(1, 6),
                'room_number' => fake()->numberBetween(1, 40),
            ])
            ->afterCreating(fn (User $user) => $this->provision($user, 'Student'));
    }

    /** Delivery representative with "Delivery" role and an empty wallet. */
    public function delivery(): static
    {
        return $this->afterCreating(fn (User $user) => $this->provision($user, 'Delivery'));
    }

    public function suspended(): static
    {
        return $this->state(['status' => 'suspended']);
    }

    public function banned(): static
    {
        return $this->state(['status' => 'banned']);
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }

    private function provision(User $user, string $role): void
    {
        $user->assignRole($role);
        $user->wallet()->firstOrCreate([], ['balance' => 0]);
    }
}