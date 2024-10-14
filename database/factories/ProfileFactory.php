<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama'      => fake()->name(),
            'tgl_lahir' => fake()->date(),
            'kelamin'   => fake()->randomElement(['L', 'P']),
            'pekerjaan' => fake()->randomElement(['Frontend Dev', 'Backend Dev', 'OB', 'Pelajar', 'Gojek', 'Admin', 'President', 'Ibu rumah tangga', 'Pegawai Sipil']),
            'alamat'    => fake()->randomElement(['Jl. Pendidikan', 'Jl. Andi Djemma', 'Jl. Antang', 'Jl. Katangka']) . fake()->randomElement(['B5 No.8', 'no 9', 'Blok Y1 no.43', 'blog 09']),
            'kelurahan' => fake()->randomElement(['Bringkanaya', 'Mamajang', 'Gunung Sari', 'Rappocini', 'Makassar', 'Gowa', 'Bantaeng', 'Malino', 'Papua Jaya', 'Ngaglik', 'Wonosari', 'Solo']),
            'kecamatan' => fake()->randomElement(['Bringkanaya', 'Mamajang', 'Gunung Sari', 'Rappocini', 'Makassar', 'Gowa', 'Bantaeng', 'Malino', 'Papua Jaya', 'Ngaglik', 'Wonosari', 'Solo']),
            'kabupaten' => fake()->randomElement(['Bringkanaya', 'Mamajang', 'Gunung Sari', 'Rappocini', 'Makassar', 'Gowa', 'Bantaeng', 'Malino', 'Papua Jaya', 'Ngaglik', 'Wonosari', 'Solo']),
            'provinsi'  => fake()->randomElement(['Bringkanaya', 'Mamajang', 'Gunung Sari', 'Rappocini', 'Makassar', 'Gowa', 'Bantaeng', 'Malino', 'Papua Jaya', 'Ngaglik', 'Wonosari', 'Solo']),
            'no_hp'     => fake()->numerify('08##########'),
            'no_wa'     => fake()->numerify('08##########'),
            'poto'      => '/storage/gambar/poto/' . fake()->lexify() . '.jpg',
            'poto_ktp'  => '/storage/gambar/poto-ktp/' . fake()->lexify() . '.jpg',
            'group'     => fake()->randomElement(['admin', 'distributor', 'kolektor', 'donatur']),
        ];
    }
}
