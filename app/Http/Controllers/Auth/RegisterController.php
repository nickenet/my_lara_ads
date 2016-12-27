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

use App\Mail\UserRegistered;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Larapen\Events\UserWasRegistered;
use App\Larapen\Helpers\Ip;
use App\Larapen\Helpers\Rules;
use App\Larapen\Scopes\ActiveScope;
use App\Larapen\Scopes\ReviewedScope;
use Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Larapen\Models\Ad;
use App\Larapen\Models\Gender;
use App\Larapen\Models\UserType;
use App\Larapen\Models\User;
use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\Request;
use Torann\LaravelMetaTags\Facades\MetaTag;
use App\Larapen\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Larapen\Helpers\Localization\Country as CountryLocalization;

class RegisterController extends FrontController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/account';

    /**
     * @var array
     */
    public $msg = [];

    /**
     * SignupController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->redirectTo = $this->lang->get('abbr') . '/account';
        
        /*
         * Messages
         */
        $this->msg['signup']['success'] = "Your account has been created.";
        $this->msg['activation']['success'] = "Congratulation :first_name ! Your account has been activated.";
        $this->msg['activation']['multiple'] = "Your account is already activated.";
        $this->msg['activation']['error'] = "Your account's activation has failed.";
    }
    
    /**
     * Show the form the create a new user account.
     *
	 * @return View
	 */
    public function showRegistrationForm()
    {
        $data = [];
        
        // References
        $data['countries'] = CountryLocalizationHelper::transAll(CountryLocalization::getCountries(), $this->lang->get('abbr'));
        $data['genders'] = Gender::where('translation_lang', $this->lang->get('abbr'))->get();
        $data['userTypes'] = UserType::all();
        
        // Meta Tags
        MetaTag::set('title', t('Sign Up'));
        MetaTag::set('description', t('Sign Up on :app_name !', ['app_name' => mb_ucfirst(config('settings.app_name'))]));
        
        return view('auth.signup.index', $data);
    }
    
    /**
     * Store a new ad post.
     *
	 * @param Request $request
	 * @return $this|\Illuminate\Http\RedirectResponse
	 */
    public function register(Request $request)
    {
        // Form validation
        $validator = Validator::make($request->all(), Rules::Signup($request));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        
        // Store User
        $userInfo = array(
            'country_code'     => $this->country->get('code'),
            'gender_id'        => $request->input('gender'),
            'name'             => $request->input('name'),
            'user_type_id'     => $request->input('user_type'),
            'phone'            => $request->input('phone'),
            'email'            => $request->input('email'),
            'password'         => bcrypt($request->input('password')),
            'phone_hidden'     => $request->input('phone_hidden'),
            'ip_addr'          => Ip::get(),
            'activation_token' => md5(uniqid()),
            'active'           => (config('settings.require_users_activation') == 1) ? 0 : 1,
        );
        $user = new User($userInfo);
        $user->save();
        
        // Update Ads created by this email
        if (isset($user->id) and $user->id > 0) {
            Ad::withoutGlobalScopes([ActiveScope::class, ReviewedScope::class])->where('seller_email', $request->input('email'))->update(['user_id' => $user->id]);
        }
        
        // Send Welcome Email
        if (config('settings.require_users_activation') == 1) {
            try {
                Mail::send(new UserRegistered($user));
            } catch (\Exception $e) {
                flash()->error($e->getMessage());
            }
        }
        
        return redirect($this->lang->get('abbr') . '/signup/success')->with(['success' => 1, 'message' => t($this->msg['signup']['success'])]);
    }

	/**
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
	 */
    public function success()
    {
        if (!session('success')) {
            return redirect($this->lang->get('abbr') . '/');
        }
        
        // Meta Tags
        MetaTag::set('title', session('message'));
        MetaTag::set('description', session('message'));
        
        return view('auth.signup.success');
    }

	/**
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
	 */
    public function activation($token)
    {
        $user = User::withoutGlobalScope(ActiveScope::class)->where('activation_token', $token)->first();
        
        if ($user) {
            if ($user->active != 1) {
                // Activate
                $user->active = 1;
                $user->save();
                flash()->success(t($this->msg['activation']['success'], ['first_name' => $user->name]));
            } else {
                flash()->error(t($this->msg['activation']['multiple']));
            }
            // Connect the User
            if (Auth::loginUsingId($user->id)) {
                return redirect($this->lang->get('abbr') . '/account');
            } else {
                return redirect($this->lang->get('abbr') . '/' . trans('routes.login'));
            }
        } else {
            $data = ['error' => 1, 'message' => t($this->msg['activation']['error'])];
        }
        
        // Meta Tags
        MetaTag::set('title', $data['message']);
        MetaTag::set('description', $data['message']);
        
        return view('auth.signup.activation', $data);
    }
}
