<?php

namespace App\Repositories\Eloquent;

use App\Models\KategoriSampah;
use App\Repositories\Contracts\KategoriSampahRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class KategoriSampahRepository implements KategoriSampahRepositoryInterface
{
    public function all(): Collection
    {
        return KategoriSampah::query()->latest()->get();
    }

    public function findOrFail(int $id): KategoriSampah
    {
        return KategoriSampah::query()->findOrFail($id);
    }

    public function create(array $data): KategoriSampah
    {
        return KategoriSampah::query()->create($data);
    }

    public function update(KategoriSampah $kategoriSampah, array $data): KategoriSampah
    {
        $kategoriSampah->update($data);

        return $kategoriSampah->refresh();
    }

    public function delete(KategoriSampah $kategoriSampah): void
    {
        $kategoriSampah->delete();
    }
}
