<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\KategoriSampahResource;

class SampahResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kategori_sampah_id' => $this->kategori_sampah_id,
            'nama_sampah' => $this->nama_sampah,
            'harga_per_kg' => $this->harga_per_kg,
            'kategori_sampah' => new KategoriSampahResource($this->whenLoaded('kategoriSampah')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
