<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use LogicException;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory(),
            'amount' => fake()->randomFloat(2, 5, 200),
            'type' => 'deposit',
            'description' => 'Wallet top-up',
        ];
    }

    public function deposit(): static
    {
        return $this->state(['type' => 'deposit', 'description' => 'Wallet top-up']);
    }

    public function spend(): static
    {
        return $this->state(['type' => 'spend', 'description' => 'Purchase']);
    }

    public function withdraw(): static
    {
        return $this->state(['type' => 'withdraw', 'description' => 'Cash withdrawal']);
    }

    /**
     * Every transaction created here also updates the wallet balance,
     * so wallet.balance always matches its transactions.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Transaction $transaction) {
            $wallet = Wallet::query()->findOrFail($transaction->wallet_id);
            $balance = (float) $wallet->balance;
            $amount = (float) $transaction->amount;

            if ($transaction->type === 'deposit') {
                $newBalance = $balance + $amount;
            } else {
                if ($balance < $amount) {
                    throw new LogicException(
                        "Wallet #{$wallet->id} has insufficient balance ({$balance}) for a {$transaction->type} of {$amount}."
                    );
                }
                $newBalance = $balance - $amount;
            }

            $wallet->forceFill(['balance' => round($newBalance, 2)])->save();
        });
    }
}