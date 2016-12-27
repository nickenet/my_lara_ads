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

namespace Larapen\LangFileManager;

use Illuminate\Routing\Router;
use Larapen\LangFileManager\app\Services\LangFiles;

class LangFileManagerServiceProvider extends \Backpack\LangFileManager\LangFileManagerServiceProvider
{
	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function setupRoutes(Router $router)
	{
		$router->group(['namespace' => 'Larapen\LangFileManager\app\Http\Controllers'], function ($router) {
			require __DIR__.'/app/Http/routes.php';
		});
	}
    
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
		$this->registerLangFileManager();
		$this->setupRoutes($this->app->router);

		$this->app->singleton('langfile', function ($app) {
			return new LangFiles($app['config']['app']['locale']);
		});

		// use this if your package has a config file
		// config([
		//         'config/langfilemanager.php',
		// ]);
    }
    
    private function registerLangFileManager()
    {
		$this->app->bind('langfilemanager', function ($app) {
			return new LangFileManager($app);
		});
    }
}
