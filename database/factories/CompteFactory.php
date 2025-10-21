<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compte>
 */
class CompteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'numero_compte' => 'ACC'.fake()->unique()->numberBetween(100000, 999999),
            'titulaire_compte' => fake()->name(),
            'type_compte' => fake()->randomElement(['Epargne', 'Cheque']),
            'solde' => fake()->randomFloat(2, 0, 10000),
            'date_creation' => now()->toDateString(),
            'statut_compte' => 'Actif',
            // client_id will be set when creating via relationship
        ];
    }
}
