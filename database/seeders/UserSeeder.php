<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Usuario admin
        User::factory()->create([
            'email' => 'chome@cpe.gov.co',
            'password' => Hash::make('880731Cr'),
        ]);

        // Usuarios de prueba
        User::factory()
            ->count(50)
            ->create();
    }
}
