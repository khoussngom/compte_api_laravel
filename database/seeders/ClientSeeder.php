<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['aliou', 'fallou', 'saliou', 'laye', 'tedene'];

        // mapping for nom (family name) when provided
        $nomMapping = [
            'aliou' => 'ndiaye',
            'fallou' => 'senghor',
            'laye' => 'diallo',
            'tedene' => 'faye',
            // saliou will keep default surname (capitalized)
        ];

        foreach ($names as $name) {
            $email = $name . '@gmail.com';

            $prenom = $name;
            $nom = isset($nomMapping[$name]) ? $nomMapping[$name] : ucfirst($name);

            $client = Client::updateOrCreate(
                ['email' => $email],
                [
                    'id' => (string) Str::uuid(),
                    'nom' => ucfirst($nom),
                    'prenom' => ucfirst($prenom),
                    'email' => $email,
                    'mot_de_passe' => Hash::make('marakhib'),
                    'telephone' => null,
                    'adresse' => null,
                    'role' => 'Client',
                ]
            );

            // create 2 comptes for each created client if none exist
            if ($client->comptes()->count() === 0) {
                $client->comptes()->createMany(\Database\Factories\CompteFactory::new()->count(2)->make()->toArray());
            }
        }

        // Keep the original specific client as well
        Client::updateOrCreate(
            ['email' => 'khoussn@gmail.com'],
            [
                'id' => (string) Str::uuid(),
                'nom' => 'Khoussn',
                'prenom' => 'Test',
                'email' => 'khoussn@gmail.com',
                'mot_de_passe' => Hash::make('marakhib'),
                'telephone' => '+221774730039',
                'adresse' => 'Adresse de test',
                'role' => 'Client',
            ]
        );
    }
}
