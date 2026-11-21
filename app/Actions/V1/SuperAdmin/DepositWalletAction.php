<?php

namespace App\Actions\V1\SuperAdmin;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class DepositWalletAction
{
    /**
     * Manually deposit funds into a user's wallet inside a locked transaction.
     *
     * @param  array{user_id: int, amount: float|int|string, description?: string|null}  $data
     */
    public function execute(array $data): Wallet
    {
        return DB::transaction(function () use ($data) {
            
            $user = User::findOrFail($data['user_id']);
            
            // Check if the user is active before proceeding with the deposit
            if($user->status !== 'active') {
                throw new \Exception('Cannot deposit to an inactive user.');
            }
            // Ensure wallet exists, then lock the row for safe concurrent balance updates
            $wallet = $user->wallet()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0.00]
            );

            $lockedWallet = Wallet::whereKey($wallet->id)->lockForUpdate()->firstOrFail();

            $amount = round((float) $data['amount'], 2);
            $newBalance = round((float) $lockedWallet->balance + $amount, 2);

            $lockedWallet->update([
                'balance' => $newBalance,
            ]);

            // Record transaction in the transactions table
            $lockedWallet->transactions()->create([
                'wallet_id'     => $lockedWallet->id,
                'type'          => 'deposit',
                'amount'        => $amount,
                'description'   => $data['description'] ?? 'Manual deposit by Super Admin',
            ]);

            return $lockedWallet->refresh();
        });
    }
}
