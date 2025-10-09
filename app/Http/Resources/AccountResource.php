<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'account_type'   => $this->account_type,
            'account_number' => $this->account_number,
            'balance'        => $this->balance,
            'status'         => $this->status,
            'transactions'   => TransactionResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
