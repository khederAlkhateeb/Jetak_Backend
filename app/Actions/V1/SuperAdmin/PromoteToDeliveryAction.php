<?php

namespace App\Actions\V1\SuperAdmin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PromoteToDeliveryAction
{
    /**
     * Promote a user to Delivery role with strict business validations.
     *
     * @throws ValidationException
     */
    public function execute(User $user): User
    {
        // Business rule validations
        if ($user->hasRole('Delivery')) {
            throw ValidationException::withMessages([
                'user' => ['This user already has the Delivery role.'],
            ]);
        }

        // Ensure the user does not have any administrative or operational roles
        if ($user->hasAnyRole(['Super Admin', 'Hall Admin', 'Store Admin'])) {
            throw ValidationException::withMessages([
                'user' => ['This user cannot be promoted because they already have administrative or operational privileges.'],
            ]);
        }

        if (! $user->hasRole('Student')) {
            throw ValidationException::withMessages([
                'user' => ['Only students can be promoted to delivery personnel.'],
            ]);
        }

        return DB::transaction(function () use ($user) {
            $user->assignRole('Delivery');

            return $user->load(['roles', 'wallet']);
        });
    }
}