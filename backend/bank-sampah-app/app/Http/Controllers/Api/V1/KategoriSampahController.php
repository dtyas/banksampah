<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\KategoriSampahResource;
use App\Services\KategoriSampahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KategoriSampahController extends ApiController
{
    public function __construct(private readonly KategoriSampahService $kategoriSampahService) {}

    public function index(): JsonResponse
    {
        $data = KategoriSampahResource::collection($this->kategoriSampahService->all());

        return $this->successResponse('Data kategori sampah berhasil diambil', $data);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = $this->kategoriSampahService->create($validated);

        return $this->successResponse('Kategori sampah berhasil ditambahkan', new KategoriSampahResource($kategori), 201);
    }

    public function show(int $id): JsonResponse
    {
        $kategori = $this->kategoriSampahService->findOrFail($id);

        return $this->successResponse('Detail kategori sampah berhasil diambil', new KategoriSampahResource($kategori));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = $this->kategoriSampahService->update($id, $validated);

        return $this->successResponse('Kategori sampah berhasil diupdate', new KategoriSampahResource($kategori));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->kategoriSampahService->delete($id);

        return $this->successResponse('Kategori sampah berhasil dihapus');
    }
}
