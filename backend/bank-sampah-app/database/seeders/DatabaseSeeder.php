<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\AccessControlService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            KategoriSampahSeeder::class,
        ]);

        User::query()->firstOrCreate([
            'email' => 'admin@banksampah.id',
        ], [
            'nama' => 'Admin Utama',
            'role' => 'super_admin',
            'menu_access' => AccessControlService::MENU_OPTIONS,
            'operational_access' => AccessControlService::OPERATIONAL_OPTIONS,
            'password' => Hash::make('password123'),
        ]);
    }
}
