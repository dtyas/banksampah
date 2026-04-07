<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_get_token(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne([
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
            'device_name' => 'phpunit',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['token', 'token_type', 'user'],
            ]);
    }

    public function test_user_can_get_profile_and_logout(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();

        $this->actingAs($user, 'sanctum');

        $meResponse = $this->getJson('/api/v1/auth/me');
        $meResponse
            ->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.id', $user->id);

        $logoutResponse = $this->postJson('/api/v1/auth/logout');
        $logoutResponse
            ->assertStatus(200)
            ->assertJsonPath('status', true);
    }
}
