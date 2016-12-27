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

use App\Http\Controllers\FrontController;
use App\Larapen\Models\Ad;
use App\Larapen\Models\SavedAd;
use App\Larapen\Models\SavedSearch;
use App\Larapen\Scopes\ActiveScope;
use App\Larapen\Scopes\ReviewedScope;
use App\Larapen\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Larapen\Helpers\Localization\Country as CountryLocalization;

abstract class AccountBaseController extends FrontController
{
    public $countries;
    public $my_ads;
    public $archived_ads;
    public $favourite_ads;
    public $pending_ads;

    /**
     * AccountBaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // From Laravel 5.3.4 or above
        $this->middleware(function ($request, $next) {
            $this->leftMenuInfo();
            return $next($request);
        });
    }

    public function leftMenuInfo()
    {
        view()->share('pagePath', '');

        $this->countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries(), $this->lang->get('abbr'));
        view()->share('countries', $this->countries);

        // My Ads
        $this->my_ads = Ad::where('user_id', $this->user->id)->active()->with('city')->take(50)->orderBy('created_at', 'DESC');
        view()->share('count_my_ads', $this->my_ads->count());

        // Archived Ads
        $this->archived_ads = Ad::where('user_id', $this->user->id)->archived()->with('city')->take(50)->orderBy('created_at', 'DESC');
        view()->share('count_archived_ads', $this->archived_ads->count());

        // Favourite Ads
        $this->favourite_ads = SavedAd::where('user_id', $this->user->id)->with('ad.city')->take(50)->orderBy('created_at', 'DESC');
        view()->share('count_favourite_ads', $this->favourite_ads->count());

        // Pending Approval Ads
        $this->pending_ads = Ad::withoutGlobalScopes([ActiveScope::class, ReviewedScope::class])->where('user_id',
            $this->user->id)->pending()->with('city')->take(50)->orderBy('created_at', 'DESC');
        view()->share('count_pending_ads', $this->pending_ads->count());

        // Save Search
        $saved_search = SavedSearch::where('user_id', $this->user->id)->orderBy('created_at', 'DESC');
        view()->share('count_saved_search', $saved_search->count());
    }
}
