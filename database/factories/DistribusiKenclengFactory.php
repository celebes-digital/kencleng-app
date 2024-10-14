<?php

namespace Database\Factories;

use App\Models\Kencleng;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DistribusiKencleng>
 */
class DistribusiKenclengFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'no_kencleng'       => fake()->randomNumber(8),
            'kencleng_id'       => fake()->numberBetween(1, Kencleng::count()),
            'donatur_id'        => fake()->numberBetween(1, Profile::count()),
            'distributor_id'    => fake()->numberBetween(1, Profile::count()),
            'geo_lat'           => mt_rand(-90000000, 90000000) / 1000000,
            'geo_long'          => mt_rand(-90000000, 90000000) / 1000000,
            'tgl_distribusi'    => fake()->date(),
            'tgl_pengambilan'   => fake()->date(),
        ];
    }
}
