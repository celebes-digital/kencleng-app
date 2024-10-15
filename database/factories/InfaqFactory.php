<?php

namespace Database\Factories;

use App\Models\DistribusiKencleng;
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
            'jumlah_donasi' => fake()->numberBetween(1000, 100000),
            'uraian'        => fake()->randomElement(['Pemasukan dana kencleng Nomor']),
            'sumber_dana'   => fake()->randomElement(['Kencleng']),
            'distribusi_id' => fake()->numberBetween(1, DistribusiKencleng::count()),
        ];
    }
}
