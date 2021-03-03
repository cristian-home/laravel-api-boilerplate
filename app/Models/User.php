<?php

namespace App\Models;

use Cache;
use Custom\OTP\OTPConstants;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'password'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        OTPConstants::OTP_ENABLED_COLUMN,
        OTPConstants::OTP_SECRET_COLUMN,
        OTPConstants::OTP_RECOVERY_CODES_COLUMN,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        OTPConstants::OTP_ENABLED_COLUMN => 'boolean',
        // OTPConstants::OTP_RECOVERY_CODES_COLUMN => 'array',
    ];

    /**
     * Ecrypt the user's google_2fa secret.
     *
     * @param  string  $value
     * @return string
     */
    public function setOtpSecretAttribute($value)
    {
        $this->attributes[OTPConstants::OTP_SECRET_COLUMN] = is_null($value)
            ? $value
            : encrypt($value);
    }

    /**
     * Decrypt the user's google_2fa secret.
     *
     * @param  string  $value
     * @return string
     */
    public function getOtpSecretAttribute($value)
    {
        return is_null($value) ? $value : decrypt($value);
    }

    /**
     * Set and encrypt the user's temporal secret.
     *
     * @param  string  $value
     * @return void
     */
    public function setTempSecretAttribute($value)
    {
        // Cifrar el valor y guardarlo en caché
        if (!is_null($value)) {
            Cache::put(
                "user{$this->id}_temp_secret",
                encrypt($value),
                now()->addMinutes(10),
            );
        }
    }

    /**
     * Decrypt and get the user's temporal secret.
     *
     * @return string
     */
    public function getTempSecretAttribute()
    {
        // Descifrar el item en caché y devolverlo
        $val = Cache::get("user{$this->id}_temp_secret");
        return is_null($val) ? $val : decrypt($val);
    }

    /**
     * Ecrypt the user's recovery codes.
     *
     * @param  string  $value
     * @return string
     */
    public function setRecoveryCodesAttribute($value)
    {
        $this->attributes[OTPConstants::OTP_RECOVERY_CODES_COLUMN] = is_null(
            $value,
        )
            ? $value
            : encrypt($value);
    }

    /**
     * Decrypt the user's recovery codes.
     *
     * @param  string  $value
     * @return string
     */
    public function getRecoveryCodesAttribute($value)
    {
        return is_null($value) ? $value : decrypt($value);
    }

    /**
     * Check if the user's temporal secret still valid.
     *
     * @return bool
     */
    public function tempSecretExpired()
    {
        return is_null($this->temp_secret);
    }

    /**
     * Renew user's temporal secret expiration.
     *
     * @return void
     */
    public function renewTempSecret()
    {
        $this->temp_secret = $this->temp_secret;
    }

    /**
     * Revoke user's temporal secret.
     *
     * @return void
     */
    public function revokeTempSecret()
    {
        Cache::forget("user{$this->id}_temp_secret");
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
