<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array{"activity_log": array{"bread_subscriptions": mixed|\Illuminate\Http\Resources\MissingValue, "recent_bookings": mixed|\Illuminate\Http\Resources\MissingValue, "recent_transactions": mixed|\Illuminate\Http\Resources\MissingValue, "created_at": mixed, email: mixed, "email_verified_at": mixed, id: mixed, name: mixed, permissions: mixed, phone: mixed, residence: array{"building_number": mixed, floor: mixed, "room_number": mixed}, roles: mixed, status: mixed, "updated_at": mixed, wallet: mixed|\Illuminate\Http\Resources\MissingValue}}
     */
    public function toArray(Request $request): array
    {
        // Transform the resource into an array.
        return [
            //User basic info
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'status'            => $this->status,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            //User residence info
            'residence' => [
                'building_number' => $this->building_number ?? null,
                'floor'           => $this->floor ?? null,
                'room_number'     => $this->room_number ?? null,
            ],
            //User role
            'role'       => $this->getRole(),
            //User wallet info
            'wallet' => $this->whenLoaded('wallet', fn () => [
                'id'      => $this->wallet->id,
                'balance' => (float) $this->wallet->balance,
            ]),
            //User activity log
            'activity_log' => [
                'recent_transactions' => $this->when(
                    $this->relationLoaded('wallet') && $this->wallet?->relationLoaded('transactions'),
                    fn () => $this->wallet->transactions
                ),
                'recent_bookings' => $this->whenLoaded('bookings'),
                'bread_subscriptions' => $this->whenLoaded('breadSubscriptions'),
            ],
            //Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}