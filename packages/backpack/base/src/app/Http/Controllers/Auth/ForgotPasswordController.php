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

namespace Larapen\Base\app\Http\Controllers\Auth;

use Larapen\Base\app\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    protected $data = []; // the information we send to the view
    protected $redirectTo = 'admin/dashboard';

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

	/**
	 * PasswordController constructor.
	 */
    public function __construct()
    {
        $this->middleware('guest');

		$this->redirectTo = config('backpack.base.route_prefix', 'admin') . '/dashboard';

        parent::__construct();
    }

    // -------------------------------------------------------
    // Laravel overwrites for loading backpack views
    // -------------------------------------------------------

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        $this->data['title'] = trans('backpack::base.reset_password'); // set the page title

        return view('backpack::auth.passwords.email', $this->data);
    }
}
