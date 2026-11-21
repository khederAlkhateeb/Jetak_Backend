<?php

namespace App\Http\Resources\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->whenLoaded('wallet', fn () => $this->wallet?->user);

        return [
            'id'            => $this->id,
            'wallet_id'     => $this->wallet_id,
            'type'          => $this->type,
            'amount'        => (float) $this->amount,
            'balance' => isset($this->balance) ? (float) $this->balance : null,
            'description'   => $this->description,
            'user'          => $this->when(
                $user instanceof \App\Models\User,
                fn () => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ]
            ),
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}