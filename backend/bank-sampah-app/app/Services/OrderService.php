<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Lapisan Business Logic untuk fitur Store Order.
 *
 * Penjelasan non-tech:
 * - Service ini ibarat "otak proses" sebelum data disimpan.
 * - Di sini kita cek aturan main: stok, hitung subtotal, hitung diskon.
 * - Setelah data rapi, baru dilempar ke Repository untuk disimpan.
 */
class OrderService
{
    public function __construct(private readonly OrderRepositoryInterface $orderRepository) {}

    /**
     * Menjalankan alur lengkap pembuatan order.
     *
     * Aturan bisnis yang dijalankan:
     * 1) Pastikan master sampah untuk item order tersedia.
     * 2) Cek stok per item (demo rule: batas maksimal kg per item per order).
     * 3) Hitung subtotal otomatis dari berat x harga_per_kg.
     * 4) Hitung total berat dan total harga sebelum diskon.
     * 5) Terapkan diskon persen pada total harga.
     * 6) Simpan hasil final lewat Repository.
     *
     * @param array<string, mixed> $payload Data request yang sudah lolos validasi controller.
     * @return array{transaksi: \App\Models\Transaksi, ringkasan: array<string, float|int>} Hasil transaksi + ringkasan hitung.
     *
     * @throws ValidationException Jika data item/sampah tidak valid atau stok tidak cukup.
     */
    public function storeOrder(array $payload): array
    {
        /** @var array<int, array{sampah_id:int, berat:float|int}> $items */
        $items = $payload['items'];
        $sampahIds = array_values(array_unique(array_map(static fn(array $item): int => (int) $item['sampah_id'], $items)));

        $sampahCollection = $this->orderRepository->findSampahByIds($sampahIds);
        $sampahMap = $sampahCollection->keyBy('id');

        $this->assertSampahExists($sampahIds, $sampahMap);

        $maxStockPerItemKg = (float) env('ORDER_DEMO_MAX_STOCK_PER_ITEM_KG', 1000);
        $this->assertStockAvailability($items, $maxStockPerItemKg);

        $calculatedItems = [];
        $totalBerat = 0.0;
        $totalHargaSebelumDiskon = 0.0;

        foreach ($items as $item) {
            $sampah = $sampahMap->get((int) $item['sampah_id']);
            $berat = (float) $item['berat'];
            $hargaPerKg = (float) $sampah->harga_per_kg;
            $subtotal = $berat * $hargaPerKg;

            $calculatedItems[] = [
                'sampah_id' => (int) $item['sampah_id'],
                'berat' => $berat,
                'subtotal' => $subtotal,
            ];

            $totalBerat += $berat;
            $totalHargaSebelumDiskon += $subtotal;
        }

        $diskonPersen = (float) ($payload['diskon_persen'] ?? 0);
        $nilaiDiskon = $totalHargaSebelumDiskon * ($diskonPersen / 100);
        $totalHargaSetelahDiskon = $totalHargaSebelumDiskon - $nilaiDiskon;

        $finalPayload = [
            'user_id' => (int) $payload['user_id'],
            'nasabah_id' => (int) $payload['nasabah_id'],
            'tanggal' => (string) ($payload['tanggal'] ?? now()->toDateString()),
            'items' => $calculatedItems,
            'total_berat' => $totalBerat,
            'total_harga_sebelum_diskon' => $totalHargaSebelumDiskon,
            'total_harga_setelah_diskon' => $totalHargaSetelahDiskon,
        ];

        $transaksi = $this->orderRepository->storeOrder($finalPayload);

        return [
            'transaksi' => $transaksi,
            'ringkasan' => [
                'total_berat' => $totalBerat,
                'total_harga_sebelum_diskon' => $totalHargaSebelumDiskon,
                'diskon_persen' => $diskonPersen,
                'nilai_diskon' => $nilaiDiskon,
                'total_harga_setelah_diskon' => $totalHargaSetelahDiskon,
            ],
        ];
    }

    /**
     * Memastikan semua ID sampah di request benar-benar ditemukan di database.
     *
     * @param array<int, int> $requestedSampahIds
     * @param Collection<int|string, mixed> $sampahMap
     * @return void
     *
     * @throws ValidationException Jika ada sampah_id yang tidak ditemukan.
     */
    private function assertSampahExists(array $requestedSampahIds, Collection $sampahMap): void
    {
        $missing = array_values(array_diff($requestedSampahIds, $sampahMap->keys()->map(static fn($id): int => (int) $id)->all()));

        if ($missing === []) {
            return;
        }

        throw ValidationException::withMessages([
            'items' => ['Beberapa item sampah tidak ditemukan: ' . implode(', ', $missing)],
        ]);
    }

    /**
     * Contoh aturan stok sederhana untuk handover project.
     *
     * Penjelasan:
     * - Di proyek real, stok biasanya dicek ke tabel inventory.
     * - Untuk contoh ini, kita pakai batas aman per item agar alur business rule terlihat.
     *
     * @param array<int, array{sampah_id:int, berat:float|int}> $items
     * @param float $maxStockPerItemKg
     * @return void
     *
     * @throws ValidationException Jika berat item melebihi batas stok demo.
     */
    private function assertStockAvailability(array $items, float $maxStockPerItemKg): void
    {
        foreach ($items as $item) {
            if ((float) $item['berat'] <= $maxStockPerItemKg) {
                continue;
            }

            throw ValidationException::withMessages([
                'items' => [
                    'Stok tidak cukup untuk sampah ID ' . $item['sampah_id'] . '. Maksimal per item: ' . $maxStockPerItemKg . ' kg.',
                ],
            ]);
        }
    }
}
