<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NasabahResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nama' => $this->nama,
            'alamat' => $this->alamat,
            'no_hp' => $this->no_hp,
            'payout_channel' => $this->payout_channel,
            'account_number' => $this->account_number,
            'account_holder_name' => $this->account_holder_name,
            'user' => $this->whenLoaded('user', function (): array {
                return [
                    'id' => $this->user?->id,
                    'nama' => $this->user?->nama,
                    'email' => $this->user?->email,
                    'role' => $this->user?->role,
                    'status' => $this->user?->status,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
