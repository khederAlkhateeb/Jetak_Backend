<?php

namespace Database\Factories;

use App\Models\BreadSubscription;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Support\WalletLedger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BreadSubscription>
 */
class BreadSubscriptionFactory extends Factory
{
    /** Price of one bread unit. The table has no price column, so adjust this to your real price. */
    public const UNIT_PRICE = 0.50;

    public function definition(): array
    {
        return [
            'student_id' => User::factory()->student(),
            'package_type' => fake()->randomElement(['daily', 'weekly']),
            'start_date' => fake()->dateTimeBetween('-30 days', '+7 days')->format('Y-m-d'),
            'end_date' => fn (array $a) => Carbon::parse($a['start_date'])
                ->addDays($a['package_type'] === 'weekly' ? 6 : 0)
                ->toDateString(),
            'status' => fn (array $a) => Carbon::parse($a['end_date'])->lt(today()) ? 'ended' : 'active',
            'daily_quantity' => fake()->numberBetween(1, 4),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (BreadSubscription $subscription) {
            $days = $subscription->package_type === 'weekly' ? 7 : 1;
            $amount = round($subscription->daily_quantity * $days * self::UNIT_PRICE, 2);

            WalletLedger::spend(
                $subscription->student,
                $amount,
                "Bread subscription #{$subscription->id}",
                $subscription->created_at
            );
        });
    }
}