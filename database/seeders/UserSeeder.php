<?php

namespace Database\Seeders;

use Google2FA;
use App\Models\User;
use Custom\OTP\OTPConstants;
use Illuminate\Database\Seeder;
use PragmaRX\Recovery\Recovery;
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
            // OTPConstants::OTP_ENABLED_COLUMN => true,
            // OTPConstants::OTP_SECRET_COLUMN => Google2FA::generateSecretKey(),
            // OTPConstants::OTP_RECOVERY_CODES_COLUMN => (new Recovery())->toArray(),
        ]);

        // Usuarios de prueba
        User::factory()
            ->count(50)
            ->create();
    }
}
