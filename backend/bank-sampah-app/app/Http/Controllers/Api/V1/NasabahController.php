<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\NasabahResource;
use App\Services\NasabahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NasabahController extends ApiController
{
    public function __construct(private readonly NasabahService $nasabahService) {}

    public function index(): JsonResponse
    {
        $data = NasabahResource::collection($this->nasabahService->all());

        return $this->successResponse('Data nasabah berhasil diambil', $data);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $nasabah = $this->nasabahService->create($validated);

        return $this->successResponse('Nasabah berhasil ditambahkan', new NasabahResource($nasabah), 201);
    }

    public function show(int $id): JsonResponse
    {
        $nasabah = $this->nasabahService->findOrFail($id);

        return $this->successResponse('Detail nasabah berhasil diambil', new NasabahResource($nasabah));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $nasabah = $this->nasabahService->update($id, $validated);

        return $this->successResponse('Data nasabah berhasil diupdate', new NasabahResource($nasabah));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->nasabahService->delete($id);

        return $this->successResponse('Data nasabah berhasil dihapus');
    }
}
