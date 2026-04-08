<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\PembayaranStoreRequest;
use App\Http\Requests\Api\V1\PembayaranUpdateRequest;
use App\Http\Resources\V1\PembayaranResource;
use App\Services\PembayaranService;
use Illuminate\Http\JsonResponse;

class PembayaranController extends ApiController
{
    public function __construct(private readonly PembayaranService $pembayaranService) {}

    public function index(): JsonResponse
    {
        $data = PembayaranResource::collection($this->pembayaranService->all());

        return $this->successResponse('Data pembayaran berhasil diambil', $data);
    }

    public function store(PembayaranStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $pembayaran = $this->pembayaranService->create($validated);

        return $this->successResponse('Data pembayaran berhasil ditambahkan', new PembayaranResource($pembayaran), 201);
    }

    public function show(int $id): JsonResponse
    {
        $pembayaran = $this->pembayaranService->findOrFail($id);

        return $this->successResponse('Detail pembayaran berhasil diambil', new PembayaranResource($pembayaran));
    }

    public function update(PembayaranUpdateRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $pembayaran = $this->pembayaranService->update($id, $validated);

        return $this->successResponse('Data pembayaran berhasil diupdate', new PembayaranResource($pembayaran));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->pembayaranService->delete($id);

        return $this->successResponse('Data pembayaran berhasil dihapus');
    }
}
