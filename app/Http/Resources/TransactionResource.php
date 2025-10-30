<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   /**
     * Transform the resource into an array.
     */




    public function toArray(Request $request): array
    {


        return [
            'id'          => $this->id,
            'type'        => $this->type, // contribution, withdrawal, etc.
            'amount'      => $this->amount,
            'status'      => $this->status, // pending, approved, rejected
            'description' => $this->description,
           'account_type' => $this->account?->account_type,
            'created_at'  => $this->created_at?->toDateTimeString(),
            'updated_at'  => $this->updated_at?->toDateTimeString(),
            'receipt_url' => $this->receipt_url
            ?? ($this->receipt_path ? url("storage/{$this->receipt_path}") : null),
            // Include user info (for admin views)
            'user' => $this->whenLoaded('account.user', function () {
                return [
                    'id'    => $this->account->user->id,
                    'name'  => $this->account->user->name,
                    'email' => $this->account->user->email,
                ];
            }),
        ];
    }
}
