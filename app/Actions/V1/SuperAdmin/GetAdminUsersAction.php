<?php

namespace App\Actions\V1\SuperAdmin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetAdminUsersAction
{
    /**
     * Execute the action to retrieve filtered and paginated system users.
     *
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator
     */
    public function execute(array $filters): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->filter($filters)
            ->sortByField($filters['sort_by'] ?? null, $filters['sort_dir'] ?? null)
            ->paginate($filters['per_page'] ?? 10);
    }
}