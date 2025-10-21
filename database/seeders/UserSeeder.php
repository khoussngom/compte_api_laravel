<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'khoussn@gmail.com'],
            [
                'name' => 'Khouss ngom',
                'email' => 'khoussn@gmail.com',
                'password' => Hash::make('marakhib'),
            ]
        );
    }
}
