<?php

namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use Illuminate\Http\Request;

class KategoriSampahController extends Controller
{
    public function index()
    {
        return response()->json(KategoriSampah::latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = KategoriSampah::create($validated);

        return response()->json([
            'message' => 'Kategori sampah berhasil ditambahkan',
            'data' => $kategori,
        ], 201);
    }

    public function show(string $id)
    {
        return response()->json(KategoriSampah::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $kategori = KategoriSampah::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori->update($validated);

        return response()->json([
            'message' => 'Kategori sampah berhasil diupdate',
            'data' => $kategori,
        ]);
    }

    public function destroy(string $id)
    {
        $kategori = KategoriSampah::findOrFail($id);
        $kategori->delete();

        return response()->json([
            'message' => 'Kategori sampah berhasil dihapus',
        ]);
    }
}
