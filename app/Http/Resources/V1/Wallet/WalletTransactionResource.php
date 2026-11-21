<?php

namespace App\Http\Resources\V1\Wallet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'wallet_id'     => $this->wallet_id,
            'type'          => $this->type,
            'amount'        => (float) $this->amount,
            'description'   => $this->description,
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}