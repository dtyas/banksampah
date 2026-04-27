<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\NasabahStoreRequest;
use App\Http\Requests\Api\V1\NasabahUpdateRequest;
use App\Http\Resources\V1\NasabahResource;
use App\Models\User;
use App\Models\Nasabah;
use App\Services\AccessControlService;
use App\Services\PembayaranService;
use App\Services\NasabahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NasabahController extends ApiController
{
    public function __construct(
        private readonly NasabahService $nasabahService,
        private readonly AccessControlService $accessControlService,
        private readonly PembayaranService $pembayaranService,
    ) {}

    public function index(): JsonResponse
    {
        $data = NasabahResource::collection($this->nasabahService->all());

        return $this->successResponse('Data nasabah berhasil diambil', $data);
    }

    public function store(NasabahStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $menuAccess = ['Kategori Sampah', 'Sampah', 'Pencairan Saldo'];
        $operationalAccess = ['Ajukan Pencairan Saldo'];

        $nasabah = DB::transaction(function () use ($validated, $menuAccess, $operationalAccess) {
            $generatedEmail = Str::slug($validated['nama'], '.') . '.' . now()->timestamp . '@nasabah.local';
            $user = User::query()->create([
                'nama' => $validated['nama'],
                'email' => $generatedEmail,
                'password' => bcrypt('Nasabah123!'),
                'role' => 'nasabah',
                'status' => 'Aktif',
                'menu_access' => $menuAccess,
                'operational_access' => $operationalAccess,
            ]);

            $this->accessControlService->syncUserAccess($user, $menuAccess, $operationalAccess);

            return $this->nasabahService->create([
                'user_id' => $user->id,
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
            ])->load('user');
        });

        return $this->successResponse('Nasabah berhasil ditambahkan', new NasabahResource($nasabah), 201);
    }

    public function show(int $id): JsonResponse
    {
        $nasabah = $this->nasabahService->findOrFail($id);

        return $this->successResponse('Detail nasabah berhasil diambil', new NasabahResource($nasabah));
    }

    public function update(NasabahUpdateRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $nasabah = DB::transaction(function () use ($id, $validated) {
            $nasabah = $this->nasabahService->findOrFail($id);

            if ($nasabah->user) {
                $userData = [
                    'nama' => $validated['nama'],
                    'role' => 'nasabah',
                ];

                if (! empty($validated['email'])) {
                    $userData['email'] = $validated['email'];
                }
                if (! empty($validated['password'])) {
                    $userData['password'] = $validated['password'];
                }
                if (! empty($validated['status'])) {
                    $userData['status'] = $validated['status'];
                }
                if (array_key_exists('menu_access', $validated)) {
                    $userData['menu_access'] = $this->accessControlService->normalizeMenuAccess($validated['menu_access']);
                }
                if (array_key_exists('operational_access', $validated)) {
                    $userData['operational_access'] = $this->accessControlService->normalizeOperationalAccess($validated['operational_access']);
                }

                $nasabah->user->update($userData);
                $this->accessControlService->syncUserAccess($nasabah->user->refresh());
            }

            return $this->nasabahService->update($id, [
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'payout_channel' => $validated['payout_channel'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'account_holder_name' => $validated['account_holder_name'] ?? null,
            ])->load('user');
        });

        return $this->successResponse('Data nasabah berhasil diupdate', new NasabahResource($nasabah));
    }

    public function destroy(int $id): JsonResponse
    {
        DB::transaction(function () use ($id): void {
            $nasabah = $this->nasabahService->findOrFail($id);
            $user = $nasabah->user;

            $this->nasabahService->delete($id);

            if ($user && $user->role === 'nasabah') {
                $user->syncRoles([]);
                $user->syncPermissions([]);
                $user->update(['status' => 'Inactive']);
            }
        });

        return $this->successResponse('Data nasabah berhasil dihapus');
    }

    /**
     * Ledger hanya untuk nasabah login (me)
     */
    public function ledger(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user || $user->role !== 'nasabah') {
            return $this->errorResponse('Akses ledger hanya untuk nasabah', null, 403);
        }

        $nasabah = Nasabah::query()->where('user_id', $user->id)->first();
        if (! $nasabah) {
            return $this->errorResponse('Profil nasabah tidak ditemukan', null, 404);
        }

        // 1. Ambil saldo dari logika perhitungan lama (Legacy)
        $saldoLegacy = $this->pembayaranService->calculateSaldoNasabah($nasabah->id);

        // 2. Sinkronisasi ke wallet
        $wallet = \App\Models\Wallet::updateOrCreate(
            ['nasabah_id' => $nasabah->id],
            [
                'saldo' => $saldoLegacy['saldo_tersedia'] ?? 0,
                'meta' => json_encode($saldoLegacy)
            ]
        );

        // 3. Ambil riwayat transaksi & pembayaran
        $transaksi = $nasabah->transaksi()
            ->latest('tanggal')
            ->limit(10)
            ->get(['id', 'tanggal', 'total_harga', 'total_berat']);

        $pembayaran = \App\Models\Pembayaran::query()
            ->whereHas('transaksi', fn($query) => $query->where('nasabah_id', $nasabah->id))
            ->latest('tanggal')
            ->limit(10)
            ->get(['id', 'transaksi_id', 'jumlah', 'status', 'metode', 'tanggal']);

        return $this->successResponse('Ledger nasabah berhasil diambil', [
            'nasabah' => new NasabahResource($nasabah->loadMissing('user')),
            'saldo' => $wallet->saldo,
            'transaksi_terakhir' => $transaksi,
            'pencairan_terakhir' => $pembayaran,
        ]);
    }

    /**
     * Ambil saldo nasabah (khusus admin)
     */
    public function getSaldoNasabah(Request $request, $id): JsonResponse
    {
        $nasabah = Nasabah::find($id);
        if (!$nasabah) {
            return $this->errorResponse('Profil nasabah tidak ditemukan', null, 404);
        }
        $saldoLegacy = $this->pembayaranService->calculateSaldoNasabah($nasabah->id);
        $wallet = \App\Models\Wallet::updateOrCreate(
            ['nasabah_id' => $nasabah->id],
            [
                'saldo' => $saldoLegacy['saldo_tersedia'] ?? 0,
                'meta' => json_encode($saldoLegacy)
            ]
        );
        return $this->successResponse('Saldo nasabah berhasil diambil', [
            'nasabah_id' => $nasabah->id,
            'saldo' => $wallet->saldo,
        ]);
    }

    public function updatePayoutAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user || $user->role !== 'nasabah') {
            return $this->errorResponse('Akses hanya untuk nasabah', null, 403);
        }

        $validated = $request->validate([
            'payout_channel' => 'required|string|max:50',
            'account_number' => 'required|string|max:100',
            'account_holder_name' => 'required|string|max:100',
        ]);

        $nasabah = Nasabah::query()->where('user_id', $user->id)->first();
        if (! $nasabah) {
            return $this->errorResponse('Profil nasabah tidak ditemukan', null, 404);
        }

        $nasabah->update($validated);

        return $this->successResponse('Rekening/ewallet berhasil diperbarui', new NasabahResource($nasabah->refresh()));
    }

    public function transaksiMe(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user || $user->role !== 'nasabah') {
            return $this->errorResponse('Akses hanya untuk nasabah', null, 403);
        }

        $nasabah = Nasabah::query()->where('user_id', $user->id)->first();
        if (! $nasabah) {
            return $this->errorResponse('Profil nasabah tidak ditemukan', null, 404);
        }

        $transaksi = $nasabah->transaksi()
            ->latest('tanggal')
            ->get(['id', 'tanggal', 'total_harga', 'total_berat']);

        return $this->successResponse('Transaksi nasabah berhasil diambil', $transaksi);
    }
}
