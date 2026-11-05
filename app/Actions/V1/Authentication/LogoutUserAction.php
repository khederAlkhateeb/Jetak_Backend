<?php

namespace App\Actions\V1\Authentication;

use App\Models\User;


class LogoutUserAction
{
    /**
     * Log out the authenticated user by revoking their current access token.
     *
     * @param User $user The authenticated user to log out.
     * @return void
     */
    public function execute(User $user): void
    {
        // Revoke the current access token for the authenticated user
        $user->currentAccessToken()->delete();
    }
}