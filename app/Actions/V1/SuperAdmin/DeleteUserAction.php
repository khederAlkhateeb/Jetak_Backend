<?php

namespace App\Actions\V1\SuperAdmin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteUserAction
{
    /**
     * Delete a user account safely with business rules enforcement.
     *
     * @throws ValidationException
     */
    public function execute(User $user): void
    {
        // Prevent a SuperAdmin from deleting themselves
        if ($user->id === auth()->id()) {
            throw ValidationException::withMessages([
                'user' => ['You cannot delete your own account.'],
            ]);
        }

        // Prevent deleting another SuperAdmin (optional safety rule)
        if ($user->hasRole('SuperAdmin')) {
            throw ValidationException::withMessages([
                'user' => ['SuperAdmin accounts cannot be deleted.'],
            ]);
        }

        if (!$user) {
            throw ValidationException::withMessages([
                'user' => ['The specified user does not exist.'],
            ]);
        }
        DB::transaction(function () use ($user) {
            // Revoke all active tokens before deletion
            $user->tokens()->delete();

            // Delete user's wallet if exists
            $user->wallet()?->delete();

            // Delete the user record
            $user->delete();
        });
    }
}
