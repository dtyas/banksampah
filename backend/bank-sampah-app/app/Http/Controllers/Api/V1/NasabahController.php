<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\NasabahStoreRequest;
use App\Http\Requests\Api\V1\NasabahUpdateRequest;
use App\Http\Resources\V1\NasabahResource;
use App\Models\User;
use App\Services\AccessControlService;
use App\Services\NasabahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NasabahController extends ApiController
{
    public function __construct(
        private readonly NasabahService $nasabahService,
        private readonly AccessControlService $accessControlService,
    ) {}

    public function index(): JsonResponse
    {
        $data = NasabahResource::collection($this->nasabahService->all());

        return $this->successResponse('Data nasabah berhasil diambil', $data);
    }

    public function store(NasabahStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $menuAccess = $this->accessControlService->normalizeMenuAccess($validated['menu_access'] ?? ['Pencairan Saldo']);
        $operationalAccess = $this->accessControlService->normalizeOperationalAccess($validated['operational_access'] ?? ['Ajukan Pencairan Saldo']);

        $nasabah = DB::transaction(function () use ($validated, $menuAccess, $operationalAccess) {
            $generatedEmail = Str::slug($validated['nama'], '.') . '.' . now()->timestamp . '@nasabah.local';
            $user = User::query()->create([
                'nama' => $validated['nama'],
                'email' => $validated['email'] ?? $generatedEmail,
                'password' => $validated['password'] ?? 'Nasabah123!',
                'role' => 'nasabah',
                'status' => $validated['status'] ?? 'Aktif',
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
}
