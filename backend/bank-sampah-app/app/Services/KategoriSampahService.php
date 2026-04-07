<?php

namespace App\Services;

use App\Models\KategoriSampah;
use App\Repositories\Contracts\KategoriSampahRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class KategoriSampahService
{
    public function __construct(private readonly KategoriSampahRepositoryInterface $kategoriRepository) {}

    public function all(): Collection
    {
        return Cache::remember($this->cacheKeyAll(), $this->cacheTtl(), function (): Collection {
            return $this->kategoriRepository->all();
        });
    }

    public function findOrFail(int $id): KategoriSampah
    {
        return Cache::remember($this->cacheKeyById($id), $this->cacheTtl(), function () use ($id): KategoriSampah {
            return $this->kategoriRepository->findOrFail($id);
        });
    }

    public function create(array $data): KategoriSampah
    {
        $kategori = $this->kategoriRepository->create($data);
        $this->bustCache();

        return $kategori;
    }

    public function update(int $id, array $data): KategoriSampah
    {
        $kategori = $this->findOrFail($id);
        $updated = $this->kategoriRepository->update($kategori, $data);
        $this->bustCache($id);

        return $updated;
    }

    public function delete(int $id): void
    {
        $kategori = $this->findOrFail($id);
        $this->kategoriRepository->delete($kategori);
        $this->bustCache($id);
    }

    private function cacheTtl(): int
    {
        return (int) env('CACHE_TTL_SECONDS', 300);
    }

    private function cacheKeyAll(): string
    {
        return 'kategori_sampah.all';
    }

    private function cacheKeyById(int $id): string
    {
        return 'kategori_sampah.' . $id;
    }

    private function bustCache(?int $id = null): void
    {
        Cache::forget($this->cacheKeyAll());

        if ($id !== null) {
            Cache::forget($this->cacheKeyById($id));
        }
    }
}
