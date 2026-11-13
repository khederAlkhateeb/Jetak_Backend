<?php

namespace App\Actions\V1\SuperAdmin;

use App\Models\User;

class GetUserDetailsAction
{
    /**
     * Load complete user profile, roles, permissions, and activity logs.
     */
    public function execute(User $user): User
    {
        return $user->load([
            'roles',
            'wallet',
            'wallet.transactions' => fn ($query) => $query->latest()->limit(10),
            'bookings'            => fn ($query) => $query->latest()->limit(10),
            'breadSubscriptions'  => fn ($query) => $query->latest()->limit(5),
        ]);
    }
}