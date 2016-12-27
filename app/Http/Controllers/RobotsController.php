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

class RobotsController extends FrontController
{
    public function index()
    {
        error_reporting(0);
        $robots_txt = @file_get_contents('robots.txt');
        
        // Get countries list
        $countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries(), $this->lang->get('abbr'));
        
        // Sitemaps
        if (!$countries->isEmpty()) {
            foreach ($countries as $item) {
                $country = CountryLocalization::getCountryInfo($item->get('code'));
                $robots_txt .= "\n" . 'Sitemap: ' . url($country->get('lang')->get('abbr') . '/' . $country->get('icode') . '/sitemaps.xml');
            }
        }
        
        // Rending
        header("Content-Type:text/plain");
        echo $robots_txt;
    }
}
