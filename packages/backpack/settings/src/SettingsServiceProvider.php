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

namespace Larapen\Settings;

use Backpack\Settings\app\Models\Setting as Setting;
use Config;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Schema;
use Route;
use CRUD;

class SettingsServiceProvider extends \Backpack\Settings\SettingsServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Check DB connection and catch it
        try {
			// only use the Settings package if the Settings table is present in the database
			if (count(Schema::getColumnListing('settings'))) {
				// get all settings from the database
				$settings = Setting::all();

				// bind all settings to the Laravel config, so you can call them like
				// Config::get('settings.contact_email')
				foreach ($settings as $key => $setting) {
					Config::set('settings.'.$setting->key, $setting->value);
				}
            }
        } catch (\Exception $e) {
            // Notify DB error
            Config::set('settings.error', true);
        }

		// publish the migrations and seeds
		$this->publishes([__DIR__.'/database/migrations/' => database_path('migrations')], 'migrations');
		$this->publishes([__DIR__.'/database/seeds/' => database_path('seeds')], 'seeds');
    }

	/**
	 * Define the routes for the application.
	 *
	 * @param \Illuminate\Routing\Router $router
	 *
	 * @return void
	 */
    public function setupRoutes(Router $router)
    {
		$router->group(['namespace' => 'Larapen\Settings\app\Http\Controllers'], function ($router) {
			// Admin Interface Routes
			Route::group(['prefix'   => config('backpack.base.route_prefix', 'admin'),
				'middleware' => ['web', 'admin'], ], function () {
				// Settings
				//Route::resource('setting', 'SettingCrudController');
				CRUD::resource('setting', 'SettingCrudController');
			});
		});
    }
}
