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

namespace App\Http\Controllers\Account;

use App\Larapen\Models\User;
use App\Http\Controllers\FrontController;
use Auth;
use Illuminate\Http\Request;

class EditController extends AccountBaseController
{
	/**
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
    public function details(Request $request)
    {
        // Check if email has changed
        $email_changed = ($request->input('email') != $this->user->email);
        
        // validation
        $this->validate($request, [
            'gender' => 'required|not_in:0',
            'name' => 'required|max:100',
            'phone' => 'required|max:60',
            'email' => ($email_changed) ? 'required|email|unique:users,email' : 'required|email',
        ]);
        
        // update
        $user = User::find($this->user->id);
        $user->gender_id = $request->input('gender');
        $user->name = $request->input('name');
        $user->about = $request->input('about');
        $user->country_code = $request->input('country');
        $user->phone = $request->input('phone');
        $user->phone_hidden = $request->input('phone_hidden');
        if ($email_changed) {
            $user->email = $request->input('email');
        }
        $user->receive_newsletter = $request->input('receive_newsletter');
        $user->receive_advice = $request->input('receive_advice');
        $user->save();
        
        flash()->success(t("Your details account has update successfully."));
        
        return redirect($this->lang->get('abbr') . '/account');
    }

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
    public function settings(Request $request)
    {
        // validation
        $this->validate($request, [
            'password' => 'between:5,15|confirmed',
        ]);
        
        // update
        $user = User::find($this->user->id);
        $user->comments_enabled = (int)$request->input('comments_enabled');
        if ($request->has('password')) {
            $user->password = $request->input('password');
        }
        $user->save();
        
        flash()->success(t("Your settings account has update successfully."));
        
        return redirect($this->lang->get('abbr') . '/account');
    }

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function preferences()
    {
        $data = [];
        
        return view('account.home', $data);
    }
}
