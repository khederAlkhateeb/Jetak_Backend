<?php

namespace App\Actions\V1\Wallet;

use App\Models\User;
use App\Models\Wallet;

class GetWalletBalanceAction
{
    /**
     * Retrieve the current wallet for the authenticated user,
     * creating a zero-balance wallet if one does not exist yet.
     */
    public function execute(User $user): Wallet
    {
        return $user->wallet()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00]
        );
    }
}