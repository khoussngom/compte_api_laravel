<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ClientSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ClientSpecificSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ClientSeeder::class,
            ClientSpecificSeeder::class,
            // CompteSeeder::class, // comptes créés via ClientSeeder
        ]);
    }
}
