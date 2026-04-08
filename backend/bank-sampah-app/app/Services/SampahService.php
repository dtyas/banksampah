<?php

namespace App\Services;

use App\Models\Sampah;
use App\Repositories\Contracts\SampahRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SampahService
{
    public function __construct(private readonly SampahRepositoryInterface $sampahRepository) {}

    public function all(): Collection
    {
        return $this->sampahRepository->allWithKategori();
    }

    public function findOrFail(int $id): Sampah
    {
        return $this->sampahRepository->findWithKategoriOrFail($id);
    }

    public function create(array $data): Sampah
    {
        $sampah = $this->sampahRepository->create($data)->load('kategoriSampah');

        return $sampah;
    }

    public function update(int $id, array $data): Sampah
    {
        $sampah = $this->findOrFail($id);
        $updated = $this->sampahRepository->update($sampah, $data)->load('kategoriSampah');

        return $updated;
    }

    public function delete(int $id): void
    {
        $sampah = $this->findOrFail($id);
        $this->sampahRepository->delete($sampah);
    }
}
