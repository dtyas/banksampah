<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\SampahResource;

class DetailTransaksiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaksi_id' => $this->transaksi_id,
            'sampah_id' => $this->sampah_id,
            'berat' => $this->berat,
            'subtotal' => $this->subtotal,
            'sampah' => $this->sampah_id ? new SampahResource($this->whenLoaded('sampah')) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
