<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\SampahResource;
use App\Services\SampahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SampahController extends ApiController
{
    public function __construct(private readonly SampahService $sampahService) {}

    public function index(): JsonResponse
    {
        $data = SampahResource::collection($this->sampahService->all());

        return $this->successResponse('Data sampah berhasil diambil', $data);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kategori_sampah_id' => 'required|exists:kategori_sampah,id',
            'nama_sampah' => 'required|string|max:255',
            'harga_per_kg' => 'required|numeric|min:0',
        ]);

        $sampah = $this->sampahService->create($validated);

        return $this->successResponse('Data sampah berhasil ditambahkan', new SampahResource($sampah), 201);
    }

    public function show(int $id): JsonResponse
    {
        $sampah = $this->sampahService->findOrFail($id);

        return $this->successResponse('Detail sampah berhasil diambil', new SampahResource($sampah));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'kategori_sampah_id' => 'required|exists:kategori_sampah,id',
            'nama_sampah' => 'required|string|max:255',
            'harga_per_kg' => 'required|numeric|min:0',
        ]);

        $sampah = $this->sampahService->update($id, $validated);

        return $this->successResponse('Data sampah berhasil diupdate', new SampahResource($sampah));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->sampahService->delete($id);

        return $this->successResponse('Data sampah berhasil dihapus');
    }
}
