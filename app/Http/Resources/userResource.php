<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class userResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


      public function toArray(Request $request): array
    {
        return [
              'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone_no'     => $this->phone_no,
            'role'         => $this->role,
            'accounts'     => AccountResource::collection($this->whenLoaded('accounts')),
            'total_balance'=> $this->whenLoaded('accounts', fn () => $this->accounts->sum('balance')),
            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),
        ];
    }




    // public function toArray(Request $request): array
    // {
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name,
    //         'email' => $this->email,
    //         'phone_no' => $this->phone_no,
    //         'role'     => $this->role,
    //         // 👇 Add wallet/account info
    //         'account' => $this->whenLoaded('account', function () {
    //             return [
    //                 'account_number' => $this->account->account_number,
    //                 'balance'        => $this->account->balance,
    //                 'status'         => $this->account->status,
    //             ];
    //         }),

    //         'created_at' => $this->created_at?->toDateTimeString(),
    //         'updated_at' => $this->updated_at?->toDateTimeString(),
    //         // Add other safe fields as needed
    //     ];
    // }
}
