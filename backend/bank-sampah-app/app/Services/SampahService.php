<?php

namespace App\Services;

use App\Models\Sampah;
use App\Repositories\Contracts\SampahRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class SampahService
{
    public function __construct(private readonly SampahRepositoryInterface $sampahRepository) {}

    public function all(): Collection
    {
        return Cache::remember($this->cacheKeyAll(), $this->cacheTtl(), function (): Collection {
            return $this->sampahRepository->allWithKategori();
        });
    }

    public function findOrFail(int $id): Sampah
    {
        return Cache::remember($this->cacheKeyById($id), $this->cacheTtl(), function () use ($id): Sampah {
            return $this->sampahRepository->findWithKategoriOrFail($id);
        });
    }

    public function create(array $data): Sampah
    {
        $sampah = $this->sampahRepository->create($data)->load('kategoriSampah');
        $this->bustCache();

        return $sampah;
    }

    public function update(int $id, array $data): Sampah
    {
        $sampah = $this->findOrFail($id);
        $updated = $this->sampahRepository->update($sampah, $data)->load('kategoriSampah');
        $this->bustCache($id);

        return $updated;
    }

    public function delete(int $id): void
    {
        $sampah = $this->findOrFail($id);
        $this->sampahRepository->delete($sampah);
        $this->bustCache($id);
    }

    private function cacheTtl(): int
    {
        return (int) env('CACHE_TTL_SECONDS', 300);
    }

    private function cacheKeyAll(): string
    {
        return 'sampah.all';
    }

    private function cacheKeyById(int $id): string
    {
        return 'sampah.' . $id;
    }

    private function bustCache(?int $id = null): void
    {
        Cache::forget($this->cacheKeyAll());

        if ($id !== null) {
            Cache::forget($this->cacheKeyById($id));
        }
    }
}
