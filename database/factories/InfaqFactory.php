<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Infaq>
 */
class InfaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tgl_transaksi' => fake()->date(),
            'jumlah'        => fake()->numberBetween(1, 20),
            'uraian'        => fake()->randomElement(['Pemasukan dana kencleng Nomor']),
            'sumber_dana'   => fake()->randomElement(['Kencleng']),
        ];
    }
}
