<?php

namespace Database\Seeders;

use App\Models\KategoriSampah;
use Illuminate\Database\Seeder;

class KategoriSampahSeeder extends Seeder
{
    /**
     * Seed the kategori sampah master data.
     */
    public function run(): void
    {
        $categories = [
            'Plastik',
            'Kertas',
            'Logam',
            'Kaca',
            'Organik',
            'Elektronik',
            'Tekstil',
        ];

        foreach ($categories as $category) {
            KategoriSampah::query()->firstOrCreate([
                'nama_kategori' => $category,
            ]);
        }
    }
}
