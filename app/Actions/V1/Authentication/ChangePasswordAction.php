<?php

namespace App\Actions\V1\Authentication;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePasswordAction
{
    /**
     * Execute the action to change the user's password.
     *
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function execute(User $user, array $data): bool
    {
        // Check if the current password matches the user's existing password
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return true;
    }
}