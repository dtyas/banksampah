<?php

namespace App\Repositories\Eloquent;

use App\Models\Sampah;
use App\Repositories\Contracts\SampahRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SampahRepository implements SampahRepositoryInterface
{
    public function allWithKategori(): Collection
    {
        return Sampah::query()->with('kategoriSampah')->latest()->get();
    }

    public function findWithKategoriOrFail(int $id): Sampah
    {
        return Sampah::query()->with('kategoriSampah')->findOrFail($id);
    }

    public function create(array $data): Sampah
    {
        return Sampah::query()->create($data);
    }

    public function update(Sampah $sampah, array $data): Sampah
    {
        $sampah->update($data);

        return $sampah->refresh();
    }

    public function delete(Sampah $sampah): void
    {
        $sampah->delete();
    }
}
