<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\PembayaranStoreRequest;
use App\Http\Requests\Api\V1\PembayaranUpdateRequest;
use App\Http\Requests\Api\V1\PencairanSaldoStoreRequest;
use App\Http\Resources\V1\PembayaranResource;
use App\Models\Transaksi;
use App\Services\PembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Controller API untuk pengelolaan data pembayaran dan pencairan saldo.
 */
class PembayaranController extends ApiController
{
    /**
     * @param PembayaranService $pembayaranService Service transaksi pembayaran.
     */
    public function __construct(private readonly PembayaranService $pembayaranService) {}

    /**
     * Menampilkan daftar pembayaran.
     */
    public function index(): JsonResponse
    {
        $data = PembayaranResource::collection($this->pembayaranService->all());

        return $this->successResponse('Data pembayaran berhasil diambil', $data);
    }

    /**
     * Menyimpan pembayaran baru (admin/petugas).
     *
     * @param PembayaranStoreRequest $request
     */
    public function store(PembayaranStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $pembayaran = $this->pembayaranService->create($validated);

        return $this->successResponse('Data pembayaran berhasil ditambahkan', new PembayaranResource($pembayaran), 201);
    }

    /**
     * Mengajukan pencairan saldo oleh nasabah.
     *
     * @param PencairanSaldoStoreRequest $request
     */
    public function requestPencairanSaldo(PencairanSaldoStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = $request->user();
        if (! $user || $user->role !== 'nasabah') {
            return $this->errorResponse('Hanya nasabah yang dapat mengajukan pencairan saldo', null, 403);
        }

        $saldoSnapshot = null;

        try {
            $pembayaran = DB::transaction(function () use ($validated, $user, &$saldoSnapshot) {
                $transaksi = Transaksi::query()
                    ->with('nasabah')
                    ->lockForUpdate()
                    ->findOrFail((int) $validated['transaksi_id']);

                if (! $transaksi->nasabah || $transaksi->nasabah->user_id !== $user->id) {
                    return null;
                }

                if (strtolower($validated['metode']) !== 'cash' && (! $transaksi->nasabah->account_number || ! $transaksi->nasabah->account_holder_name)) {
                    throw new \RuntimeException('rekening');
                }

                $saldoSnapshot = $this->pembayaranService->calculateSaldoNasabahForUpdate($transaksi->nasabah->id);
                if ((float) $validated['jumlah'] > (float) $saldoSnapshot['saldo_tersedia']) {
                    throw new \RuntimeException('saldo', 0, null);
                }

                $payload = [
                    'transaksi_id' => (int) $validated['transaksi_id'],
                    'jumlah' => (float) $validated['jumlah'],
                    'metode' => $validated['metode'],
                    'status' => 'menunggu',
                    'tanggal' => now()->toDateString(),
                    'actor_user_id' => (int) $user->id,
                ];

                return $this->pembayaranService->create($payload);
            });
        } catch (\RuntimeException $exception) {
            if ($exception->getMessage() === 'rekening') {
                return $this->errorResponse('Lengkapi rekening/ewallet sebelum mengajukan pencairan', null, 422);
            }

            if ($exception->getMessage() === 'saldo') {
                return $this->errorResponse('Saldo tidak mencukupi untuk pengajuan ini', $saldoSnapshot, 422);
            }

            throw $exception;
        }

        if (! $pembayaran) {
            return $this->errorResponse('Transaksi tidak valid untuk nasabah ini', null, 403);
        }

        return $this->successResponse('Pengajuan pencairan saldo berhasil dibuat', new PembayaranResource($pembayaran), 201);
    }

    /**
     * Menampilkan detail pembayaran.
     *
     * @param int $id
     */
    public function show(int $id): JsonResponse
    {
        $pembayaran = $this->pembayaranService->findOrFail($id);

        return $this->successResponse('Detail pembayaran berhasil diambil', new PembayaranResource($pembayaran));
    }

    /**
     * Mengubah data pembayaran, termasuk proses verifikasi.
     *
     * @param PembayaranUpdateRequest $request
     * @param int $id
     */
    public function update(PembayaranUpdateRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $validated['actor_user_id'] = (int) $request->user()->id;

        $pembayaran = $this->pembayaranService->update($id, $validated);

        return $this->successResponse('Data pembayaran berhasil diupdate', new PembayaranResource($pembayaran));
    }

    /**
     * Menghapus pembayaran.
     *
     * @param int $id
     */
    public function destroy(int $id): JsonResponse
    {
        $this->pembayaranService->delete($id);

        return $this->successResponse('Data pembayaran berhasil dihapus');
    }
}
