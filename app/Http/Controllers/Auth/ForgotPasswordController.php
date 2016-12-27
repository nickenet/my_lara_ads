<?php
/**
 * LaraClassified - Geo Classified Ads CMS
 * Copyright (c) Mayeul Akpovi. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Http\Controllers\FrontController;
use Torann\LaravelMetaTags\Facades\MetaTag;

class ForgotPasswordController extends FrontController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected $redirectTo = '/account';

    /**
     * PasswordController constructor.
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
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        // Meta Tags
        MetaTag::set('title', t('Lost your password?'));
        MetaTag::set('description', t('Lost your password?'));

        return view('auth.passwords.email');
    }
}
