<?php

namespace Database\Seeders\Support;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\CarbonInterface;

/**
 * Single entry point for moving money in seeders/factories.
 * Every movement creates a Transaction, and TransactionFactory applies it to the wallet balance.
 */
class WalletLedger
{
    public static function walletOf(User $user): Wallet
    {
        return $user->wallet()->firstOrCreate([], ['balance' => 0]);
    }

    public static function deposit(User $user, float $amount, string $description = 'Wallet top-up', ?CarbonInterface $at = null): Transaction
    {
        return self::record($user, 'deposit', $amount, $description, $at);
    }

    public static function withdraw(User $user, float $amount, string $description = 'Cash withdrawal', ?CarbonInterface $at = null): Transaction
    {
        return self::record($user, 'withdraw', $amount, $description, $at);
    }

    /**
     * Spend from the wallet. If the balance is not enough, a top-up
     * (rounded up to the next 50) is deposited one minute before the spend.
     */
    public static function spend(User $user, float $amount, string $description, ?CarbonInterface $at = null): Transaction
    {
        $wallet = self::walletOf($user)->refresh();
        $shortfall = round($amount - (float) $wallet->balance, 2);

        if ($shortfall > 0) {
            $topUp = (float) (ceil($shortfall / 50) * 50);
            self::deposit($user, $topUp, 'Wallet top-up', $at?->copy()->subMinute());
        }

        return self::record($user, 'spend', $amount, $description, $at);
    }

    /** Expected balance = deposits - (spend + withdraw). */
    public static function expectedBalance(Wallet $wallet): float
    {
        $in = (float) $wallet->transactions()->where('type', 'deposit')->sum('amount');
        $out = (float) $wallet->transactions()->whereIn('type', ['spend', 'withdraw'])->sum('amount');

        return round($in - $out, 2);
    }

    /** @return array<int, string> */
    public static function inconsistencies(): array
    {
        $problems = [];

        foreach (Wallet::all() as $wallet) {
            $expected = self::expectedBalance($wallet);
            $actual = round((float) $wallet->balance, 2);

            if (abs($expected - $actual) > 0.001) {
                $problems[] = "Wallet #{$wallet->id}: balance is {$actual} but transactions give {$expected}";
            }
            if ($actual < 0) {
                $problems[] = "Wallet #{$wallet->id}: negative balance {$actual}";
            }
        }

        return $problems;
    }

    private static function record(User $user, string $type, float $amount, string $description, ?CarbonInterface $at): Transaction
    {
        $attributes = [
            'type' => $type,
            'amount' => round($amount, 2),
            'description' => $description,
        ];

        if ($at) {
            $attributes['created_at'] = $at;
            $attributes['updated_at'] = $at;
        }

        return Transaction::factory()->for(self::walletOf($user))->create($attributes);
    }
}