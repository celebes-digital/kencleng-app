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
        // User::factory(10)->create();
        // Kencleng::factory(10)->create();
        Profile::factory(10)->create();
        DistribusiKencleng::factory(10)->create();
        InfaqFactory::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
