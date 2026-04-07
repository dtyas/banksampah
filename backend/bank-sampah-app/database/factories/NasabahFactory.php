<?php

namespace Database\Factories;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Nasabah>
 */
class NasabahFactory extends Factory
{
    protected $model = Nasabah::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama' => $this->faker->name(),
            'alamat' => $this->faker->address(),
            'no_hp' => $this->faker->phoneNumber(),
        ];
    }
}
