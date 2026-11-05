<?php

namespace App\Actions\V1\Authentication;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MeUserAction
{
    /**
     * Execute the action to get the authenticated user with their related data.
     *
     * @param User $user
     * @return User
     */
    public function execute(User $user): User
    {
        return $user->load([
            'wallet',
            'breadSubscriptions'=> function ($query) {
                $query->where('status', 'active');
            },
            'bookings'=> function ($query) {
                $query->latest()->limit(5);
            },
        ]);
    }
}