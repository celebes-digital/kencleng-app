<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class KenclengFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_kencleng'   => fake()->randomNumber(8),
            'qr_image'      => '/storage/gambar/kencleng/' . fake()->randomNumber(2) . '.png',
        ];
    }
}
