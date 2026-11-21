<?php

namespace App\Actions\V1\SuperAdmin;

use App\Models\Transaction;

class GetAdminTransactionDetailsAction
{
    /**
     * Load full details for a specific financial transaction.
     */
    public function execute(Transaction $transaction)
    {
        return $transaction->load([
            'wallet.user.roles',
        ]);
    }
}