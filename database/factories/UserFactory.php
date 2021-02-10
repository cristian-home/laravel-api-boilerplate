<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Custom\OTP\OTPConstants;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make($this->faker->password),
            OTPConstants::OTP_ENABLED_COLUMN => false,
            OTPConstants::OTP_SECRET_COLUMN => null,
            'remember_token' => Str::random(10),
        ];
    }
}
