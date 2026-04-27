<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Resources\V1\UserResource;
use App\Notifications\ResetPasswordNotification;

class AuthController extends ApiController
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:255',
        ]);


        $user = \App\Models\User::query()
            ->where('email', $validated['email'])
            ->where('status', 'Aktif')
            ->first();

        if (! $user) {
            return $this->errorResponse('Akun tidak ditemukan atau tidak aktif', null, 422);
        }

        if (! Hash::check($validated['password'], $user->password)) {
            return $this->errorResponse('Email atau password tidak valid', null, 422);
        }

        $token = $user->createToken($validated['device_name'] ?? 'spa-client')->plainTextToken;

        return $this->successResponse('Login berhasil', [
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = \App\Models\User::query()->where('email', $validated['email'])->first();

        if (! $user) {
            return $this->errorResponse('Email tidak ditemukan', null, 404);
        }

        // Create password reset token
        $token = Password::broker()->createToken($user);

        // Send email notification with reset link
        $user->notify(new ResetPasswordNotification($token));

        return $this->successResponse('Link reset password telah dikirim ke email Anda', [
            'email' => $user->email,
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker()->reset(
            $validated,
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Optionally delete all existing tokens to force re-login
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse('Password berhasil direset');
        }

        return $this->errorResponse(__($status), null, 422);
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse('Profil user berhasil diambil', new UserResource($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->successResponse('Logout berhasil');
    }
}
