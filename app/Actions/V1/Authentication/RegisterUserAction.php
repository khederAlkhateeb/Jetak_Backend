<?php

namespace App\Actions\V1\Authentication;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

/**
 * Handles the business logic for registering a new user.
 *
 * Responsibilities:
 *  - Validate the user's input data.
 *  - Create a new user account with the provided information.
 *  - Hash the user's password before storing it.
 *  - Assign the appropriate role to the new user.
 *  - Issue a Sanctum personal access token on success.
 *
 * @package App\V1\Actions\Authentication
 */


class RegisterUserAction
{
    /**
     * Register a new user with the given data.
     *
     * @param  array{email: string, password: string, name: string} $data The validated
     *         registration payload containing the user's email, password, and name.
     *         The user's account will be marked as "active" and will be able to log in immediately.
     *         The user will be able to access the application as soon as their account is created.
     *  - Assign the appropriate role to the new user.
     *  - Issue a Sanctum personal access token on success.
     *
     * @package App\V1\Actions\Authentication
     */
    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => 'active', // Mark the user as active
                'phone' => $data['phone'] ?? null,
                'building_number' => $data['building_number'] ?? null,
                'floor' => $data['floor'] ?? null,
                'room_number' => $data['room_number'] ?? null,
            ]);

            $user->assignRole('Student'); // Assign the "user" role to the newly registered user
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }
}
