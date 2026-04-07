<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Repositories\Contracts\TransaksiRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TransaksiService
{
    public function __construct(private readonly TransaksiRepositoryInterface $transaksiRepository) {}

    public function all(): Collection
    {
        return Cache::remember($this->cacheKeyAll(), $this->cacheTtl(), function (): Collection {
            return $this->transaksiRepository->allWithRelations();
        });
    }

    public function findOrFail(int $id): Transaksi
    {
        return Cache::remember($this->cacheKeyById($id), $this->cacheTtl(), function () use ($id): Transaksi {
            return $this->transaksiRepository->findWithRelationsOrFail($id);
        });
    }

    public function create(array $data): Transaksi
    {
        $transaksi = $this->transaksiRepository->createWithItems($data);
        $this->bustCache();

        return $transaksi;
    }

    public function update(int $id, array $data): Transaksi
    {
        $transaksi = $this->findOrFail($id);
        $updated = $this->transaksiRepository->updateWithItems($transaksi, $data);
        $this->bustCache($id);

        return $updated;
    }

    public function delete(int $id): void
    {
        $transaksi = $this->findOrFail($id);
        $this->transaksiRepository->delete($transaksi);
        $this->bustCache($id);
    }

    private function cacheTtl(): int
    {
        return (int) env('CACHE_TTL_SECONDS', 300);
    }

    private function cacheKeyAll(): string
    {
        return 'transaksi.all';
    }

    private function cacheKeyById(int $id): string
    {
        return 'transaksi.' . $id;
    }

    private function bustCache(?int $id = null): void
    {
        Cache::forget($this->cacheKeyAll());

        if ($id !== null) {
            Cache::forget($this->cacheKeyById($id));
        }
    }
}
