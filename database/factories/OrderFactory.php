<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\Support\WalletLedger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => User::factory()->student(),
            'delivery_id' => null,
            'batch_number' => null,
            'total_amount' => 0, // recalculated from the items after creation
            'status' => 'pending',
        ];
    }

    /** Sets status with matching delivery_id / batch_number. */
    public function withStatus(string $status, ?User $delivery = null): static
    {
        $assigned = in_array($status, ['out_for_delivery', 'delivered'], true);
        $batched = $assigned || in_array($status, ['batching', 'preparing'], true);

        return $this->state(fn () => [
            'status' => $status,
            'delivery_id' => $assigned ? ($delivery?->id ?? User::factory()->delivery()) : null,
            'batch_number' => $batched ? 'B-' . fake()->numerify('####') : null,
        ]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {
            // 1) items (only if none were provided)
            if (! $order->items()->exists()) {
                $products = Product::query()->inRandomOrder()->limit(fake()->numberBetween(1, 4))->get();

                if ($products->isEmpty()) {
                    $products = Product::factory()->count(3)->create();
                }

                foreach ($products as $product) {
                    OrderItem::factory()->for($order)->for($product)->create([
                        'quantity' => fake()->numberBetween(1, 5),
                        'price' => $product->price,
                    ]);
                }
            }

            // 2) total = sum(price * quantity)
            $total = round(
                $order->items()->get()->sum(fn (OrderItem $item) => (float) $item->price * $item->quantity),
                2
            );
            $order->forceFill(['total_amount' => $total])->save();

            // 3) charge the student's wallet (cancelled orders are not charged)
            if ($order->status !== 'cancelled') {
                WalletLedger::spend($order->student, $total, "Order #{$order->id}", $order->created_at);
            }
        });
    }
}