<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;

class NasabahController extends Controller
{
    public function index()
    {
        $nasabah = Nasabah::latest()->get();

        return response()->json($nasabah);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $nasabah = Nasabah::create($validated);

        return response()->json([
            'message' => 'Nasabah berhasil ditambahkan',
            'data' => $nasabah
        ], 201);
    }

    public function show(string $id)
    {
        $nasabah = Nasabah::findOrFail($id);

        return response()->json($nasabah);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $nasabah = Nasabah::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $nasabah->update($validated);

        return response()->json([
            'message' => 'Data nasabah berhasil diupdate',
            'data' => $nasabah
        ]);
    }

    public function destroy(string $id)
    {
        $nasabah = Nasabah::findOrFail($id);
        $nasabah->delete();

        return response()->json([
            'message' => 'Data nasabah berhasil dihapus'
        ]);
    }
}