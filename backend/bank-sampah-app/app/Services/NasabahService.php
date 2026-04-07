<?php

namespace App\Services;

use App\Models\Nasabah;
use App\Repositories\Contracts\NasabahRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class NasabahService
{
    public function __construct(private readonly NasabahRepositoryInterface $nasabahRepository) {}

    public function all(): Collection
    {
        return Cache::remember($this->cacheKeyAll(), $this->cacheTtl(), function (): Collection {
            return $this->nasabahRepository->all();
        });
    }

    public function findOrFail(int $id): Nasabah
    {
        return Cache::remember($this->cacheKeyById($id), $this->cacheTtl(), function () use ($id): Nasabah {
            return $this->nasabahRepository->findOrFail($id);
        });
    }

    public function create(array $data): Nasabah
    {
        $nasabah = $this->nasabahRepository->create($data);
        $this->bustCache();

        return $nasabah;
    }

    public function update(int $id, array $data): Nasabah
    {
        $nasabah = $this->findOrFail($id);
        $updated = $this->nasabahRepository->update($nasabah, $data);
        $this->bustCache($id);

        return $updated;
    }

    public function delete(int $id): void
    {
        $nasabah = $this->findOrFail($id);
        $this->nasabahRepository->delete($nasabah);
        $this->bustCache($id);
    }

    private function cacheTtl(): int
    {
        return (int) env('CACHE_TTL_SECONDS', 300);
    }

    private function cacheKeyAll(): string
    {
        return 'nasabah.all';
    }

    private function cacheKeyById(int $id): string
    {
        return 'nasabah.' . $id;
    }

    private function bustCache(?int $id = null): void
    {
        Cache::forget($this->cacheKeyAll());

        if ($id !== null) {
            Cache::forget($this->cacheKeyById($id));
        }
    }
}
