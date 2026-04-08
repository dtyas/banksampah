<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{
    public function index(): JsonResponse
    {
        $users = User::query()->latest()->get();

        return $this->successResponse('Data user berhasil diambil', UserResource::collection($users));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['super_admin', 'petugas', 'nasabah'])],
            'status' => ['nullable', Rule::in(['Aktif', 'Inactive'])],
            'menu_access' => 'nullable|array',
            'menu_access.*' => 'string|max:255',
            'operational_access' => 'nullable|array',
            'operational_access.*' => 'string|max:255',
        ]);

        $user = User::query()->create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'status' => $validated['status'] ?? 'Aktif',
            'menu_access' => $validated['menu_access'] ?? [],
            'operational_access' => $validated['operational_access'] ?? [],
        ]);

        return $this->successResponse('User berhasil ditambahkan', new UserResource($user), 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);

        return $this->successResponse('Detail user berhasil diambil', new UserResource($user));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'sometimes|required|string|min:8',
            'role' => ['sometimes', 'required', Rule::in(['super_admin', 'petugas', 'nasabah'])],
            'status' => ['sometimes', 'required', Rule::in(['Aktif', 'Inactive'])],
            'menu_access' => 'sometimes|array',
            'menu_access.*' => 'string|max:255',
            'operational_access' => 'sometimes|array',
            'operational_access.*' => 'string|max:255',
        ]);

        $user->fill($validated);
        $user->save();

        return $this->successResponse('User berhasil diupdate', new UserResource($user->refresh()));
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        $user->delete();

        return $this->successResponse('User berhasil dihapus');
    }
}
