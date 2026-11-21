<?php

namespace App\Actions\V1\SuperAdmin;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WithdrawWalletAction
{
    /**
     * Manually withdraw/deduct funds from a user's wallet inside a locked transaction.
     *
     * @param  array{user_id: int, amount: float|int|string, description?: string|null}  $data
     *
     * @throws ValidationException
     */
    public function execute(array $data): Wallet
    {
        return DB::transaction(function () use ($data) {
            $user = User::findOrFail($data['user_id']);

            $wallet = $user->wallet()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0.00]
            );

            $lockedWallet = Wallet::whereKey($wallet->id)->lockForUpdate()->firstOrFail();

            $amount = round((float) $data['amount'], 2);
            $currentBalance = round((float) $lockedWallet->balance, 2);

            if ($currentBalance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => ['Insufficient wallet balance for this withdrawal.'],
                ]);
            }

            $newBalance = round($currentBalance - $amount, 2);

            $lockedWallet->update([
                'balance' => $newBalance,
            ]);

                $lockedWallet->transactions()->create([
                    'wallet_id'     => $lockedWallet->id,
                    'type'          => 'withdraw',
                    'amount'        => $amount,
                    'description'   => $data['description'] ?? 'Manual withdrawal by Super Admin',
                ]);

            return $lockedWallet->refresh();
        });
    }
}