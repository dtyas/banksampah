<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Nasabah;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanController extends ApiController
{
    public function summary(Request $request): JsonResponse
    {
        $query = $this->filteredTransaksiQuery($request);

        $topSampah = \App\Models\DetailTransaksi::query()
            ->whereIn('transaksi_id', (clone $query)->pluck('id'))
            ->selectRaw('sampah_id, SUM(berat) as total_berat')
            ->groupBy('sampah_id')
            ->orderByDesc('total_berat')
            ->with('sampah.kategoriSampah')
            ->first();

        $data = [
            'total_nasabah' => Nasabah::query()->count(),
            'total_transaksi' => (clone $query)->count(),
            'total_berat' => (float) ((clone $query)->sum('total_berat') ?? 0),
            'total_harga' => (float) ((clone $query)->sum('total_harga') ?? 0),
            'total_pembayaran_berhasil' => (float) Pembayaran::query()
                ->when($request->filled('start_date'), fn(Builder $q) => $q->whereDate('tanggal', '>=', $request->string('start_date')))
                ->when($request->filled('end_date'), fn(Builder $q) => $q->whereDate('tanggal', '<=', $request->string('end_date')))
                ->when($request->filled('nasabah_id'), fn(Builder $q) => $q->where('nasabah_id', $request->integer('nasabah_id')))
                ->where('status', 'berhasil')
                ->sum('jumlah'),
            'top_sampah' => $topSampah?->sampah?->nama_sampah ?? '-',
            'top_kategori' => $topSampah?->sampah?->kategoriSampah?->nama_kategori ?? '-',
            'top_berat' => (float) ($topSampah?->total_berat ?? 0),
        ];

        return $this->successResponse('Ringkasan laporan berhasil diambil', $data);
    }

    public function chart(Request $request): JsonResponse
    {
        $format = "%Y-%m";
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $format = "%Y-%m-%d";
        }

        $rows = $this->filteredTransaksiQuery($request)
            ->selectRaw("DATE_FORMAT(tanggal, '$format') as period")
            ->selectRaw('COUNT(*) as total_transaksi')
            ->selectRaw('SUM(total_harga) as total_harga')
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $data = [
            'labels' => $rows->pluck('period')->all(),
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'key' => 'total_transaksi',
                    'data' => $rows->pluck('total_transaksi')->map(fn($v) => (int) ($v ?? 0))->all(),
                ],
                [
                    'label' => 'Total Harga',
                    'key' => 'total_harga',
                    'data' => $rows->pluck('total_harga')->map(fn($v) => (float) ($v ?? 0))->all(),
                ],
            ],
        ];

        return $this->successResponse('Data chart laporan berhasil diambil', $data);
    }

    public function transaksi(Request $request): JsonResponse
    {
        $transaksi = $this->filteredTransaksiQuery($request)
            ->with(['nasabah', 'detailTransaksi.sampah', 'pembayaran'])
            ->latest('tanggal')
            ->get();

        return $this->successResponse('Data laporan transaksi berhasil diambil', $transaksi);
    }

    private function filteredTransaksiQuery(Request $request): Builder
    {
        return Transaksi::query()
            ->when($request->filled('start_date'), fn(Builder $q) => $q->whereDate('tanggal', '>=', $request->string('start_date')))
            ->when($request->filled('end_date'), fn(Builder $q) => $q->whereDate('tanggal', '<=', $request->string('end_date')))
            ->when($request->filled('nasabah_id'), fn(Builder $q) => $q->where('nasabah_id', $request->integer('nasabah_id')))
            ->when($request->filled('status_pembayaran'), function (Builder $q) use ($request): void {
                $q->whereHas('pembayaran', fn(Builder $paymentQuery) => $paymentQuery->where('status', $request->string('status_pembayaran')));
            })
            ->when($request->filled('sampah_id'), function (Builder $q) use ($request): void {
                $q->whereHas('detailTransaksi', fn(Builder $detailQuery) => $detailQuery->where('sampah_id', $request->integer('sampah_id')));
            });
    }
}
