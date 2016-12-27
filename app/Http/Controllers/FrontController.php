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

use Illuminate\Support\Facades\Input;
use ChrisKonnertz\OpenGraph\OpenGraph;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\App;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use App\Larapen\Helpers\Localization\Country as CountryLocalization;
use App\Larapen\Helpers\Localization\Language as LanguageLocalization;
use App\Larapen\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use Torann\LaravelMetaTags\Facades\MetaTag;
use Crypt;

class FrontController extends Controller
{
    public $request;
    public $data = array();

    public $lang;
    public $country;
	public $ip_country;
    public $user;
    public $og;

    public $cookie_expire;
    public $cache_expire = 3600; // 1h
    public $ads_pictures_number = 1;

    /**
     * FrontController constructor.
     */
    public function __construct()
    {
        // Initialization
        $this->lang = collect([]);
        $this->country = collect([]);
        $this->ip_country = collect([]);
        $this->user = null;

        // From Laravel 5.3.4 or above
        $this->middleware(function ($request, $next)
		{
            $this->getLocalization();
			$this->setFrontSettings();

            return $next($request);
        });
    }


    /**
     * Get Localization
     * Get Locale from browser or from country spoken language
     * and get Country by user IP
     */
    private function getLocalization()
    {
        // Language
        $langObj = new LanguageLocalization();
        $lang = $langObj->find();

        // Country
        $countryObj = new CountryLocalization();
        $countryObj->find();
        $countryObj->setCountryParameters();


        // Share var in Controller
        $this->lang = (!empty($lang)) ? $lang : collect([]);
        $this->country = (!empty($countryObj->country)) ? $countryObj->country : collect([]);
        $this->ip_country = (!empty($countryObj->ip_country)) ? $countryObj->ip_country : collect([]);
        $this->user = (!empty($countryObj->user)) ? $countryObj->user : null;


        // Session : Set country code
        if (!$this->country->isEmpty() and $this->country->has('code')) {
            session(['country_code' => $this->country->get('code')]);
        }
        // Session : Set timezome
        if (!$this->country->isEmpty() and $this->country->has('timezone') and !empty($this->country->get('timezone'))) {
            session(['time_zone' => $this->country->get('timezone')->time_zone_id]);
        }
        // Session : language code
        if (!$this->lang->isEmpty()) {
            //Session::forget('language_code');
            session(['language_code' => $this->lang->get('abbr')]);
        }


        // Share vars to views
        view()->share('lang', $lang);
        view()->share('user', $this->user);
        view()->share('country', $this->country);
        view()->share('ip_country', $this->ip_country);
    }

	/**
	 * Set all the front-end settings
	 */
	public function setFrontSettings()
	{
		// Cache duration setting /============================================================
		$this->cache_expire = (int)config('settings.app_cache_expire');

		// Ads photos number
		$ads_pictures_number = (int)config('settings.ads_pictures_number');
		if ($ads_pictures_number >= 1 and $ads_pictures_number <= 20) {
			$this->ads_pictures_number = $ads_pictures_number;
		}
		if ($ads_pictures_number > 20) {
			$this->ads_pictures_number = 20;
		}
		view()->share('ads_pictures_number', $this->ads_pictures_number);


		// Default language for Bots /=========================================================
		$crawler = new CrawlerDetect();
		if ($crawler->isCrawler()) {
			$this->lang = $this->country->get('lang');
			view()->share('lang', $this->lang);
			App::setLocale($this->lang->get('abbr'));
		}

		// Set Local
		if (!$this->lang->isEmpty()) {
			setlocale(LC_ALL, $this->lang->get('locale'));
		}

		// Set Language for Countries /========================================================
		$this->country = CountryLocalizationHelper::trans($this->country, $this->lang->get('abbr'));
		view()->share('country', $this->country);


		// DNS Prefetch meta tags /============================================================
		$dns_prefetch = [
			'//fonts.googleapis.com',
			'//graph.facebook.com',
			'//google.com',
			'//apis.google.com',
			'//ajax.googleapis.com',
			'//www.google-analytics.com',
			'//pagead2.googlesyndication.com',
			'//gstatic.com',
			'//cdn.api.twitter.com',
			'//oss.maxcdn.com',
		];
		view()->share('dns_prefetch', $dns_prefetch);


		// Set Config /=========================================================================
		// App name
		config(['app.name' => config('settings.app_name')]);
		// reCAPTCHA
		config(['recaptcha.public_key' => env('RECAPTCHA_PUBLIC_KEY', config('settings.recaptcha_public_key'))]);
		config(['recaptcha.private_key' => env('RECAPTCHA_PRIVATE_KEY', config('settings.recaptcha_private_key'))]);
		// Mail
		config(['mail.driver' => env('MAIL_DRIVER', config('settings.mail_driver'))]);
		config(['mail.host' => env('MAIL_HOST', config('settings.mail_host'))]);
		config(['mail.port' => env('MAIL_PORT', config('settings.mail_port'))]);
		config(['mail.encryption' => env('MAIL_ENCRYPTION', config('settings.mail_encryption'))]);
		config(['mail.username' => env('MAIL_USERNAME', config('settings.mail_username'))]);
		config(['mail.password' => env('MAIL_PASSWORD', config('settings.mail_password'))]);
		config(['mail.from.address' => config('settings.app_email')]);
		config(['mail.from.name' => config('settings.app_name')]);
		// Mailgun
		config(['services.mailgun.domain' => env('MAILGUN_DOMAIN', config('settings.mailgun_domain'))]);
		config(['services.mailgun.secret' => env('MAILGUN_SECRET', config('settings.mailgun_secret'))]);
		// Mandrill
		config(['services.mandrill.secret' => env('MANDRILL_SECRET', config('settings.mandrill_secret'))]);
		// Amazon SES
		config(['services.ses.key' => env('SES_KEY', config('settings.ses_key'))]);
		config(['services.ses.secret' => env('SES_SECRET', config('settings.ses_secret'))]);
		config(['services.ses.region' => env('SES_REGION', config('settings.ses_region'))]);
		// Stripe
		config(['services.stripe.key' => env('STRIPE_KEY', config('settings.stripe_key'))]);
		config(['services.stripe.secret' => env('STRIPE_SECRET', config('settings.stripe_secret'))]);
		// PayPal
		config(['services.paypal.mode' => env('PAYPAL_MODE', config('settings.paypal_mode'))]);
		config(['services.paypal.username' => env('PAYPAL_USERNAME', config('settings.paypal_username'))]);
		config(['services.paypal.password' => env('PAYPAL_PASSWORD', config('settings.paypal_password'))]);
		config(['services.paypal.signature' => env('PAYPAL_SIGNATURE', config('settings.paypal_signature'))]);
		// Facebook
		config(['services.facebook.client_id' => env('FACEBOOK_CLIENT_ID', config('settings.facebook_client_id'))]);
		config(['services.facebook.client_secret' => env('FACEBOOK_CLIENT_SECRET', config('settings.facebook_client_secret'))]);
		// Google
		config(['services.google.client_id' => env('GOOGLE_CLIENT_ID', config('settings.google_client_id'))]);
		config(['services.google.client_secret' => env('GOOGLE_CLIENT_SECRET', config('settings.google_client_secret'))]);
		config(['services.googlemaps.key' => env('GOOGLE_MAPS_API_KEY', config('settings.googlemaps_key'))]);
		// Meta-tags
		config(['meta-tags.title' => config('settings.app_slogan')]);
		config(['meta-tags.open_graph.site_name' => config('settings.app_name')]);
		config(['meta-tags.twitter.creator' => config('settings.twitter_username')]);
		config(['meta-tags.twitter.site' => config('settings.twitter_username')]);

		// Fix unknown public folder (for elFinder)
		config(['filesystems.disks.uploads-disk.root' => public_path('uploads')]); // Only in version 1.0
		config(['elfinder.roots.0.path' => public_path('uploads')]);


		// SEO /===============================================================================
		if (config('settings.app_name') and config('settings.app_slogan')) {
			$title = config('settings.app_name') . ' - ' . config('settings.app_slogan');
		} else {
			$title = t('Free local classified ads in :location', ['location' => $this->country->get('name')]);
		}
		if (config('settings.meta_description')) {
			$description = config('settings.meta_description');
		} else {
			$description = str_limit(str_strip(t('Sell and Buy products and services on :app_name in Minutes',
					['app_name' => mb_ucfirst(config('settings.app_name'))]) . ' ' . $this->country->get('name') . '. ' . t('Free ads in :location',
					['location' => $this->country->get('name')]) . '. ' . t('Looking for a product or service') . ' - ' . $this->country->get('name')),
				200);
		}

		// Meta Tags
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));


		// Open Graph /=========================================================================
		//->localeAlternate(['en_US']) // @todo: Get all Language locale
		$this->og = new OpenGraph();
		$locale = $this->lang->has('locale') ? $this->lang->get('locale') : 'en_US';
		$this->og->siteName(config('settings.app_name'))->locale($locale)->type('website')->url(url(Request::getRequestUri()));
		view()->share('og', $this->og);


		// CSRF Control /========================================================================
		// CSRF - Some JavaScript frameworks, like Angular, do this automatically for you.
		// It is unlikely that you will need to use this value manually.
		setcookie('X-XSRF-TOKEN', csrf_token(), $this->cookie_expire, '/', getDomain());


		// Theme selection /=====================================================================
		if (Input::has('theme')) {
			if (file_exists(public_path() . '/assets/css/style/' . Input::get('theme') . '.css')) {
				config(['app.theme' => Input::get('theme')]);
			} else {
				config(['app.theme' => config('settings.app_theme')]);
			}
		} else {
			config(['app.theme' => config('settings.app_theme')]);
		}
	}
}
