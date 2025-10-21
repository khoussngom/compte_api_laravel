<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Compte;
use App\Models\Client;

class CompteSeeder extends Seeder
{
    public function run(): void
    {
        // Create some generic comptes
        Compte::factory()->count(20)->create();

        // Ensure each existing client has at least 2 comptes
        $clients = Client::all();
        foreach ($clients as $client) {
            $count = $client->comptes()->count();
            if ($count < 2) {
                $toCreate = 2 - $count;
                $client->comptes()->createMany(\Database\Factories\CompteFactory::new()->count($toCreate)->make()->toArray());
            }
        }
    }
}
