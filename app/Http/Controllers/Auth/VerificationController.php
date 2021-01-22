<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Access\AuthorizationException;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Constructor de la nueva instancia del controlador
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api');
        $this->middleware('signed')->only('verify');
        $this->middleware('to.lower')->only(['resend']);
    }

    /* Sobre escribir logica original del trait */

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('auth.verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'signature' => 'required',
            'expires' => 'required',
            'hash' => 'required'
        ]);

        $user = User::findOrFail($request->id);

        if (
            !hash_equals(
                (string) $request->hash,
                sha1($user->getEmailForVerification())
            )
        ) {
            throw new AuthorizationException(__("The URL is not valid."));
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                "message" => __("The mail has already been verified.")
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(["message" => __("Mail verified correctly.")]);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                "message" => __("The mail has already been verified.")
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            "message" => __("An email has been sent to :mail.", [
                'mail' => $request->email
            ])
        ]);
    }

    public function redirectTo()
    {
        return redirect(RouteServiceProvider::HOME);
    }
}
