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

use Auth;
use Creativeorange\Gravatar\Facades\Gravatar;
use App\Larapen\Models\Ad;
use App\Larapen\Models\Gender;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Torann\LaravelMetaTags\Facades\MetaTag;
use App\Larapen\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Larapen\Helpers\Localization\Country as CountryLocalization;

class HomeController extends AccountBaseController
{
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function index()
    {
        $data = [];
        
        $data['countries'] = CountryLocalizationHelper::transAll(CountryLocalization::getCountries(), $this->lang->get('abbr'));
        $data['genders'] = Gender::where('translation_lang', $this->lang->get('abbr'))->get();
        $data['gravatar'] = Gravatar::fallback(url('images/user.jpg'))->get($this->user->email);
        $data['ad_counter'] = DB::table('ads')->select('user_id', DB::raw('SUM(visits) as total_visits'))->where('user_id',
            $this->user->id)->groupBy('user_id')->first();
        
        // Meta Tags
        MetaTag::set('title', t('My account'));
        MetaTag::set('description', t('My account on :app_name', ['app_name' => config('settings.app_name')]));
        
        return view('account.home', $data);
    }
}
