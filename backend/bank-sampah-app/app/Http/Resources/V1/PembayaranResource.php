<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PembayaranResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaksi_id' => $this->transaksi_id,
            'jumlah' => $this->jumlah,
            'metode' => $this->metode,
            'status' => $this->status,
            'tanggal' => $this->tanggal,
            'verified_at' => $this->verified_at,
            'verified_by' => $this->verified_by,
            'transaksi' => $this->whenLoaded('transaksi', function (): ?array {
                if (! $this->transaksi) {
                    return null;
                }

                return [
                    'id' => $this->transaksi->id,
                    'nasabah_id' => $this->transaksi->nasabah_id,
                    'tanggal' => $this->transaksi->tanggal,
                    'total_harga' => $this->transaksi->total_harga,
                ];
            }),
            'verifier' => $this->whenLoaded('verifier', function (): ?array {
                if (! $this->verifier) {
                    return null;
                }

                return [
                    'id' => $this->verifier->id,
                    'nama' => $this->verifier->nama,
                    'email' => $this->verifier->email,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
