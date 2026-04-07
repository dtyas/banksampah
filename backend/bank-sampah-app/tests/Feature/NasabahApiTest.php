<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NasabahApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_nasabah(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        Nasabah::factory()->count(2)->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/v1/nasabah');

        $response
            ->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ]);
    }

    public function test_can_create_nasabah(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $this->actingAs($user, 'sanctum');

        $payload = [
            'nama' => 'Rika Paramitha',
            'alamat' => 'Jl. Anggrek No. 10',
            'no_hp' => '081277228899',
        ];

        $response = $this->postJson('/api/v1/nasabah', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.nama', 'Rika Paramitha');

        $this->assertDatabaseHas('nasabah', [
            'nama' => 'Rika Paramitha',
        ]);
    }
}
