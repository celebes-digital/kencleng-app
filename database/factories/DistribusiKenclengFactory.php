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
        $tglDistribusi = $this->faker->optional()->dateTimeBetween('-2 months', 'now');
        $tglPengambilan = $tglDistribusi ? $this->faker->optional()->dateTimeBetween($tglDistribusi, 'now') : null;

        return [
            'kencleng_id'       => Kencleng::factory(),
            'donatur_id'        => Profile::factory(),
            'kolektor_id'       => Profile::factory(),
            'distributor_id'    => Profile::factory(),
            'geo_lat'           => $this->faker->latitude,
            'geo_long'          => $this->faker->longitude,
            'tgl_distribusi'    => $tglDistribusi ? $tglDistribusi->format('Y-m-d') : null,
            'tgl_pengambilan'   => $tglPengambilan ? $tglPengambilan->format('Y-m-d') : null,
            'jumlah'            => $this->faker->numberBetween(1000, 10000000),
        ];
    }
}
