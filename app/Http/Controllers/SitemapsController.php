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

namespace App\Http\Controllers;

use App\Larapen\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Larapen\Helpers\Localization\Country as CountryLocalization;
use App\Larapen\Models\Category;
use Carbon\Carbon;
use App\Larapen\Helpers\Search;
use App\Larapen\Models\Ad;
use App\Larapen\Models\City;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request as HttpRequest;
use Watson\Sitemap\Facades\Sitemap;

class SitemapsController extends FrontController
{
    protected $defaultDate = '2015-10-30T20:10:00+02:00';

    /**
     * SitemapsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        // Get Countries
        $this->countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries(), $this->lang->get('abbr'));

		// From Laravel 5.3.4 or above
		$this->middleware(function ($request, $next) {
			$this->commonQueries();
			return $next($request);
		});
    }

	/**
	 * Common Queries
	 */
	public function commonQueries()
	{
		// Get country language
		$this->lang = $this->country->get('lang');
		App::setLocale($this->lang->get('abbr'));

		// Date : Carbon object
		$this->defaultDate = Carbon::parse(date('Y-m-d H:i'));
		if (session('time_zone')) {
			$this->defaultDate->timezone(session('time_zone'));
		}
	}
    
    /**
     * Index Sitemap
     * @return mixed
     */
    public function index()
    {
        foreach ($this->countries as $item) {
            $country = CountryLocalization::getCountryInfo($item->get('code'));
            Sitemap::addSitemap(url($country->get('lang')->get('abbr') . '/' . $country->get('icode') . '/sitemaps.xml'));
        }
        
        return Sitemap::index();
    }
    
    /**
     * Index Sitemap
     * @return mixed
     */
    public function site()
    {
        Sitemap::addSitemap(url($this->lang->get('abbr') . '/' . $this->country->get('icode') . '/sitemaps/pages.xml'));
        Sitemap::addSitemap(url($this->lang->get('abbr') . '/' . $this->country->get('icode') . '/sitemaps/categories.xml'));
        Sitemap::addSitemap(url($this->lang->get('abbr') . '/' . $this->country->get('icode') . '/sitemaps/cities.xml'));

        $count_ads = Ad::active()->where('country_code', $this->country->get('code'))->count();
        if ($count_ads > 0) {
            Sitemap::addSitemap(url($this->lang->get('abbr') . '/' . $this->country->get('icode') . '/sitemaps/ads.xml'));
        }
        
        return Sitemap::index();
    }
    
    /**
     * @return mixed
     */
    public function pages()
    {
        Sitemap::addTag(lurl('/?d=' . $this->country->get('code')), $this->defaultDate, 'daily', '1.0');
        Sitemap::addTag(lurl(trans('routes.v-sitemap') . '?d=' . $this->country->get('icode')), $this->defaultDate, 'daily', '0.5');
        Sitemap::addTag(lurl(trans('routes.v-search') . '?d=' . $this->country->get('icode')), $this->defaultDate, 'daily', '0.6');
        
        return Sitemap::render();
    }
    
    /**
     * @return mixed
     */
    public function categories()
    {
        // Categories
        $cats = Category::where('translation_lang', $this->lang->get('abbr'))->orderBy('lft')->get();
        if (!is_null($cats)) {
            $cats = collect($cats)->keyBy('translation_of');
            $cats = $sub_cats = $cats->groupBy('parent_id');
            $cats = $cats->get(0);
            $sub_cats = $sub_cats->forget(0);
            
            foreach ($cats as $cat) {
                Sitemap::addTag(lurl(trans('routes.v-search-cat', ['countryCode' => $this->country->get('icode'), 'catSlug' => $cat->slug])),
                    $this->defaultDate, 'daily', '0.8');
                if ($sub_cats->get($cat->id)) {
                    foreach ($sub_cats->get($cat->id) as $sub_cat) {
                        Sitemap::addTag(lurl(trans('routes.v-search-subCat',
                            [
                                'countryCode' => $this->country->get('icode'),
                                'catSlug'     => $cat->slug,
                                'subCatSlug'  => $sub_cat->slug
                            ])),
                            $this->defaultDate, 'weekly', '0.5');
                    }
                }
            }
        }
        
        return Sitemap::render();
    }
    
    /**
     * @return mixed
     */
    public function cities()
    {
        $taked = 100;
        $cities = City::where('country_code', $this->country->get('code'))->take($taked)->orderBy('population', 'DESC')->orderBy('name')->get();
        
        foreach ($cities as $city) {
            $city->name = trim(head(explode('/', $city->name)));
            Sitemap::addTag(lurl(trans('routes.v-search-location',
                        [
                            'countryCode' => $this->country->get('icode'),
                            'city'        => slugify($city->name),
                            'id'          => $city->id
                        ])),
                $this->defaultDate, 'weekly', '0.7');
        }
        
        return Sitemap::render();
    }
    
    /**
     * @return mixed
     */
    public function ads()
    {
        $taked = 100;
        $ads = Ad::active()->where('country_code', $this->country->get('code'))->take($taked)->orderBy('created_at', 'DESC')->get();
        
        if (!is_null($ads)) {
            foreach ($ads as $ad) {
                Sitemap::addTag(lurl(slugify($ad->title) . '/' . $ad->id . '.html'), $ad->created_at, 'daily', '0.6');
            }
        }
        
        return Sitemap::render();
    }
}
