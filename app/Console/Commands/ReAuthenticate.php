<?php

namespace App\Console\Commands;

use Arr;
use App\Models\User;
use Custom\OTP\OTPConstants;
use Illuminate\Console\Command;
use PragmaRX\Recovery\Recovery;

class ReAuthenticate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =
        '2fa:reauthenticate ' .
        '{--email= : The email of the user to reauthenticate} ' .
        '{--force : run without asking for confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate the secret key for a user\'s two factor authentication';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // retrieve the email from the option
        $email = $this->option('email');

        // if no email was passed to the option, prompt the user to enter the email
        if (!$email) {
            $email = $this->ask('what is the user\'s email?');
        }

        // retrieve the user with the specified email
        $user = User::where('email', $email)->first();

        if (!$user) {
            // show an error and exist if the user does not exist
            $this->error('No user with that email.');
            return;
        }

        // Print a warning
        $this->info('A new secret will be generated for ' . $user->email);
        $this->info('This action will invalidate the previous secret key.');

        // ask for confirmation if not forced
        if (
            !$this->option('force') &&
            !$this->confirm('Do you wish to continue?')
        ) {
            return;
        }

        // initialise the 2FA class
        $google2fa = app('pragmarx.google2fa');

        $recovery = new Recovery();

        // generate a new secret key for the user
        $user->{OTPConstants::OTP_SECRET_COLUMN} = $google2fa->generateSecretKey();
        $user->{OTPConstants::OTP_RECOVERY_CODES_COLUMN} = $recovery->toArray();

        // save the user
        $user->save();

        // show the new secret key
        $this->info('A new secret has been generated for ' . $user->email);

        $this->table(
            ['New User OTP secret'],
            [['secret' => $user->{OTPConstants::OTP_SECRET_COLUMN}]],
        );

        $table_codes = [];

        foreach (
            $user->{OTPConstants::OTP_RECOVERY_CODES_COLUMN}
            as $key => $code
        ) {
            $table_codes = Arr::add($table_codes, $key, ['code' => $code]);
        }

        $this->table(['New Recovery codes'], $table_codes);

        return 0;
    }
}
