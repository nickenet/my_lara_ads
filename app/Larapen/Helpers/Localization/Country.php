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

namespace App\Larapen\Helpers\Localization;

use App\Larapen\Helpers\Ip;
use App\Larapen\Models\Ad;
use App\Larapen\Models\Country as CountryModel;
use App\Larapen\Models\Currency;
use App\Larapen\Models\Language as LanguageModel;
use App\Larapen\Models\TimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Larapen\Settings\app\Models\Setting;
use PulkitJalan\GeoIP\Facades\GeoIP;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class Country
{
    public $default_country = '';
    public $default_uri = '/';
    public $countries_list_uri = '/countries';
    
    public $user;
    public $countries;
    public $country;
    public $ip_country;
    public $site_country_info = '';
    
    public static $cache_expire = 60;
    public static $cookie_expire = 60;
    
    // Maxmind Support URL
    private static $maxmind_support_url = 'http://www.bedigit.com/guide/laraclassified/';
    
    
    public function __construct()
    {
        $this->app = app();
        
        $this->configRepository = $this->app['config'];
        $this->view = $this->app['view'];
        $this->translator = $this->app['translator'];
        $this->router = $this->app['router'];
        $this->request = $this->app['request'];
        $this->language = new Language();
        
        // Default values
        $this->default_country_code = config('settings.app_default_country');
        $this->default_url = url(config('larapen.localization.default_uri'));
        $this->default_page = url(config('app.locale') . '/' . trans('routes.' . config('larapen.localization.countries_list_uri')));
        
        // Cache and Cookies Expires
        self::$cache_expire = config('settings.app_cache_expire');
        self::$cookie_expire = config('settings.app_cookie_expire');
        
        // Check if User is logged
        $this->user = $this->checkUser();
        
        // Init. Country Infos
        $this->country = collect([]);
        $this->ip_country = collect([]);
    }
    
    /**
     * @return bool|mixed|\stdClass
     */
    public function find()
    {
        // Get user IP country
        $this->ip_country = $this->getCountryFromIP();
        
        // Get current country
        $this->country = $this->getCountryFromQueryString();
        if ($this->country->isEmpty()) {
            $this->country = $this->getCountryFromAd();
            if ($this->country->isEmpty()) {
                $this->country = $this->getCountryFromURIPath();
                if ($this->country->isEmpty()) {
                    $this->country = $this->getCountryForBots();
                }
            }
        }
        
        if ($this->request->session()->has('country_code') and $this->country->isEmpty()) {
            $this->country = self::getCountryInfo(session('country_code'));
        } else {
            if ($this->country->isEmpty()) {
                $this->country = $this->getDefaultCountry($this->default_country_code);
            }
            if ($this->country->isEmpty()) {
                if (!$this->ip_country->isEmpty() and $this->ip_country->has('code')) {
                    $this->country = $this->ip_country;
                }
            }
        }
        
        return $this->country;
    }
    
    /**
     * @return bool
     */
    public function setCountryParameters()
    {
        // SKIP Countries selection page
        if (
            in_array(
                trans('routes.' . config('larapen.localization.countries_list_uri')),
                [
                    getSegment(1),
                ]
            )
        )
        {
            return false;
        }
        // SKIP All xml page (Sitemaps)
        if (ends_with($this->request->url(), '.xml')) {
            return false;
        }
        
        
        // Redirect country not found
        if (!$this->isAvailableCountry($this->country->get('code'))) {
            // Redirect to country selection page
            header('Location: ' . $this->default_page, true, 301);
            exit();
        }
        
        // SiteInfo : Not logged
        if (!Auth::check() and !in_array(getSegment(1), [
                trans('routes.login'),
                trans('routes.signup'),
                trans('routes.create'),
                trans('routes.about'),
                trans('routes.contact'),
                trans('routes.faq'),
                trans('routes.phishing'),
                trans('routes.anti-scam'),
                trans('routes.sitemap'),
                trans('routes.terms'),
                trans('routes.privacy')
            ]) and !Input::has('iam') and getSegment(1) !== null /* and is_null(getAdIdFromURLSegment()) */ and !str_contains(Route::currentRouteAction(),
                'SearchController') and !str_contains(Route::currentRouteAction(), 'SitemapController') and !str_contains(Route::currentRouteAction(),
                'PasswordController')
        ) {
            $msg = 'Login for faster access to the best deals. Click here if you don\'t have an account.';
            $this->site_country_info = t($msg, [
                'login_url' => lurl(trans('routes.login')),
                'register_url' => lurl(trans('routes.signup'))
            ], 'global', config('app.locale'));
        }
        
        // SiteInfo : Country - We know the user IP country and selected country
        if (config('settings.activation_geolocation')) {
            if (!$this->ip_country->isEmpty() and !$this->country->isEmpty()) {
                if ($this->ip_country->get('code') != $this->country->get('code')) {
                    $url = url(self::getLangFromCountry($this->ip_country->get('languages'))->get('code') . '/?d=' . $this->ip_country->get('code'));
                    $msg = ':app_name is also available in your country: :country. Start the best deals here now!';
                    $this->site_country_info = t($msg,
                        ['app_name' => config('settings.app_name'), 'country' => $this->ip_country->get('name'), 'url' => $url]);
                }
            }
        }

        // Share vars to views
        if (isset($this->site_country_info) and $this->site_country_info != '') {
            view()->share('site_country_info', $this->site_country_info);
        }
        
        return true;
    }
    
    public function getDefaultCountry($default_country_code)
    {
        // Check default country
        if (trim($default_country_code) != '') {
            if ($this->isAvailableCountry($default_country_code)) {
                return self::getCountryInfo($default_country_code);
            }
        }
        
        return collect([]);
    }
    
    /**
     * Get Country from logged User
     * @return bool|\stdClass
     */
    public function getCountryFromUser()
    {
        if (Auth::check()) {
            if (isset($this->user) and isset($this->user->country_code)) {
                if ($this->isAvailableCountry($this->user->country_code)) {
                    return self::getCountryInfo($this->user->country_code);
                }
            }
        }
        
        return collect([]);
    }
    
    /**
     * Get Country from logged User
     * @return bool|\stdClass
     */
    public function getCountryFromAd()
    {
        // Get ad ID from URL segment
        $ad_id = getAdIdFromURLSegment();

        // Return empty collection if ad ID not found
        if (is_null($ad_id)) {
            return collect([]);
        }
        
        // GET ADS INFO
        $ad = Ad::active()->where('id', $ad_id)->first();
        if (is_null($ad)) {
            return collect([]);
        }
        
        $country_code = $ad->country_code;
        
        if ($this->isAvailableCountry($country_code)) {
            return self::getCountryInfo($country_code);
        }
        
        return collect([]);
    }
    
    /**
     * Get Country from Domain
     * @return bool|\stdClass
     */
    public function getCountryFromDomain()
    {
        $country_code = getSubDomainName();
        if ($this->isAvailableCountry($country_code)) {
            return self::getCountryInfo($country_code);
        }
        
        return collect([]);
    }
    
    /**
     * Get Country from Query String
     * @return bool|\stdClass
     */
    public function getCountryFromQueryString()
    {
        $country_code = '';
        if (Input::has('site')) {
            $country_code = Input::get('site');
        }
        if (Input::has('d')) {
            $country_code = Input::get('d');
        }
        
        if ($this->isAvailableCountry($country_code)) {
            return self::getCountryInfo($country_code);
        }
        
        return collect([]);
    }
    
    /**
     * Get Country from Query String
     * @return bool|\stdClass
     */
    public function getCountryFromURIPath()
    {
        $country_code = getSegment(2);
        if ($this->isAvailableCountry($country_code)) {
            return self::getCountryInfo($country_code);
        }
        
        return collect([]);
    }
    
    /**
     * Get Country for Bots if not found
     * @return bool|\stdClass
     */
    public function getCountryForBots()
    {
		$crawler = new CrawlerDetect();
        if ($crawler->isCrawler()) {
            // Don't set the default country for homepage
            if (!str_contains(Route::currentRouteAction(), 'HomeController'))
            {
                $country_code = config('settings.app_default_country');
                if ($this->isAvailableCountry($country_code)) {
                    return self::getCountryInfo($country_code);
                }
            }
        }
        
        return collect([]);
    }
    
    
    /**
     * @return bool|mixed|\stdClass
     */
    public function getCountryFromIP()
    {
        $country = self::getCountryFromCookie();
        if (!$country->isEmpty()) {
            if ($country->get('level') == 'user') { // @todo: Check if user has logged
                $country = self::getCountryInfo($country->get('code'));
            }
            
            return $country;
        } else {
            // GeoIP
            $country_code = $this->getCountryCodeFromIP();
            if (!$country_code or trim($country_code) == '') {
                // Geolocalization has failed
                return collect([]);
            }
            
            return self::setCountryToCookie($country_code);
        }
    }

    /**
     * @param $country_code
     * @return bool|\Illuminate\Support\Collection|\stdClass
     */
    public static function setCountryToCookie($country_code)
    {
        if (trim($country_code) == '') {
            return collect([]);
        }
        
        if (isset($_COOKIE['ip_country_code'])) {
            unset($_COOKIE['ip_country_code']);
        }

        setcookie('ip_country_code', $country_code, self::$cookie_expire, '/', getDomain());
        
        return self::getCountryInfo($country_code);
    }
    
    /**
     * @return bool|mixed
     */
    public static function getCountryFromCookie()
    {
        if (isset($_COOKIE['ip_country_code'])) {
            $country_code = $_COOKIE['ip_country_code'];
            if (trim($country_code) == '') {
                return collect([]);
            } // TMP
            return self::getCountryInfo($country_code);
        } else {
            return collect([]);
        }
    }

    /**
     * @return bool|string
     */
    public function getCountryCodeFromIP()
    {
        // Localize the user's country
        try {
            $ip_addr = Ip::get();
            
            
            GeoIP::setIp($ip_addr);
            $country_code = GeoIP::getCountryCode();
            
            
            if (!is_string($country_code) or strlen($country_code) != 2) {
                return false;
            }
        } catch (\Exception $e) {
            if (config('settings.activation_geolocation')) {
                if (Auth::check()) {
                    $user = Auth::user();
                    if ($user->is_admin == 1) {
                        // Get settings
                        $setting = Setting::where('key', 'activation_geolocation')->first();
                        
                        // Notice message for admin users
                        $msg = "";
                        $msg .= "<h4><strong>Only Admin Users can see this message</strong></h4>";
                        $msg .= "<strong>Maxmind GeoLite2 City</strong> not found at: ";
                        $msg .= "<code>" . database_path('maxmind/') . "</code><br>";
                        $msg .= "Please check the <a href='" . self::$maxmind_support_url . "' target='_blank'>Maxmind database installation for LaraClassified</a> support.";
                        $msg .= "<br><br><a href='" . url(config('backpack.base.route_prefix', 'admin') . "/setting/" . $setting->id . "/edit") . "' class='btn btn-xs btn-thin btn-default-lite' id='disableGeoOption'>Disable the Geolocalization</a>";
                        flash()->warning($msg);
                    }
                }
            }
            
            return false;
        }
        
        return strtolower($country_code);
    }
    
    /**
     * @param $country_code
     * @return bool|\stdClass
     */
    public static function getCountryInfo($country_code)
    {
        if (trim($country_code) == '') {
            return collect([]);
        }
        $country_code = strtoupper($country_code);
        
        $country = CountryModel::find($country_code);
        if (is_null($country)) {
            return collect([]);
        }
        $country = $country->toArray();
        
        $currency = Currency::find($country['currency_code']);
        $lang = self::getLangFromCountry($country['languages']);
        $time_zone = TimeZone::where('country_code', 'LIKE', $country_code)->first();
        
        $country['currency'] = ($currency) ? $currency : [];
        $country['lang'] = ($lang) ? $lang : [];
        $country['timezone'] = ($time_zone) ? $time_zone : [];
        $country = collect($country);
        
        return $country;
    }

    /**
     * Only used for search bots
     * @param $languages
     * @return mixed
     */
    public static function getLangFromCountry($languages)
    {
        // Get language code
        $lang_code = $hreflang = '';
        if (trim($languages) != '') {
            $country_language = explode(',', $languages);
            $available_language = LanguageModel::all();
            if (!is_null($available_language)) {
                $found = false;
                foreach ($country_language as $isoLang) {
                    foreach ($available_language as $language) {
                        if (starts_with(strtolower($isoLang), strtolower($language->abbr))) {
                            $lang_code = $language->abbr;
                            $hreflang = $isoLang;
                            $found = true;
                            break;
                        }
                    }
                    if ($found) {
                        break;
                    }
                }
            }
        }

        // Get language info
        if ($lang_code != '') {
            $is_available_lang = collect(LanguageModel::where('abbr', $lang_code)->first());
            if (!$is_available_lang->isEmpty()) {
                $lang = $is_available_lang->merge(collect(['hreflang' => $hreflang]));
            } else {
                $lang = self::getLangFromConfig();
            }
        } else {
            $lang = self::getLangFromConfig();
        }

        return $lang;
    }

    /**
     * @return mixed
     */
    public static function getLangFromConfig()
    {
        // Default language (from Admin panel)
        try {
            $defaultLang = collect(LanguageModel::where('default', 1)->first());
        } catch (\Exception $e) {
            $defaultLang = collect([]);
        }

        if (!$defaultLang->isEmpty()) {
            config(['app.locale' => $defaultLang->get('abbr')]);
            $lang = $defaultLang->merge(collect(['hreflang' => $defaultLang->get('abbr')]));
        } else {
            // Default language (from config)
            $lang = collect(LanguageModel::where('abbr', config('app.locale'))->first())->merge(collect(['hreflang' => config('app.locale')]));
        }

        return $lang;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getCountries()
    {
        try {
            $countries = CountryModel::with('continent')->with('currency')->orderBy('asciiname')->get()->keyBy('code');
            if (empty($countries)) {
                return collect([]);
            }
        } catch (\Exception $e) {
            return collect([]);
        }
        
        // Country filters
        $tab = [];
        foreach ($countries as $code => $country) {
            // Get only Countries with currency
            if (isset($country->currency) and count($country->currency) > 0) {
                $tab[$code] = collect($country)->forget('currency_code');
            } else {
                // Just for debug
                // dd(collect($item));
            }
            
            // Get only allowed Countries with active Continent
            if (!isset($country->continent) or $country->continent->active != 1) {
                unset($tab[$code]);
            }
        }
        $countries = collect($tab);
        
        return $countries;
    }

    /**
     * @param $country_code
     * @return bool
     */
    public function isAvailableCountry($country_code)
    {
        if (!is_string($country_code) or strlen($country_code) != 2) {
            return false;
        }
        
        $countries = self::getCountries();
        $available_country_codes = is_array($countries) ? collect(array_keys($countries)) : $countries->keys();
        $available_country_codes = $available_country_codes->map(function ($item, $key) {
            return strtolower($item);
        })->flip();
        if ($available_country_codes->has(strtolower($country_code))) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Check if User is logged
     *
     * @return bool
     */
    public function checkUser()
    {
        if (Auth::check()) {
            $this->user = Auth::user();
            view()->share('user', $this->user);
            $this->userLevel = 'user';
            
            return $this->user;
        }
        
        return false;
    }
}
