<?php

use Custom\OTP\OTPConstants;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table
                ->string('password')
                ->default(Hash::make(env('DEFAULT_USER_PASSWORD', 'Password')));
            $table->rememberToken();
            $table->boolean(OTPConstants::OTP_ENABLED_COLUMN)->default(false);
            $table->text(OTPConstants::OTP_SECRET_COLUMN)->nullable();
            $table->text(OTPConstants::OTP_RECOVERY_CODES_COLUMN)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
