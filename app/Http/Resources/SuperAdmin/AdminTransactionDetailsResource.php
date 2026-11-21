<?php

namespace App\Http\Resources\SuperAdmin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminTransactionDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $wallet = $this->whenLoaded('wallet');
        $user = $wallet? $wallet->user : null;

        return [
            'id'            => $this->id,
            'type'          => $this->type,
            'amount'        => (float) $this->amount,
            'balance' => isset($this->balance) ? (float) $this->balance : null,
            'description'   => $this->description,
            'wallet'        => $this->when(
                $wallet !== null,
                fn () => [
                    'id'              => $wallet->id,
                    'current_balance' => (float) $wallet->balance,
                ]
            ),
            'user'          => $this->when(
                $user instanceof User,
                fn () => [
                    'id'              => $user->id,
                    'name'            => $user->name,
                    'email'           => $user->email,
                    'phone'           => $user->phone,
                    'status'          => $user->status,
                    'building_number' => $user->building_number,
                    'room_number'     => $user->room_number,
                    'role'           => $user->getRole(),
                ]
            ),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}