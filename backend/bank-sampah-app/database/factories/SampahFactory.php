<?php

namespace Database\Factories;

use App\Models\KategoriSampah;
use App\Models\Sampah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sampah>
 */
class SampahFactory extends Factory
{
    protected $model = Sampah::class;

    public function definition(): array
    {
        return [
            'kategori_sampah_id' => KategoriSampah::factory(),
            'nama_sampah' => $this->faker->unique()->words(2, true),
            'harga_per_kg' => $this->faker->randomFloat(2, 500, 5000),
        ];
    }
}
