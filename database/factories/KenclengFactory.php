<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kencleng>
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
            'batch_kencleng_id' => \App\Models\BatchKencleng::factory(), // Add this line to ensure batch_kencleng_id is set
        ];
    }

    /**
     * Indicate that the model's batch_kencleng_id should be assigned.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withBatchKenclengId(int $batchKenclengId): self
    {
        return $this->state(function (array $attributes) use ($batchKenclengId) {
            return [
                'batch_kencleng_id' => $batchKenclengId,
            ];
        });
    }
}
