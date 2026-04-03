<?php

namespace App\Http\Controllers;

use App\Models\Sampah;
use Illuminate\Http\Request;

class SampahController extends Controller
{
    public function index()
    {
        return response()->json(
            Sampah::with('kategoriSampah')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_sampah_id' => 'required|exists:kategori_sampah,id',
            'nama_sampah' => 'required|string|max:255',
            'harga_per_kg' => 'required|numeric|min:0',
        ]);

        $sampah = Sampah::create($validated);

        return response()->json([
            'message' => 'Data sampah berhasil ditambahkan',
            'data' => $sampah,
        ], 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Sampah::with('kategoriSampah')->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $sampah = Sampah::findOrFail($id);

        $validated = $request->validate([
            'kategori_sampah_id' => 'required|exists:kategori_sampah,id',
            'nama_sampah' => 'required|string|max:255',
            'harga_per_kg' => 'required|numeric|min:0',
        ]);

        $sampah->update($validated);

        return response()->json([
            'message' => 'Data sampah berhasil diupdate',
            'data' => $sampah,
        ]);
    }

    public function destroy(string $id)
    {
        $sampah = Sampah::findOrFail($id);
        $sampah->delete();

        return response()->json([
            'message' => 'Data sampah berhasil dihapus',
        ]);
    }
}
