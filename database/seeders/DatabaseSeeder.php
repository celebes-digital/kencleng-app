<?php

namespace Database\Seeders;

use App\Models\DistribusiKencleng;
use App\Models\Kencleng;
use App\Models\Profile;
use App\Models\User;
use Database\Factories\InfaqFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed the users
        // \App\Models\User::factory(100)->create();

        // // Seed the profiles
        // \App\Models\Profile::factory(200)->create();

        // // Seed the kenclengs
        // \App\Models\Kencleng::factory(10)->create();

        // // Seed the distribusi kenclengs
        // \App\Models\DistribusiKencleng::factory(1000)->create();

        // Seed the infaqs
        // \App\Models\Infaq::factory(10)->create();

        $this->call(ProvinceSeeder::class);
        $this->call(DistrictSeeder::class);
        $this->call(SubDistrictSeeder::class);
        $this->call(PostalCodeSeeder::class);
    }
}
