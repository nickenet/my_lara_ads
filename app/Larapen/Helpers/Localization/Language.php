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
use App\Larapen\Models\Language as LanguageModel;
use App\Larapen\Models\Country as CountryModel;
use App\Larapen\Helpers\Localization\Helpers\Country as CountryHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use PulkitJalan\GeoIP\Facades\GeoIP;

class Language
{
    protected $country;
    
    public function __construct()
    {
        $this->app = app();
        
        $this->configRepository = $this->app['config'];
        $this->view = $this->app['view'];
        $this->translator = $this->app['translator'];
        $this->router = $this->app['router'];
        $this->request = $this->app['request'];
        
        // set default locale
        $this->defaultLocale = $this->configRepository->get('app.locale');
    }

    /**
     * Find language
     *
     * @return Collection|Language
     */
    public function find()
    {
        // Detect Language
        $lang = $this->fromUrl();

        return $lang;
    }

    /**
     * Get language from URL
     *
     * @return Language|Collection
     */
    public function fromUrl()
    {
        $lang_code = $hreflang = Request::segment(1);
        if ($lang_code != '') {
            $is_available_lang = collect(LanguageModel::where('abbr', $lang_code)->first());
            if (!$is_available_lang->isEmpty()) {
                $lang = $is_available_lang->merge(collect(['hreflang' => $hreflang]));
            } else {
                $lang = $this->fromBrowser();
            }
        } else {
            $lang = $this->fromBrowser();
        }

        return $lang;
    }

    /**
     * Get language from Browser
     *
     * @return Language|Collection
     */
    public function fromBrowser()
    {
        if (!config('larapen.core.detect_browser_language')) {
            return $this->fromConfig();
        }

        // Get browser language
        $accept_language = Request::server('HTTP_ACCEPT_LANGUAGE');
        $accept_language_tab = explode(',', $accept_language);
        $lang_tab = [];
        if (!empty($accept_language_tab)) {
            foreach ($accept_language_tab as $key => $value) {
                $tmp = explode(';', $value);
                if (empty($tmp)) continue;

                if (isset($tmp[0]) and isset($tmp[1])) {
                    $q = str_replace('q=', '', $tmp[1]);
                    $lang_tab[ $value ] = ['code' => $tmp[0], 'q' => (double) $q];
                } else {
                    $lang_tab[ $value ] = ['code' => $tmp[0], 'q' => 1];
                }
            }
        }

        // Get country info \w country language
        $country = self::getCountryFromIP();

        // Search the default language (Intersection Browser & Country language OR First Browser language)
        $lang_code = $hreflang = '';
        if (!empty($lang_tab)) {
            foreach ($lang_tab as $key => $value) {
                if (!$country->isEmpty() and $country->has('lang')) {
                    if (!$country->get('lang')->isEmpty() and $country->get('lang')->has('abbr')) {
                        if (str_contains($value['code'], $country->get('lang')->get('abbr'))) {
                            $lang_code = substr($value['code'], 0, 2);
                            $hreflang = $lang_code;
                            break;
                        }
                    }
                } else {
                    if ($lang_code == '') {
                        $lang_code = substr($value['code'], 0, 2);
                        $hreflang = $lang_code;
                    }
                }
            }
        }

        // Check language
        if ($lang_code != '') {
            $is_available_lang = collect(LanguageModel::where('abbr', $lang_code)->first());
            if (!$is_available_lang->isEmpty()) {
                $lang = $is_available_lang->merge(collect(['hreflang' => $hreflang]));
            } else {
                $lang = $this->fromConfig();
            }
        } else {
            $lang = $this->fromConfig();
        }

        return $lang;
    }

    /**
     * Get language from Database or Config file
     *
     * @return mixed
     */
    public function fromConfig()
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
            try {
                $lang = collect(LanguageModel::where('abbr', config('app.locale'))->first())->merge(collect(['hreflang' => config('app.locale')]));
            } catch (\Exception $e) {
                $lang = collect(['abbr' => config('app.locale'), 'hreflang' => config('app.locale')]);
            }

            // Check if language code exists
            if (!$lang->has('abbr')) {
                $lang = collect(['abbr' => config('app.locale'), 'hreflang' => config('app.locale')]);
            }
        }

        return $lang;
    }

    /**
     * @return Collection
     */
    public static function supportedLanguage()
    {
        $languages = LanguageModel::where('active', 1)->get();

        return collect($languages);
    }

    /**
     * @param $countries
     * @param string $locale
     * @param string $source
     * @return Collection|static
     */
    public function countries($countries, $locale = 'en', $source = 'cldr')
    {
        // Security
        if (!$countries instanceof Collection) {
            return collect([]);
        }

        //$locale = 'en'; // debug
        $country_lang = new CountryHelper();
        $tab = [];
        foreach ($countries as $code => $country) {
            $tab[$code] = $country;
            if ($name = $country_lang->get($code, $locale, $source)) {
                $tab[$code]['name'] = $name;
            }
        }

        //return collect($tab);
        return collect($tab)->sortBy('name');
    }

    /**
     * @param $country
     * @param string $locale
     * @param string $source
     * @return Collection|static
     */
    public function country($country, $locale = 'en', $source = 'cldr')
    {
        // Security
        if (!$country instanceof Collection) {
            return collect([]);
        }

        //$locale = 'en'; // debug
        $country_lang = new CountryHelper();
        if ($name = $country_lang->get($country->get('code'), $locale, $source)) {
            return $country->merge(['name' => $name]);
        } else {
            return $country;
        }
    }

    /**
     * @param $country_code
     * @return bool|\stdClass
     */
    public function getCountryInfo($country_code)
    {
        if (trim($country_code) == '') {
            return collect([]);
        }
        $country_code = strtoupper($country_code);

        $country = CountryModel::find($country_code)->toArray();
        if (count($country) == 0) {
            return collect([]);
        }

        $country = collect($country);

        return $country;
    }



    /**
     * @return bool|mixed|\stdClass
     */
    public static function getCountryFromIP()
    {
        $country = Country::getCountryFromCookie();
        if (!$country->isEmpty()) {
            return $country;
        } else {
            // GeoIP
            $country_code = self::getCountryCodeFromIP();
            if (!$country_code or trim($country_code) == '') {
                // Geolocalization has failed
                return collect([]);
            }

            return Country::setCountryToCookie($country_code);
        }
    }

    /**
     * @return bool|string
     */
    public static function getCountryCodeFromIP()
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
            return false;
        }

        return strtolower($country_code);
    }
}
