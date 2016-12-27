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

use App\Larapen\Helpers\Arr;
use App\Larapen\Helpers\Search;
use App\Larapen\Models\SubAdmin1;
use App\Larapen\Models\AdType;
use App\Larapen\Models\Category;
use App\Larapen\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Larapen\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Larapen\Helpers\Localization\Country as CountryLocalization;
use Torann\LaravelMetaTags\Facades\MetaTag;

class SearchController extends FrontController
{
    public $request;
	public $countries;
	public $isIndexSearch = false;
	public $isCatSearch = false;
	public $isLocSearch = false;
	public $isUserSearch = false;

	protected $city = null;
	private $cats;

	/**
	 * SearchController constructor.
	 * @param Request $request
	 */
    public function __construct(Request $request)
    {
        parent::__construct();

        // From Laravel 5.3.4 or above
        $this->middleware(function ($request, $next) {
            $this->commonQueries();
            return $next($request);
        });

        $this->request = $request;
    }

    /**
     * Common Queries
     */
    public function commonQueries()
    {
        $countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries(), $this->lang->get('abbr'));
        $this->countries = $countries;
        view()->share('countries', $countries);

        // CATEGORIES COLLECTION
        $cats = Category::where('translation_lang', $this->lang->get('abbr'))->orderBy('lft')->get();
        if (!is_null($cats)) {
            $cats = collect($cats)->keyBy('translation_of');
        }
        view()->share('cats', $cats);
        $this->cats = $cats;


        // COUNT CATEGORIES ADS COLLECTION
        $sql = 'SELECT sc.id, c.parent_id, count(*) as total
				FROM ' . DB::getTablePrefix() . 'ads as a
				INNER JOIN ' . DB::getTablePrefix() . 'categories as sc ON sc.id=a.category_id AND sc.active=1
				INNER JOIN ' . DB::getTablePrefix() . 'categories as c ON c.id=sc.parent_id AND c.active=1
				WHERE a.country_code = :country_code AND a.active=1 AND a.archived!=1 AND a.deleted_at IS NULL
				GROUP BY sc.id';
        $bindings = ['country_code' => $this->country->get('code')];
        $count_sub_cat_ads = DB::select(DB::raw($sql), $bindings);
        $count_sub_cat_ads = collect($count_sub_cat_ads)->keyBy('id');
        view()->share('count_sub_cat_ads', $count_sub_cat_ads);

        // COUNT PARENT CATEGORIES ADS COLLECTION
        $sql = 'SELECT c.id, count(*) as total
				FROM ' . DB::getTablePrefix() . 'ads as a
				INNER JOIN ' . DB::getTablePrefix() . 'categories as sc ON sc.id=a.category_id AND sc.active=1
				INNER JOIN ' . DB::getTablePrefix() . 'categories as c ON c.id=sc.parent_id AND c.active=1
				WHERE a.country_code = :country_code AND a.active=1 AND a.archived!=1 AND a.deleted_at IS NULL
				GROUP BY c.id';
        $bindings = ['country_code' => $this->country->get('code')];
        $count_cat_ads = DB::select(DB::raw($sql), $bindings);
        $count_cat_ads = collect($count_cat_ads)->keyBy('id');
        view()->share('count_cat_ads', $count_cat_ads);


        // CITIES COLLECTION
        $cities = City::where('country_code', '=', $this->country->get('code'))->take(100)->orderBy('population',
            'DESC')->orderBy('name')->get();
        view()->share('cities', $cities);


        // ADTYPE COLLECTION
        $ad_types = AdType::orderBy('name')->get();
        view()->share('ad_types', $ad_types);

        // STATES COLLECTION => MODAL
        $states = SubAdmin1::where('code', 'LIKE', $this->country->get('code') . '.%')->orderBy('name')->get(['code', 'name'])->keyBy('code');
        view()->share('states', $states);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->isIndexSearch = true;

		$cat = $this->getSelectedCategory();
		$location = $this->getSelectedLocation();

        $search = new Search($this->request, $this->country, $this->lang);
        $data = $search->fechAll();
        view()->share('count', $data['count']);
        view()->share('ads', $data['ads']);

        $this->exportRequiredVars();
        
        // HEAD: BUILD TITLE & DESCRIPTION
        if ($this->isIndexSearch) {
            $title = t('Search for') . ' ';
            if (Input::has('q') and Input::has('c') and Input::has('l')) {
                $title .= Input::get('q') . ' ' . $this->cat->name . ' - ' . $this->city->name;
            } else {
                if (Input::has('q') and Input::has('c') and !Input::has('l')) {
                    $title .= Input::get('q') . ' ' . $this->cat->name;
                } else {
                    if (Input::has('q') and !Input::has('c') and Input::has('l')) {
                        $title .= Input::get('q') . ' - ' . $this->city->name;
                    } else {
                        if (!Input::has('q') and Input::has('c') and Input::has('l')) {
                            $title .= $this->cat->name . ' - ' . $this->city->name;
                        } else {
                            if (Input::has('q') and !Input::has('c') and !Input::has('l')) {
                                $title .= Input::get('q');
                            } else {
                                if (!Input::has('q') and Input::has('c') and !Input::has('l')) {
                                    $title .= t('free ads') . ' ' . $this->cat->name;
                                } else {
                                    if (!Input::has('q') and !Input::has('c') and Input::has('l')) {
                                        $title .= t('free ads in') . ' - ' . $this->city->name;
                                    } else {
                                        if (Input::has('r')) {
                                            $title .= t('free ads in') . ' ' . $this->city->name;
                                        } else {
                                            if (!Input::has('q') and !Input::has('c') and !Input::has('l') and !Input::has('r')) {
                                                $title = t('Latest free ads');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $title = t('Free ads in');
            if ($this->isCatSearch) {
                $title .= ' ' . $this->cat->name;
            } else {
                if ($this->isLocSearch) {
                    $title .= ' ' . $this->city->name;
                }
            }
        }
        // Meta Tags
        MetaTag::set('title', $title . ', ' . $this->country->get('name'));
        MetaTag::set('description', $title);
        
        return view('search.serp');
    }

    /**
     * @param $countryCode
     * @param null $catSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function category($countryCode, $catSlug = null)
    {
        // Check multi-countries site parameters
        if (!config('larapen.core.multi_countries_website')) {
            $catSlug = $countryCode;
        }

        $this->isCatSearch = true;

		// Get category ID
		$cat = $this->getSelectedCategory($catSlug);
		if (empty($cat)) {
			$cat = Category::where('translation_lang', $this->lang->get('abbr'))->where('slug', 'LIKE', $catSlug)->first();
		}
        
        $cat_id = ($cat) ? $cat->tid : 0;
        
        $search = new Search($this->request, $this->country, $this->lang);
        $data = $search->setCategory($cat_id)->setRequestFilters()->fetch();

        view()->share('uriPathCatSlug', $catSlug);
        $this->exportRequiredVars();
        
        // SEO
        $title = $cat->name . ' - ' . t('Free ads :category in :location', ['category' => $cat->name, 'location' => $this->country->get('name')]);
        $description = str_limit(t('Free ads :category in :location', [
                'category' => $cat->name,
                'location' => $this->country->get('name')
            ]) . '. ' . t('Looking for a product or service') . ' - ' . $this->country->get('name'), 200);
        
        // Meta Tags
        MetaTag::set('title', $title);
        MetaTag::set('description', $description);
        
        // Open Graph
        $this->og->title($title)->description($description)->type('website');
        if ($data['count']->get('all') > 0) {
            $filtered = $data['ads']->getCollection();
            if ($this->og->has('image')) {
                $this->og->forget('image')->forget('image:width')->forget('image:height');
            }
            /*
            foreach($pictures->get() as $picture) {
                $this->og->image(url('pic/x/cache/large/' . $picture->filename),
                    [
                        'width'     => 600,
                        'height'    => 600
                    ]);
            }
            */
        }
        view()->share('og', $this->og);
        
        return view('search.serp', $data);
    }

    /**
     * @param $countryCode
     * @param $catSlug
     * @param null $subCatSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function subCategory($countryCode, $catSlug, $subCatSlug = null)
    {
        // Check multi-countries site parameters
        if (!config('larapen.core.multi_countries_website')) {
            $subCatSlug = $catSlug;
            $catSlug = $countryCode;
        }

        $this->isCatSearch = true;

        // Get sub-category ID
		$cat = $this->getSelectedCategory($catSlug, $subCatSlug);
		if (empty($cat)) {
			$cat = Category::where('translation_lang', $this->lang->get('abbr'))->where('slug', 'LIKE', $catSlug)->first();
            if (empty($cat)) {
                abort(404);
            }
		}
        $sub_cat = Category::where('translation_lang', $this->lang->get('abbr'))->where('parent_id', '=', $cat->tid)->where('slug', 'LIKE', $subCatSlug)->first();
        $sub_cat_id = ($sub_cat) ? $sub_cat->tid : 0;

        // Redirect to parent category if sub-category not found
        if (!isset($sub_cat_id) or $sub_cat_id <= 0 or !is_numeric($sub_cat_id)) {
            if (!is_null($cat)) {
                return redirect($this->lang->get('abbr') . '/' . trans('routes.v-search-cat', ['catSlug' => $cat->slug]));
            } else {
                abort(404);
            }
        }
        
        $search = new Search($this->request, $this->country, $this->lang);
        $data = $search->setSubCategory($sub_cat_id)->setRequestFilters()->fetch();

        view()->share('uriPathCatSlug', $catSlug);
        view()->share('uriPathSubCatSlug', $subCatSlug);
        $this->exportRequiredVars();
        
        // Meta Tags
        MetaTag::set('title', $sub_cat->name . ' - ' . t('Free ads :category in :location',
                ['category' => $cat->name, 'location' => $this->country->get('name')]));
        MetaTag::set('description', t('Free ads :category in :location', [
                'category' => $sub_cat->name,
                'location' => $this->country->get('name')
            ]) . '. ' . t('Looking for a product or service') . ' - ' . $this->country->get('name'));
        
        return view('search.serp', $data);
    }

    /**
     * @param $countryCode
     * @param $cityName
     * @param null $cityId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function location($countryCode, $cityName, $cityId = null)
    {
        // Check multi-countries site parameters
        if (!config('larapen.core.multi_countries_website')) {
            $cityId = $cityName;
            $cityName = $countryCode;
        }

        $this->isLocSearch = true;

        $location = $this->getSelectedLocation($cityName, $cityId);
        if (empty($location)) {
            abort(404);
        }
        
        $search = new Search($this->request, $this->country, $this->lang);
        $data = $search->setLocation($location->latitude, $location->longitude)->setRequestFilters()->fetch();

        view()->share('uriPathCityName', $cityName);
        view()->share('uriPathCityId', $cityId);
        $this->exportRequiredVars();
        
        // Meta Tags
        MetaTag::set('title',
            $location->name . ' - ' . t('Free ads in :location', ['location' => $location->name]) . ', ' . $this->country->get('name'));
        MetaTag::set('description', t('Free ads in :location',
                ['location' => $location->name]) . ', ' . $this->country->get('name') . '. ' . t('Looking for a product or service') . ' - ' . $location->name . ', ' . $this->country->get('name'));
        
        return view('search.serp', $data);
    }

    /**
     * @param $countryCode
     * @param null $userId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function user($countryCode, $userId = null)
    {
        // Check multi-countries site parameters
        if (!config('larapen.core.multi_countries_website')) {
            $userId = $countryCode;
        }

        $this->isUserSearch = true;

        $search = new Search($this->request, $this->country, $this->lang);
        $data = $search->setUser($userId)->setRequestFilters()->fetch();

        $this->exportRequiredVars();
        
        return view('search.serp', $data);
    }


	/**
	 * CATEGORY SELECTED
	 *
	 * @param null $catSlug
	 * @param null $subCatSlug
	 * @return mixed|null
	 */
    private function getSelectedCategory($catSlug = null, $subCatSlug = null)
	{
		if (!$this->isCatSearch and !Input::has('c')) {
			return null;
		}

		$cat = null;

		if (Input::has('c')) {
			$cat = $this->cats->get((int)Input::get('c'));
		} else {
			if (is_null($subCatSlug)) {
				$cat = $this->cats->whereStrict('slug', $catSlug)->flatten()->get(0);
			} else {
				$cat = $this->cats->whereStrict('slug', $catSlug)->flatten()->get(0);
				$sub_cat = $this->cats->whereStrict('slug', $subCatSlug)->flatten()->get(0);
                view()->share('sub_cat', $sub_cat);
			}
		}

		if (empty($cat)) {
			abort(404);
		}

		$this->cat = $cat;
        view()->share('cat', $cat);

		return $cat;
	}

	/**
	 * CITY SELECTED
	 *
	 * @param null $cityName
	 * @param null $cityId
	 * @return array|null|\stdClass
	 */
	private function getSelectedLocation($cityName = null, $cityId = null)
	{
		if (is_null($cityId) and !Input::has('r') and !Input::has('l') and !Input::has('location')) {
			return null;
		}

		if (Input::has('r')) {
			// If REGION
			// NOTE: city = SubAdmin1 (Just for Search result page title)
			$region = Input::get('r');
			$city = Search::searchCountryPopularCityByRegion($this->country->get('code'), $region);

			// If empty... then return collection of URL parameters
			if (empty($city)) {
				$city = Arr::toObject(['name' => $region . ' (-)', 'subadmin1_code' => 0]);
			}
		}
		else
		{
			// If NOT REGION
			if (Input::has('l'))
			{
				$city = City::find(Input::get('l'));

			}
			else if (Input::has('location'))
			{
				$cityName = rawurldecode(Input::get('location'));
				$city = City::where('country_code', $this->country->get('code'))->where('name', 'LIKE', $cityName)->first();
				if (empty($city)) {
					$city = City::where('country_code', $this->country->get('code'))->where('name', 'LIKE', '% ' . $cityName)->first();
					if (empty($city)) {
						$city = City::where('country_code', $this->country->get('code'))->where('name', 'LIKE', $cityName . ' %')->first();
					}
				}
			}
			else
			{
				// Get City by Id
				$city = City::find((int)$cityId);

				// Get City by (raw) Name - @todo: delete this in the next releases
				if (empty($city)) {
					$cityName = rawurldecode($cityName);
					$city = City::where('country_code', $this->country->get('code'))->where('name', 'LIKE', $cityName)->first();
					if (empty($city)) {
						$city = City::where('country_code', $this->country->get('code'))->where('name', 'LIKE', '% ' . $cityName)->first();
						if (empty($city)) {
							$city = City::where('country_code', $this->country->get('code'))->where('name', 'LIKE', $cityName . ' %')->first();
						}
					}
				}
			}
		}

		if (empty($city)) {
			abort(404);
		}

		$this->city = $city;
        view()->share('city', $city);

		return $city;
	}

    private function exportRequiredVars()
    {
        view()->share('isIndexSearch', $this->isIndexSearch);
        view()->share('isCatSearch', $this->isCatSearch);
        view()->share('isLocSearch', $this->isLocSearch);
        view()->share('isUserSearch', $this->isUserSearch);
    }
}
