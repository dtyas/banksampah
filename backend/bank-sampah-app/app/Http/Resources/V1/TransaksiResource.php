<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\DetailTransaksiResource;
use App\Http\Resources\V1\NasabahResource;
use App\Http\Resources\V1\PembayaranResource;

class TransaksiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nasabah_id' => $this->nasabah_id,
            'tanggal' => $this->tanggal,
            'total_berat' => $this->total_berat,
            'total_harga' => $this->total_harga,
            'user' => [
                'id' => $this->whenLoaded('user', fn() => $this->user?->id),
                'nama' => $this->whenLoaded('user', fn() => $this->user?->nama),
                'email' => $this->whenLoaded('user', fn() => $this->user?->email),
            ],
            'nasabah' => new NasabahResource($this->whenLoaded('nasabah')),
            'detail_transaksi' => DetailTransaksiResource::collection($this->whenLoaded('detailTransaksi')),
            'pembayaran' => new PembayaranResource($this->whenLoaded('pembayaran')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
