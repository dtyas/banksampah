<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\NasabahResource;
use App\Models\User;
use App\Services\NasabahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:8',
            'status' => 'nullable|in:Aktif,Inactive',
            'menu_access' => 'nullable|array',
            'menu_access.*' => 'string|max:255',
            'operational_access' => 'nullable|array',
            'operational_access.*' => 'string|max:255',
        ]);

        $nasabah = DB::transaction(function () use ($validated) {
            $generatedEmail = Str::slug($validated['nama'], '.') . '.' . now()->timestamp . '@nasabah.local';
            $user = User::query()->create([
                'nama' => $validated['nama'],
                'email' => $validated['email'] ?? $generatedEmail,
                'password' => $validated['password'] ?? 'Nasabah123!',
                'role' => 'nasabah',
                'status' => $validated['status'] ?? 'Aktif',
                'menu_access' => $validated['menu_access'] ?? ['Pencairan Saldo'],
                'operational_access' => $validated['operational_access'] ?? ['Ajukan Pencairan Saldo'],
            ]);

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

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->nasabahService->findOrFail($id)->user_id),
            ],
            'password' => 'nullable|string|min:8',
            'status' => 'nullable|in:Aktif,Inactive',
            'menu_access' => 'nullable|array',
            'menu_access.*' => 'string|max:255',
            'operational_access' => 'nullable|array',
            'operational_access.*' => 'string|max:255',
        ]);

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
                    $userData['menu_access'] = $validated['menu_access'];
                }
                if (array_key_exists('operational_access', $validated)) {
                    $userData['operational_access'] = $validated['operational_access'];
                }

                $nasabah->user->update($userData);
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
                $user->delete();
            }
        });

        return $this->successResponse('Data nasabah berhasil dihapus');
    }
}
