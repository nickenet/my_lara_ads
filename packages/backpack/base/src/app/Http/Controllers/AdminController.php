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

namespace Larapen\Base\app\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;
use App\Larapen\Models\Ad;
use App\Larapen\Models\Country;
use App\Larapen\Models\User;

class AdminController extends Controller
{
	protected $data = []; // the information we send to the view

	/**
	 * Create a new controller instance.
	 */
	public function __construct()
	{
		$this->middleware('admin');
		parent::__construct();

		// Get site mini stats
		if (config('settings.ads_review_activation')) {
			$countUnactivatedAds = Ad::where('active', 0)->orWhere('reviewed', 0)->count();
			$countActivatedAds = Ad::where('active', 1)->where('reviewed', 1)->count();
		} else {
			$countUnactivatedAds = Ad::where('active', 0)->count();
			$countActivatedAds = Ad::where('active', 1)->count();
		}
		$countUnactivatedUsers = User::where('is_admin', 0)->where('active', 0)->count();
		$countActivatedUsers = User::where('is_admin', 0)->where('active', 1)->count();
		$countUsers = User::where('is_admin', 0)->count();
		$countCountries = Country::where('active', 1)->count();

		view()->share('countUnactivatedAds', $countUnactivatedAds);
		view()->share('countActivatedAds', $countActivatedAds);
		view()->share('countUnactivatedUsers', $countUnactivatedUsers);
		view()->share('countActivatedUsers', $countActivatedUsers);
		view()->share('countUsers', $countUsers);
		view()->share('countCountries', $countCountries);
	}

	/**
	 * Show the admin dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function dashboard()
	{
		$this->data['title'] = trans('backpack::base.dashboard'); // set the page title

		return view('backpack::dashboard', $this->data);
	}

	/**
	 * Redirect to the dashboard.
	 *
	 * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
	 */
	public function redirect()
	{
		// The '/admin' route is not to be used as a page, because it breaks the menu's active state.
		return redirect(config('backpack.base.route_prefix', 'admin') . '/dashboard');
	}
}
