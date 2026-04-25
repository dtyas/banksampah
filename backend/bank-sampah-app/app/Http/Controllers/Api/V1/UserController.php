<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\UserStoreRequest;
use App\Http\Requests\Api\V1\UserUpdateRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\Nasabah;
use App\Models\User;
use App\Services\AccessControlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends ApiController
{
    public function __construct(private readonly AccessControlService $accessControlService) {}

    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->filled('role'), fn($query) => $query->where('role', $request->string('role')))
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $keyword = $request->string('q');
                $query->where(function ($subQuery) use ($keyword): void {
                    $subQuery->where('nama', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->get();

        return $this->successResponse('Data user berhasil diambil', UserResource::collection($users));
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Ambil akses dari request atau default kosong
        $menuAccess = $this->accessControlService->normalizeMenuAccess($validated['menu_access'] ?? []);
        $operationalAccess = $this->accessControlService->normalizeOperationalAccess($validated['operational_access'] ?? []);

        // Otomatisasi hanya jika data tidak dikirim secara manual atau role adalah nasabah
        if ($validated['role'] === 'nasabah') {
            $menuAccess = ['Kategori Sampah', 'Sampah', 'Pencairan Saldo'];
            $operationalAccess = ['Ajukan Pencairan Saldo'];
        }
        // Untuk super_admin/petugas, jika di request kosong, baru kita beri full akses
        elseif (empty($validated['menu_access'])) {
            $menuAccess = $this->accessControlService::MENU_OPTIONS;
            $operationalAccess = $this->accessControlService::OPERATIONAL_OPTIONS;
        }

        $user = DB::transaction(function () use ($validated, $menuAccess, $operationalAccess): User {
            $user = User::query()->create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'status' => $validated['status'] ?? 'Aktif',
                'menu_access' => $menuAccess,
                'operational_access' => $operationalAccess,
            ]);

            $this->accessControlService->syncUserAccess($user, $menuAccess, $operationalAccess);
            $this->syncNasabahProfile($user);

            return $user;
        });

        return $this->successResponse('User berhasil ditambahkan', new UserResource($user), 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        return $this->successResponse('Detail user berhasil diambil', new UserResource($user));
    }

    public function update(UserUpdateRequest $request, int $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        $validated = $request->validated();

        // 1. Tangani Password (hanya update jika diisi)
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // 2. Normalisasi Akses (Prioritaskan apa yang dikirim dari Frontend)
        if (array_key_exists('menu_access', $validated)) {
            $validated['menu_access'] = $this->accessControlService->normalizeMenuAccess($validated['menu_access'] ?? []);
        }

        if (array_key_exists('operational_access', $validated)) {
            $validated['operational_access'] = $this->accessControlService->normalizeOperationalAccess($validated['operational_access'] ?? []);
        }

        // 3. Logika Override: Jangan timpa jika admin mengirimkan data akses spesifik
        // Kita hanya jalankan otomasi role jika input menu_access benar-benar tidak ada di request
        $role = $validated['role'] ?? $user->role;

        if (!array_key_exists('menu_access', $validated)) {
            if ($role === 'nasabah') {
                $validated['menu_access'] = ['Kategori Sampah', 'Sampah', 'Pencairan Saldo'];
            } elseif ($role === 'super_admin' || $role === 'petugas') {
                $validated['menu_access'] = $this->accessControlService::MENU_OPTIONS;
            }
        }

        if (!array_key_exists('operational_access', $validated)) {
            if ($role === 'nasabah') {
                $validated['operational_access'] = ['Ajukan Pencairan Saldo'];
            } elseif ($role === 'super_admin' || $role === 'petugas') {
                $validated['operational_access'] = $this->accessControlService::OPERATIONAL_OPTIONS;
            }
        }

        DB::transaction(function () use ($user, $validated): void {
            // Gunakan update() untuk memastikan data masuk ke DB
            $user->update($validated);

            // Kirim parameter ke syncUserAccess agar sinkron dengan data terbaru
            $this->accessControlService->syncUserAccess(
                $user,
                $validated['menu_access'] ?? $user->menu_access,
                $validated['operational_access'] ?? $user->operational_access
            );

            $this->syncNasabahProfile($user);
        });

        return $this->successResponse('User berhasil diupdate', new UserResource($user->refresh()));
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);

        DB::transaction(function () use ($user): void {
            if ($user->role === 'nasabah') {
                Nasabah::query()->where('user_id', $user->id)->delete();
                $user->syncRoles([]);
                $user->syncPermissions([]);
                $user->update(['status' => 'Inactive']);
                return;
            }

            $user->syncRoles([]);
            $user->syncPermissions([]);
            $user->delete();
        });

        return $this->successResponse('User berhasil dihapus');
    }

    private function syncNasabahProfile(User $user): void
    {
        if ($user->role !== 'nasabah') {
            Nasabah::query()->where('user_id', $user->id)->delete();
            return;
        }

        $nasabah = Nasabah::query()->withTrashed()->firstOrNew([
            'user_id' => $user->id,
        ]);

        $nasabah->nama = $user->nama;
        $nasabah->save();

        if ($nasabah->trashed()) {
            $nasabah->restore();
        }
    }
}
