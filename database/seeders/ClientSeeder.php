<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        Client::factory()->count(10)->create()->each(function ($client) {
            $client->comptes()->createMany(\Database\Factories\CompteFactory::new()->count(2)->make()->toArray());
        });
    }
}
