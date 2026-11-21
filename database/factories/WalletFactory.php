<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Plain User factory on purpose: User::factory()->student() already creates a wallet.
            'user_id' => User::factory(),
            // Balance always starts at 0 and only changes through transactions.
            'balance' => 0,
        ];
    }
}