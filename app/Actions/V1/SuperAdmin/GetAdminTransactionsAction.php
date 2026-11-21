<?php

namespace App\Actions\V1\SuperAdmin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetAdminTransactionsAction
{
    /**
     * Retrieve paginated financial transactions across the entire platform (Audit Trail).
     *
     * @param  array<string, mixed>  $filters
     */
    public function execute(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 10);

        return \App\Models\Transaction::query()
            ->with(['wallet.user'])
            ->when(
                ! empty($filters['type']),
                fn ($query) => $query->where('type', $filters['type'])
            )
            ->when(
                ! empty($filters['from_date']),
                fn ($query) => $query->whereDate('created_at', '>=', $filters['from_date'])
            )
            ->when(
                ! empty($filters['to_date']),
                fn ($query) => $query->whereDate('created_at', '<=', $filters['to_date'])
            )
            ->latest()
            ->paginate($perPage);
    }
}