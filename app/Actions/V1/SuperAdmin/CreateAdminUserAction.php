<?php

namespace App\Actions\V1\SuperAdmin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserAction
{
    /**
     * Execute the action to create a new administrative/operational user account.
     *
     * @param array<string, mixed> $data
     * @return User
     */
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'],
                'password'          => Hash::make($data['password']),
                'status'            => $data['status'] ?? 'active',
                'email_verified_at' => now(),
            ]);

            $user->assignRole($data['role']);

            $user->wallet()->create([
                'balance' => 0.00,
            ]);

            return $user->load('roles');
        });
    }
}