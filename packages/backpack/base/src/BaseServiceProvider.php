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

namespace Larapen\Base;

use Illuminate\Routing\Router;
use Route;

class BaseServiceProvider extends \Backpack\Base\BaseServiceProvider
{
    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        // register the 'admin' middleware
        $router->middleware('admin', app\Http\Middleware\Admin::class);

        $router->group(['namespace' => 'Larapen\Base\app\Http\Controllers'], function ($router) {
            Route::group(
                [
                    'middleware' => 'web',
                    'prefix'     => config('backpack.base.route_prefix', 'admin'),
                ],
                function () {
                    // if not otherwise configured, setup the auth routes
                    if (config('backpack.base.setup_auth_routes')) {
                        Route::auth();
                        Route::get('logout', 'Auth\LoginController@logout');
                    }

                    // if not otherwise configured, setup the dashboard routes
                    if (config('backpack.base.setup_dashboard_routes')) {
                        Route::get('dashboard', 'AdminController@dashboard');
                        Route::get('/', 'AdminController@redirect');
                    }
                });
        });
    }
}
