<?php

namespace App\Actions\V1\Profile;

use App\Models\User;



class UpdateHousingInfoAction
{
    /**
     * Execute the action to update the user's housing information.
     *
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function execute(User $user, array $data): bool
    {
        $user->update([
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'] ?? $user->phone,
            'building_number' => $data['building_number'] ?? $user->building_number,
            'floor' => $data['floor'] ?? $user->floor,
            'room_number' => $data['room_number'] ?? $user->room_number,
        ]);

        return true;
    }
}