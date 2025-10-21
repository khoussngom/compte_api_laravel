<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compte>
 */
class CompteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'numero_compte' => 'ACC' . sprintf('%08d', fake()->unique()->numberBetween(0, 99999999)),
            'titulaire_compte' => fake()->name(),
            'type_compte' => fake()->randomElement(['Epargne', 'Cheque']),
            'solde' => fake()->randomFloat(2, 0, 10000),
            'devise' => 'FCFA',
            'date_creation' => now()->toDateString(),
            'statut_compte' => 'Actif',
            'motif_blocage' => null,
            'client_id' => Client::factory(),
        ];
    }
}
