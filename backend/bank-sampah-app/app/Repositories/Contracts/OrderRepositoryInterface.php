<?php

namespace App\Repositories\Contracts;

use App\Models\Transaksi;
use Illuminate\Support\Collection;

/**
 * Lapisan kontrak untuk akses data Order.
 *
 * Catatan untuk programmer baru:
 * - Interface ini adalah "janji" method yang boleh dipakai Service.
 * - Service tidak perlu tahu query Eloquent detailnya, cukup panggil kontrak ini.
 */
interface OrderRepositoryInterface
{
    /**
     * Mengambil data master sampah berdasarkan daftar ID.
     *
     * @param array<int, int> $sampahIds Daftar ID sampah yang diminta user.
     * @return Collection<int, \App\Models\Sampah> Data sampah dalam bentuk collection Eloquent.
     */
    public function findSampahByIds(array $sampahIds): Collection;

    /**
     * Menyimpan 1 order transaksi beserta item detail ke database.
     *
     * @param array<string, mixed> $payload Data final yang sudah dihitung oleh Service.
     * @return Transaksi Data transaksi yang sudah tersimpan lengkap dengan relasi.
     */
    public function storeOrder(array $payload): Transaksi;
}
