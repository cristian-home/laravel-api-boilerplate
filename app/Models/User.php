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

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property bool $otp_enabled
 * @property string $otp_secret
 * @property string $recovery_codes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property string $temp_secret
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOtpEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOtpSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
