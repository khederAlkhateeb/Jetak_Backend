<?php

namespace App\Actions\V1\Wallet;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetWalletTransactionsAction
{
    public function __construct(
        private readonly GetWalletBalanceAction $getWalletBalanceAction,
    ) {}

    /**
     * Retrieve paginated financial transactions for the authenticated user's wallet.
     *
     * @param  array<string, mixed>  $filters
     */
    public function execute(User $user, array $filters = []): LengthAwarePaginator
    {
        $wallet = $this->getWalletBalanceAction->execute($user);
        $perPage = (int) ($filters['per_page'] ?? 10);

        return $wallet->transactions()
            ->when(
                ! empty($filters['type']),
                fn ($query) => $query->where('type', $filters['type'])
            )
            ->latest()
            ->paginate($perPage);
    }
}