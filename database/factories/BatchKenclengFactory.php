<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BatchKencleng>
 */
class BatchKenclengFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_batch' => $this->faker->numberBetween(1, 25),
            'jumlah' => $this->faker->numberBetween(1, 25),
        ];
    }
}
