<?php

namespace App\Services;

use App\Models\KategoriSampah;
use App\Repositories\Contracts\KategoriSampahRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class KategoriSampahService
{
    public function __construct(private readonly KategoriSampahRepositoryInterface $kategoriRepository) {}

    public function all(): Collection
    {
        return $this->kategoriRepository->all();
    }

    public function findOrFail(int $id): KategoriSampah
    {
        return $this->kategoriRepository->findOrFail($id);
    }

    public function create(array $data): KategoriSampah
    {
        $kategori = $this->kategoriRepository->create($data);

        return $kategori;
    }

    public function update(int $id, array $data): KategoriSampah
    {
        $kategori = $this->findOrFail($id);
        $updated = $this->kategoriRepository->update($kategori, $data);

        return $updated;
    }

    public function delete(int $id): void
    {
        $kategori = $this->findOrFail($id);
        $this->kategoriRepository->delete($kategori);
    }
}
