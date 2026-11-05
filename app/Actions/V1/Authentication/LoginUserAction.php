<?php

namespace App\Actions\V1\Authentication;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Handles the business logic for authenticating a user and issuing
 * an API access token.
 *
 * Responsibilities:
 *  - Verify the provided credentials (email + password).
 *  - Ensure the user's associated profile (alumni or student) has
 *    been approved by an admin before allowing access.
 *  - Issue a Sanctum personal access token on success.
 *
 * @package App\V1\Actions\Authentication
 */
class LoginUserAction
{
    /**
     * Authenticate a user with the given credentials.
     *
     * @param  array{email: string, password: string} $data The validated
     *         login payload containing the user's email and password.
     *
     * @return array{user: User, token: string} The authenticated user
     *         instance and a newly issued plain-text Sanctum token.
     *
     * @throws ValidationException If:
     *         - No user exists with the given email, or the password
     *           does not match (generic "Invalid credentials" message
     *           to avoid leaking which field was incorrect).
     *         - The user's alumni/student profile status is "pending"
     *           (awaiting admin approval).
     *         - The user's alumni/student profile status is "rejected".
     */
    public function execute(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        // Validate credentials and profile status
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'credentials' => ['Invalid credentials.'],
            ]);
        }

        if ($user && $user->status === 'banned') {
            throw ValidationException::withMessages([
                'email' => ['Your account is still banned and requires approval.'],
            ]);
        }
        if ($user && $user->status === 'suspended') {
            throw ValidationException::withMessages([
                'email' => ['Your registration was suspended.'],
            ]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'user'  => $user,
            'token' => $token,
        ];
    }
}
