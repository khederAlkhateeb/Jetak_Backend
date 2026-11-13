<?php

namespace App\Actions\V1\SuperAdmin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateUserStatusAction
{
    /**
     * Update the account status of a user (active, suspended, banned).
     */
    public function execute(User $user, string $status): User
    {
        // Prevent a SuperAdmin from changing their own status
        if ($user->hasRole('SuperAdmin') && $user->id === auth()->id()) {
            throw ValidationException::withMessages([
                'status' => ['You cannot change your own SuperAdmin status.'],
            ]);
        }
        // Prevent changing the status that the user already has
        if($user->status == $status){
            throw ValidationException::withMessages([
                'status' => ['The user already has the status: ' . $status . '.'],
            ]);
        }

        return DB::transaction(function () use ($user, $status) {
            $user->update([
                'status' => $status,
            ]);

            if (in_array($status, ['banned', 'suspended'])) {
                $user->tokens()->delete();
            }

            return $user->load(['roles', 'wallet']);
        });
    }
}