<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\FrontController;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Torann\LaravelMetaTags\Facades\MetaTag;

class ResetPasswordController extends FrontController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/account';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('guest');
    }

    // -------------------------------------------------------
    // Laravel overwrites for loading LaraClassified views
    // -------------------------------------------------------

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $token
     *
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request, $token = null)
    {
        // Meta Tags
        MetaTag::set('title', t('Reset Password'));
        MetaTag::set('description', t('Reset your Password'));

        return view('auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
    }
}
