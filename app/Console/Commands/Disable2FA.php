<?php

namespace App\Console\Commands;

use App\Models\User;
use Custom\OTP\OTPConstants;
use Illuminate\Console\Command;

class Disable2FA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =
        '2fa:disable ' .
        '{--email= : The email of the user} ' .
        '{--force : run without asking for confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable user\'s two factor authentication';

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
        $this->info(
            'The two factor authentication will be disabled for ' .
                $user->email,
        );
        $this->info('This action will invalidate the previous secret key.');

        // ask for confirmation if not forced
        if (
            !$this->option('force') &&
            !$this->confirm('Do you wish to continue?')
        ) {
            return;
        }

        // Disabled 2fa for the user
        $user->{OTPConstants::OTP_ENABLED_COLUMN} = false;
        $user->{OTPConstants::OTP_SECRET_COLUMN} = null;
        $user->{OTPConstants::OTP_RECOVERY_CODES_COLUMN} = null;

        // save the user
        $user->save();

        $this->info(
            'Two-factor authentication has been disabled for ' . $user->email,
        );

        return 0;
    }
}
