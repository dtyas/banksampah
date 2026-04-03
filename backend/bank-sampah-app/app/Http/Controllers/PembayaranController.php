<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index()
    {
        return response()->json(
            Pembayaran::with('transaksi')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|string|max:255',
            'status' => 'required|in:menunggu,diverifikasi,diproses,berhasil,ditolak',
            'tanggal' => 'required|date',
        ]);

        $pembayaran = Pembayaran::create($validated);

        return response()->json([
            'message' => 'Data pembayaran berhasil ditambahkan',
            'data' => $pembayaran,
        ], 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Pembayaran::with('transaksi')->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $validated = $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|string|max:255',
            'status' => 'required|in:menunggu,diverifikasi,diproses,berhasil,ditolak',
            'tanggal' => 'required|date',
        ]);

        $pembayaran->update($validated);

        return response()->json([
            'message' => 'Data pembayaran berhasil diupdate',
            'data' => $pembayaran,
        ]);
    }

    public function destroy(string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->delete();

        return response()->json([
            'message' => 'Data pembayaran berhasil dihapus',
        ]);
    }
}
