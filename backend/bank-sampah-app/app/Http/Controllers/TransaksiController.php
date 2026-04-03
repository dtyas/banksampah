<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        return response()->json(
            Transaksi::with(['user', 'nasabah', 'detailTransaksi.sampah', 'pembayaran'])
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nasabah_id' => 'required|exists:nasabah,id',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.sampah_id' => 'required|exists:sampah,id',
            'items.*.berat' => 'required|numeric|min:0.01',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalBerat = collect($validated['items'])->sum('berat');
            $totalHarga = collect($validated['items'])->sum('subtotal');

            $transaksi = Transaksi::create([
                'user_id' => $validated['user_id'],
                'nasabah_id' => $validated['nasabah_id'],
                'tanggal' => $validated['tanggal'],
                'total_berat' => $totalBerat,
                'total_harga' => $totalHarga,
            ]);

            foreach ($validated['items'] as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'sampah_id' => $item['sampah_id'],
                    'berat' => $item['berat'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil ditambahkan',
                'data' => $transaksi->load(['user', 'nasabah', 'detailTransaksi.sampah']),
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menambahkan transaksi',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        return response()->json(
            Transaksi::with(['user', 'nasabah', 'detailTransaksi.sampah', 'pembayaran'])
                ->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nasabah_id' => 'required|exists:nasabah,id',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.sampah_id' => 'required|exists:sampah,id',
            'items.*.berat' => 'required|numeric|min:0.01',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalBerat = collect($validated['items'])->sum('berat');
            $totalHarga = collect($validated['items'])->sum('subtotal');

            $transaksi->update([
                'user_id' => $validated['user_id'],
                'nasabah_id' => $validated['nasabah_id'],
                'tanggal' => $validated['tanggal'],
                'total_berat' => $totalBerat,
                'total_harga' => $totalHarga,
            ]);

            $transaksi->detailTransaksi()->delete();

            foreach ($validated['items'] as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'sampah_id' => $item['sampah_id'],
                    'berat' => $item['berat'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil diupdate',
                'data' => $transaksi->load(['user', 'nasabah', 'detailTransaksi.sampah']),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal mengupdate transaksi',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return response()->json([
            'message' => 'Transaksi berhasil dihapus',
        ]);
    }
}
