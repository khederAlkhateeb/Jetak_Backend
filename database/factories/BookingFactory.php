<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\User;
use Database\Seeders\Support\WalletLedger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        $startHour = fake()->numberBetween(8, 20);
        $hours = fake()->numberBetween(1, 3);
        $hourlyRate = fake()->randomElement([3, 5, 8, 10]);

        return [
            'student_id' => User::factory()->student(),
            'hall_id' => Hall::factory(),
            'booking_date' => fake()->dateTimeBetween('-20 days', '+14 days')->format('Y-m-d'),
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time' => sprintf('%02d:00:00', $startHour + $hours),
            'total_price' => $hourlyRate * $hours,
            'status' => fake()->randomElement(['pending', 'confirmed', 'confirmed', 'cancelled']),
        ];
    }

    public function withStatus(string $status): static
    {
        return $this->state(['status' => $status]);
    }

    public function configure(): static
    {
        // Only confirmed bookings are charged. pending/cancelled do not move money.
        return $this->afterCreating(function (Booking $booking) {
            if ($booking->status === 'confirmed') {
                WalletLedger::spend(
                    $booking->student,
                    (float) $booking->total_price,
                    "Booking #{$booking->id}",
                    $booking->created_at
                );
            }
        });
    }
}