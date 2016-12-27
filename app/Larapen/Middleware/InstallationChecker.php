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

namespace App\Larapen\Middleware;

use App\Larapen\Models\TimeZone;
use Closure;
use Larapen\Settings\app\Models\Setting;

class InstallationChecker
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->segment(1) == 'install') {
            // Check if installation is processing
            $InstallInProgress = false;
            if (
                !empty($request->session()->get('database_imported')) or
                !empty($request->session()->get('cron_jobs')) or
                !empty($request->session()->get('install_finish'))
            )
            {
                $InstallInProgress = true;
            }
            if ($this->alreadyInstalled($request) and $this->properlyInstalled() and !$InstallInProgress) {
                return redirect('/');
            }
        } else {
            if (!$this->alreadyInstalled($request) or !$this->properlyInstalled()) {
                $baseUrl = $this->getBaseUrl();
                return redirect($baseUrl . '/install');
            }
        }

        return $next($request);
    }

    /**
     * If application is already installed.
     *
     * @param $request
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function alreadyInstalled($request)
    {
        // Check if installation has just finished
        $installHasJustFinished = false;
        if (!empty($request->session()->get('install_finish'))) {
            $installHasJustFinished = true;
        }

        if ($installHasJustFinished === true) {
            // Write file
            file_put_contents(storage_path('installed'), '');

            $request->session()->forget('install_finish');
            $request->session()->flush();

            // Redirect to the homepage after installation
            return redirect('/');
        }

        return file_exists(storage_path('installed'));
    }

    /**
     * @return bool
     */
    public function properlyInstalled()
    {
        // Check Installation Setup
        $properly = true;
        try {
            // Check if .env file exists
            if (!$this->envFileExists()) {
                $properly = false;
            }

            // Check if all database tables exists
            $namespace = 'App\\Larapen\\Models\\';
            $modelsPath = app_path('Larapen/Models');
            $modelFiles = array_filter(\File::glob($modelsPath . '/' . '*'), 'is_file');

            if (count($modelFiles) > 0) {
                foreach ($modelFiles as $filePath) {
                    $filename = last(explode('/', $filePath));
                    $modelname = head(explode('.', $filename));

                    if (!str_contains($filename, '.php') or $modelname == 'BaseModel') {
                        continue;
                    }

                    eval('$model = new ' . $namespace . $modelname . '();');
                    if (!\Schema::hasTable($model->getTable())) {
                        $properly = false;
                    }
                }
            }

            // Check Settings table
            if (Setting::count() <= 0) {
                $properly = false;
            }
            // Check TimeZone table
            if (TimeZone::count() <= 0) {
                $properly = false;
            }
        } catch (\PDOException $e) {
            $properly = false;
        } catch (\Exception $e) {
            $properly = false;
        }

        return $properly;
    }

    /**
     * Check if /.env file exists
     *
     * @return bool
     */
    public function envFileExists()
    {
        return file_exists(base_path('.env'));
    }

    /**
     * @return mixed
     */
    private function getBaseUrl()
    {
        $currentUrl = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . strtok($_SERVER["REQUEST_URI"],'?');
        $tmp = explode('/install', $currentUrl);
        $baseUrl = current($tmp);
        $baseUrl = rtrim($baseUrl, '/');

        return $baseUrl;
    }
}
