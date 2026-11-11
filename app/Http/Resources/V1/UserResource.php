<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'status'          => $this->status,
            'building_number' => $this->building_number,
            'floor'           => $this->floor,
            'room_number'     => $this->room_number,
            'role'            => $this->getRole(),

            'wallet'              => $this->whenLoaded('wallet'),
            'bread_subscriptions' => $this->whenLoaded('breadSubscriptions'),
            'bookings'            => $this->whenLoaded('bookings'),

            'created_at'      => $this->created_at,
        ];
    }
}