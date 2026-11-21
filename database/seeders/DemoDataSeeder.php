<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BreadSubscription;
use App\Models\Hall;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\Support\WalletLedger;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('DemoDataSeeder skipped in production.');

            return;
        }

        $halls = Hall::all();
        if ($halls->isEmpty()) {
            $halls = Hall::factory()->count(3)->create();
        }

        $deliveryMen = User::factory()->count(3)->delivery()->create();

        $students = User::factory()->count(20)->student()->create();
        if ($existing = User::where('email', 'student@gmail.com')->first()) {
            $students->push($existing);
        }

        // Inactive users: they get a wallet but no activity.
        User::factory()->count(2)->student()->suspended()->create();
        User::factory()->student()->banned()->create();

        foreach ($students as $student) {
            $this->seedStudentActivity($student, $deliveryMen, $halls);
        }

        $problems = WalletLedger::inconsistencies();
        if ($problems !== []) {
            throw new RuntimeException("Wallet consistency check failed:\n" . implode("\n", $problems));
        }

        $this->command?->info('Demo data seeded. All wallets match their transactions.');
    }

    private function seedStudentActivity(User $student, Collection $deliveryMen, Collection $halls): void
    {
        // Oldest event first: initial top-up 31 days ago.
        WalletLedger::deposit(
            $student,
            fake()->randomElement([50, 100, 150, 200, 300]),
            'Initial wallet top-up',
            now()->subDays(31)
        );

        // Build the student's events, then replay them in chronological order
        // so the running balance never goes negative.
        $events = [];
        for ($i = 0, $n = fake()->numberBetween(1, 4); $i < $n; $i++) {
            $events[] = ['order', $this->randomMoment()];
        }
        for ($i = 0, $n = fake()->numberBetween(0, 3); $i < $n; $i++) {
            $events[] = ['booking', $this->randomMoment()];
        }
        if (fake()->boolean(50)) {
            $events[] = ['bread', $this->randomMoment()];
        }
        if (fake()->boolean(25)) {
            $events[] = ['withdraw', $this->randomMoment()];
        }

        usort($events, fn ($a, $b) => $a[1]->getTimestamp() <=> $b[1]->getTimestamp());

        foreach ($events as [$kind, $at]) {
            match ($kind) {
                'order' => $this->createOrder($student, $deliveryMen, $at),
                'booking' => $this->createBooking($student, $halls, $at),
                'bread' => $this->createBreadSubscription($student, $at),
                'withdraw' => $this->createWithdrawal($student, $at),
            };
        }
    }

    private function createOrder(User $student, Collection $deliveryMen, Carbon $at): void
    {
        $status = fake()->randomElement([
            'pending', 'batching', 'preparing', 'out_for_delivery',
            'delivered', 'delivered', 'delivered', 'cancelled',
        ]);

        Order::factory()
            ->for($student, 'student')
            ->withStatus($status, $deliveryMen->random())
            ->create(['created_at' => $at, 'updated_at' => $at]);
    }

    private function createBooking(User $student, Collection $halls, Carbon $at): void
    {
        Booking::factory()
            ->for($student, 'student')
            ->for($halls->random())
            ->create([
                'booking_date' => $at->copy()->addDays(fake()->numberBetween(0, 7))->toDateString(),
                'created_at' => $at,
                'updated_at' => $at,
            ]);
    }

    private function createBreadSubscription(User $student, Carbon $at): void
    {
        BreadSubscription::factory()
            ->for($student, 'student')
            ->create([
                'start_date' => $at->copy()->addDay()->toDateString(),
                'created_at' => $at,
                'updated_at' => $at,
            ]);
    }

    private function createWithdrawal(User $student, Carbon $at): void
    {
        $balance = (float) WalletLedger::walletOf($student)->refresh()->balance;

        if ($balance >= 20) {
            WalletLedger::withdraw($student, min($balance, fake()->randomElement([10, 20, 50])), 'Cash withdrawal', $at);
        }
    }

    private function randomMoment(): Carbon
    {
        return now()->subDays(fake()->numberBetween(0, 29))->subMinutes(fake()->numberBetween(0, 1439));
    }
}