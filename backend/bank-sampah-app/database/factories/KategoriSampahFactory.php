<?php

namespace Database\Factories;

use App\Models\KategoriSampah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KategoriSampah>
 */
class KategoriSampahFactory extends Factory
{
    protected $model = KategoriSampah::class;

    public function definition(): array
    {
        return [
            'nama_kategori' => $this->faker->unique()->word(),
        ];
    }
}
