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

use App\Larapen\Helpers\Ip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use PulkitJalan\GeoIP\Facades\GeoIP;

class InstallController extends Controller
{
    public static $cookie_expire = 3600;
    public $baseUrl;
    public $installUrl;
	public $item_id = '16458425';

    /**
     * InstallController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        // From Laravel 5.3.4 or above
        $this->middleware(function ($request, $next) {
            $this->commonQueries($request);
            return $next($request);
        });

        // Create SQL destination path if not exists
        if (!\File::exists(storage_path('app/database/geonames/countries'))) {
            \File::makeDirectory(storage_path('app/database/geonames/countries'), 0755, true);
        }

        // Base URL
        $this->baseUrl = $this->getBaseUrl();
        view()->share('baseUrl', $this->baseUrl);
        config(['app.url' => $this->baseUrl]);

        // Installation URL
        $this->installUrl = $this->baseUrl . '/install';
        view()->share('installUrl', $this->installUrl);
    }

    /**
     * Common Queries
     * @param Request $request
     */
    public function commonQueries(Request $request)
    {
        // Delete all front&back office sessions
        $request->session()->forget('country_code');
        $request->session()->forget('time_zone');
        $request->session()->forget('language_code');

        // Get country code by the user IP address
        $ipCountryCode = $this->getCountryCodeFromIPAddr();
    }

    /**
     * Check for current step
     * @param $request
     * @param null $liveData
     * @return int
     */
    public function step($request, $liveData = null)
    {
        $step = 0;

        $data = $request->session()->get('compatibilities');
        if (isset($data)) {
            $step = 1;
        } else {
            return $step;
        }

        $data = $request->session()->get('site_info');
        if (isset($data)) {
            $step = 3;
        } else {
            return $step;
        }

        $data = $request->session()->get('database');
        if (isset($data)) {
            $step = 4;
        } else {
            return $step;
        }

        $data = $request->session()->get('database_imported');
        if (isset($data)) {
            $step = 5;
        } else {
            return $step;
        }

        $data = $request->session()->get('cron_jobs');
        if (isset($data)) {
            $step = 6;
        } else {
            return $step;
        }

        return $step;
    }

	/**
	 * STEP 0 - Starting installation
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
    public function starting(Request $request)
    {
		$exitCode = \Artisan::call('cache:clear');
        sleep(1);
        $exitCode = \Artisan::call('config:cache');
        $exitCode = \Artisan::call('config:clear');

        return redirect($this->installUrl . '/system_compatibility');
    }

	/**
	 * STEP 1 - Check System Compatibility
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
    public function systemCompatibility(Request $request)
    {
        // Begin check
        $request->session()->forget('compatibilities');

        // Check compatibilities
        $compatibilities = $this->checkSystemCompatibility();
        $result = true;
        foreach ($compatibilities as $compatibility) {
            if (!$compatibility['check']) {
                $result = false;
            }
        }

        // Retry if something not work yet
        try {
            if ($result) {
                $request->session()->set('compatibilities', $compatibilities);
            }

            return view('install.compatibilities', [
                'compatibilities' => $compatibilities,
                'result' => $result,
                'step' => $this->step($request),
                'current' => 1,
            ]);
        } catch (\Exception $e) {
            \Artisan::call('cache:clear');
            sleep(5);
            return redirect($this->installUrl . '/system_compatibility');
        }
    }

	/**
	 * STEP 2 - Set Site Info
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
    public function siteInfo(Request $request)
    {
        if ($this->step($request) < 1) {
            return redirect($this->installUrl . '/system_compatibility');
        }

        // make sure session is working
        $rules = [
            'site_name' => 'required',
            'site_slogan' => 'required',
            'name' => 'required',
            'purchase_code' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'default_country' => 'required',
        ];
        $smtp_rules = [
            'smtp_hostname' => 'required',
            'smtp_port' => 'required',
            'smtp_username' => 'required',
            'smtp_password' => 'required',
            'smtp_encryption' => 'required',
        ];
		$mailgun_rules = [
			'mailgun_domain' => 'required',
			'mailgun_secret' => 'required',
		];
		$mandrill_rules = [
			'mandrill_secret' => 'required',
		];
		$ses_rules = [
			'ses_key' => 'required',
			'ses_secret' => 'required',
			'ses_region' => 'required',
		];

        // validate and save posted data
        if ($request->isMethod('post'))
        {
            $request->session()->forget('site_info');

            // Check purchase code
			$messages = [];
            $purchase_code_data = $this->purchaseCodeChecker($request);
            if ($purchase_code_data->valid == false) {
                $rules['purchase_code_valid'] = 'required';
				if ($purchase_code_data->message != '') {
					$messages = ['purchase_code_valid.required' => 'The :attribute field is required. - ERROR: <strong>'.$purchase_code_data->message.'</strong>'];
				}
            }

            if ($request->mail_driver == 'smtp') {
                $rules = array_merge($rules, $smtp_rules);
            }
			if ($request->mail_driver == 'mailgun') {
				$rules = array_merge($rules, $mailgun_rules);
			}
			if ($request->mail_driver == 'mandrill') {
				$rules = array_merge($rules, $mandrill_rules);
			}
			if ($request->mail_driver == 'ses') {
				$rules = array_merge($rules, $ses_rules);
			}

			if (!empty($messages)) {
				$this->validate($request, $rules, $messages);
			} else {
				$this->validate($request, $rules);
			}

            // Check SMTP connection
            if ($request->mail_driver == 'smtp') {
                $rules = [];
                $messages = [];
                try {
                    $transport = \Swift_SmtpTransport::newInstance($request->smtp_hostname, $request->smtp_port, $request->smtp_encryption);
                    $transport->setUsername($request->smtp_username);
                    $transport->setPassword($request->smtp_password);
                    $mailer = \Swift_Mailer::newInstance($transport);
                    $mailer->getTransport()->start();
                } catch (\Swift_TransportException $e) {
                    $rules['smtp_valid'] = 'required';
                    if ($e->getMessage() != '') {
                        $messages = ['smtp_valid.required' => 'The :attribute field is required. - ERROR: <strong>'.$e->getMessage().'</strong>'];
                    }
                } catch (\Exception $e) {
                    $rules['smtp_valid'] = 'required';
                    if ($e->getMessage() != '') {
                        $messages = ['smtp_valid.required' => 'The :attribute field is required. - ERROR: <strong>'.$e->getMessage().'</strong>'];
                    }
                }
                if (!empty($messages)) {
                    $this->validate($request, $rules, $messages);
                } else {
                    $this->validate($request, $rules);
                }
            }

            // Save data in session
            $site_info = $request->all();
            $request->session()->set('site_info', $site_info);

            return redirect($this->installUrl . '/database');
        }

        $site_info = $request->session()->get('site_info');
        if (!empty($request->old())) {
            $site_info = $request->old();
        }

        return view('install.site_info', [
            'site_info' => $site_info,
            'rules' => $rules,
            'smtp_rules' => $smtp_rules,
			'mailgun_rules' => $mailgun_rules,
			'mandrill_rules' => $mandrill_rules,
			'ses_rules' => $ses_rules,
            'step' => $this->step($request),
            'current' => 2,
        ]);
    }

	/**
	 * STEP 3 - Database configuration
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
    public function database(Request $request)
    {
        if ($this->step($request) < 2) {
            return redirect($this->installUrl . '/site_info');
        }

        // Check required fields
        $rules = array(
            'hostname' => 'required',
            'port' => 'required',
            'username' => 'required',
            'password' => 'required',
            'database_name' => 'required',
        );

        // Validate and save posted data
        if ($request->isMethod('post'))
        {
            $request->session()->forget('database');

            $this->validate($request, $rules);

            // Check mysql connection
            $messages = [];
            try {
                $port = $request->port;
                $port = (int) $port;
                $conn = new \mysqli($request->hostname, $request->username, $request->password, $request->database_name, $port);
            } catch (\Exception $e) {
                $rules['mysql_connection'] = 'required';
                if ($e->getMessage() != '') {
                    $messages = ['mysql_connection.required' => 'The :attribute field is required. - ERROR: <strong>'.$e->getMessage().'</strong>'];
                }
            }

            if (!empty($messages)) {
                $this->validate($request, $rules, $messages);
            } else {
                $this->validate($request, $rules);
            }

            // Get database info and Save it in session
            $database = $request->all();
            $request->session()->set('database', $database);

            // Write config file
            $this->writeEnv($request);

            // Return to Import Database page
            return redirect($this->installUrl . '/database_import');
        }

        $database = $request->session()->get('database');
        if (!empty($request->old())) {
            $database = $request->old();
        }

        return view('install.database', [
            'database' => $database,
            'rules' => $rules,
            'step' => $this->step($request),
            'current' => 3,
        ]);
    }

	/**
	 * STEP 4 - Import Database
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
    public function databaseImport(Request $request)
    {
        if ($this->step($request) < 3) {
            return redirect($this->installUrl . '/database');
        }

        // Get database connexion info & site info
        $database = $request->session()->get('database');
        $site_info = $request->session()->get('site_info');

        if ($request->action == 'import') {
            $request->session()->forget('database_imported');

			// Get MySQLi resource
			$mysqli = $this->getMySQLiResource($database);

            // Check if database is not empty
            $rules = [];
            $prefix_check = empty($database['tables_prefix']) ? '' : "  AND table_name LIKE '".$database['tables_prefix']."%'";
			$sql = "SELECT COUNT(DISTINCT `table_name`) as count 
					FROM `information_schema`.`columns` 
					WHERE `table_schema` = '".$database['database_name']."'".$prefix_check;
            $result = $mysqli->query($sql);
            $result = $result->fetch_object();
            if ($result->count > 0) {
                $rules['database_not_empty'] = 'required';
            }
            $this->validate($request, $rules);

            // Import database with prefix
            $this->importDatabase($database, $site_info);

			// The database is now imported !
            $request->session()->set('database_imported', true);

            $request->session()->flash('alert-success', trans('messages.install.database_import.success'));

            return redirect($this->installUrl . '/cron_jobs');
        }

        return view('install.database_import', [
            'database' => $database,
            'step' => $this->step($request),
            'current' => 3,
        ]);
    }

	/**
	 * STEP 5 - Set Cron Jobs
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
    public function cronJobs(Request $request)
    {
        if ($this->step($request) < 5) {
            return redirect($this->installUrl . '/database');
        }

        $request->session()->set('cron_jobs', true);

        return view('install.cron_jobs', [
            'step' => $this->step($request),
            'current' => 5,
        ]);
    }

	/**
	 * STEP 6 - Finish
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
    public function finish(Request $request)
    {
        if ($this->step($request) < 6) {
            return redirect($this->installUrl . '/database');
        }

        $request->session()->set('install_finish', true);

        // Delete all front&back office cookies
        if (isset($_COOKIE['ip_country_code'])) {
            unset($_COOKIE['ip_country_code']);
        }

        return view('install.finish', [
            'step' => $this->step($request),
            'current' => 6,
        ]);
    }












	/**
	 * Check for requirement when install app
	 * @return array
	 */
    private function checkSystemCompatibility()
    {
        // Fix unknown public folder (For 'public/uploads' folder)
        $user_public_folder = last(explode(DIRECTORY_SEPARATOR, public_path()));

        return [
            [
                'type' => 'requirement',
                'name' => 'PHP version',
                'check' => version_compare(PHP_VERSION, '5.6.4', '>='),
                'note' => 'PHP 5.6.4 or higher is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'OpenSSL Extension',
                'check' => extension_loaded('openssl'),
                'note' => 'OpenSSL PHP Extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'Mbstring PHP Extension',
                'check' => extension_loaded('mbstring'),
                'note' => 'Mbstring PHP Extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'PDO PHP extension',
                'check' => extension_loaded('pdo'),
                'note' => 'PDO PHP extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'Tokenizer PHP Extension',
                'check' => extension_loaded('tokenizer'),
                'note' => 'Tokenizer PHP Extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'XML PHP Extension',
                'check' => extension_loaded('xml'),
                'note' => 'XML PHP Extension is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP Zip Archive',
                'check' => class_exists('ZipArchive', false),
                'note' => 'PHP Zip Archive is required.',
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP GD Library',
                'check' => (extension_loaded('gd') && function_exists('gd_info')),
                'note' => 'PHP GD Library is required.',
            ],
            [
                'type' => 'permission',
                'name' => 'bootstrap/cache/',
                'check' => file_exists(base_path('/bootstrap/cache')) &&
                    is_dir(base_path('/bootstrap/cache')) &&
                    (is_writable(base_path('/bootstrap/cache'))) &&
                    getPerms(base_path('/bootstrap/cache')) >= 775,
                'note' => 'The directory must be writable by the web server (0775).',
            ],
            [
                'type' => 'permission',
                'name' => $user_public_folder . '/uploads/',
                'check' => file_exists(base_path('/' . $user_public_folder . '/uploads')) &&
                    is_dir(base_path('/' . $user_public_folder . '/uploads')) &&
                    (is_writable(base_path('/' . $user_public_folder . '/uploads'))) &&
                    getPerms(base_path('/' . $user_public_folder . '/uploads')) >= 775,
                'note' => 'The directory must be writable by the web server (0775).',
            ],
            [
                'type' => 'permission',
                'name' => 'storage/app/',
                'check' => file_exists(base_path('/storage/app')) &&
                    is_dir(base_path('/storage/app')) &&
                    (is_writable(base_path('/storage/app'))) &&
                    getPerms(base_path('/storage/app')) >= 775,
                'note' => 'The directory must be writable by the web server (0775).',
            ],
            [
                'type' => 'permission',
                'name' => 'storage/framework/',
                'check' => file_exists(base_path('/storage/framework')) &&
					is_dir(base_path('/storage/framework')) &&
					(is_writable(base_path('/storage/framework'))) &&
                    getPerms(base_path('/storage/framework')) >= 775,
                'note' => 'The directory must be writable by the web server (0775).',
            ],
            [
                'type' => 'permission',
                'name' => 'storage/logs/',
                'check' => file_exists(base_path('/storage/logs')) &&
					is_dir(base_path('/storage/logs')) &&
					(is_writable(base_path('/storage/logs'))) &&
                    getPerms(base_path('/storage/logs')) >= 775,
                'note' => 'The directory must be writable by the web server (0775).',
            ],
        ];
    }

	/**
	 * @return string
	 */
    public function checkServerVar()
    {
        $vars = array('HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 'PHP_SELF', 'HTTP_ACCEPT', 'HTTP_USER_AGENT');
        $missing = array();
        foreach ($vars as $var) {
            if (!isset($_SERVER[$var])) {
                $missing[] = $var;
            }
        }

        if (!empty($missing)) {
            return '$_SERVER does not have: '.implode(', ', $missing);
        }

        if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING'])) {
            return 'Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.';
        }

        if (!isset($_SERVER['PATH_INFO']) && strpos($_SERVER['PHP_SELF'], $_SERVER['SCRIPT_NAME']) !== 0) {
            return 'Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.';
        }

        return '';
    }

	/**
	 * Write configuration values to file
     * @param $request
     */
    private function writeEnv($request)
    {
        // Set app key
		$app_key = '';
        $env_file_path = base_path('.env');
		if (file_exists($env_file_path)) {
            // Read app key from file
            $app_config = $this->getFile($env_file_path);
            preg_match('/APP_KEY=(.*)\n/', $app_config, $tmp);
            $app_key = trim($tmp[1]);
		}
        $app_key = (strlen(trim($app_key)) == 32) ? $app_key : config('app.key');

		// Get app host
		$app_host = getHostByUrl($this->baseUrl);

        // Get database info
        $database = $request->session()->get('database');

		// Generate .env file string
        $config_string = 'APP_ENV=production
APP_DEBUG=true
APP_KEY='.$app_key.'
APP_URL='.$this->baseUrl.'

DB_HOST='.(isset($database['hostname']) ? $database['hostname'] : '').'
DB_DATABASE='.(isset($database['database_name']) ? $database['database_name'] : '').'
DB_USERNAME='.(isset($database['username']) ? $database['username'] : '').'
DB_PASSWORD='.(isset($database['password']) ? $database['password'] : '').'
DB_PORT='.(isset($database['port']) ? $database['port'] : '').'
DB_TABLES_PREFIX='.(isset($database['tables_prefix']) ? $database['tables_prefix'] : '').'
DB_CHARSET=utf8
DB_COLLATION=utf8_unicode_ci
DB_DUMP_COMMAND_PATH=

IMAGE_DRIVER=gd

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

APP_LOG=daily
';
        // Save the new .env file
        $this->writeFile($env_file_path, $config_string);

		// Reload .env
		$exitCode = Artisan::call('config:cache');
		$exitCode = Artisan::call('config:clear');
    }

	/**
	 * Import Database - Migration & Seed
	 * @param $database
	 * @param $site_info
	 * @return bool
	 */
    private function importDatabase($database, $site_info)
    {
		// Run migrate
		//$exitCode = \Artisan::call('migrate');

    	// Import database schema
		$this->importSchemaSql($database);
        //sleep(1);

		// Run seed
		//$exitCode = \Artisan::call('db:seed');

		// Import required data
		$this->importRequiredDataSql($database);
        //sleep(1);

		// Import Geonames Default country database
		$this->importGeonamesSql($database, $site_info);
        //sleep(1);

        // Update database with customer info
        $this->updateDatabase($database, $site_info);

		return true;
    }

	/**
	 * Import Database Schema
     * @param $database
     * @return bool
     */
    private function importSchemaSql($database)
	{
		// Default country SQL file
        $filename = 'database/schema.sql';
        $rawFilePath = storage_path($filename);
        $filePath = storage_path('app/'.$filename);
		$this->setSqlWithDbPrefix($rawFilePath, $filePath, $database);

        return $this->importSql($database, $filePath);
	}

	/**
	 * Import required data
     * @param $database
     * @return bool
     */
	private function importRequiredDataSql($database)
	{
		// Default country SQL file
        $filename = 'database/data.sql';
		$rawFilePath = storage_path($filename);
		$filePath = storage_path('app/'.$filename);
		$this->setSqlWithDbPrefix($rawFilePath, $filePath, $database);

        return $this->importSql($database, $filePath);
	}

	/**
	 * Import Geonames Default country database
	 * @param $database
	 * @param $site_info
	 * @return bool
	 */
    private function importGeonamesSql($database, $site_info)
    {
		if (!isset($site_info['default_country'])) return false;

		// Default country SQL file
        $filename = 'database/geonames/countries/' . strtolower($site_info['default_country']) .'.sql';
        $rawFilePath = storage_path($filename);
        $filePath = storage_path('app/'.$filename);

		$this->setSqlWithDbPrefix($rawFilePath, $filePath, $database);

        return $this->importSql($database, $filePath);
    }

    /**
     * @param $database
     * @param $filePath
     * @return bool
     */
    private function importSql($database, $filePath)
    {
        // Get MySQLi resource
        $mysqli = $this->getMySQLiResource($database, true);

        try {
            $errorDetect = false;

            // Temporary variable, used to store current query
            $tmpline = '';
            // Read in entire file
            $lines = file($filePath);
            // Loop through each line
            foreach ($lines as $line) {
                // Skip it if it's a comment
                if (substr($line, 0, 2) == '--' || trim($line) == '') {
                    continue;
                }
                if (substr($line, 0, 2) == '/*') {
                    //continue;
                }

                // Add this line to the current segment
                $tmpline .= $line;
                // If it has a semicolon at the end, it's the end of the query
                if (substr(trim($line), -1, 1) == ';') {
                    // Perform the query
                    if (!$mysqli->query($tmpline)) {
                        echo  "<pre>Error performing query '<strong>".$tmpline."</strong>' : ".$mysqli->error." - Code: ".$mysqli->errno."</pre><br />";
                        $errorDetect = true;
                    }
                    // Reset temp variable to empty
                    $tmpline = '';
                }
            }
            // Check if error is detected
            if ($errorDetect) {
                dd('ERROR');
            }
        }
        catch (\Exception $e)
        {
            $msg = 'Error when importing required data : '. $e->getMessage();
            echo '<pre>'; print_r($msg); echo '</pre>'; exit();
        }

        // Close MySQL connexion
        $mysqli->close();

        // Delete the SQL file
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return true;
    }

    /**
     * @param $database
     * @param $site_info
     */
    private function updateDatabase($database, $site_info)
    {
        // Get MySQLi resource
        $mysqli = $this->getMySQLiResource($database, true);

        // Default date
        $date = \Carbon\Carbon::now();


        // USERS - Insert default superuser
        $mysqli->query('DELETE FROM `'.$database['tables_prefix'].'users` WHERE 1');
        $sql = 'INSERT INTO `'.$database['tables_prefix']."users`(`id`, `country_code`, `user_type_id`, `gender_id`, `name`, `about`, `email`, `password`, `is_admin`, `active`) VALUES (1, '" .$site_info['default_country']."', 1, 1, '".$site_info['name']."', 'Administrator', '".$site_info['email']."', '".bcrypt($site_info['password'])."', 1, 1);";
        $aaa = $mysqli->query($sql);


		// COUNTRIES - Activate default country
		$sql = 'UPDATE `'.$database['tables_prefix'].'countries` SET `active`=1 WHERE `code`="'.$site_info['default_country'].'"';
		$mysqli->query($sql);


        // SETTINGS - Update settings
        $sql = 'UPDATE `'.$database['tables_prefix'].'settings` SET `value`="'.$site_info['purchase_code'].'" WHERE `key`="purchase_code"';
        $mysqli->query($sql);
        $sql = 'UPDATE `'.$database['tables_prefix'].'settings` SET `value`="'.$site_info['site_name'].'" WHERE `key`="app_name"';
        $mysqli->query($sql);
        $sql = 'UPDATE `'.$database['tables_prefix'].'settings` SET `value`="'.$site_info['site_slogan'].'" WHERE `key`="app_slogan"';
        $mysqli->query($sql);
        $sql = 'UPDATE `'.$database['tables_prefix'].'settings` SET `value`="'.$site_info['email'].'" WHERE `key`="app_email"';
        $mysqli->query($sql);
        $sql = 'UPDATE `'.$database['tables_prefix'].'settings` SET `value`="'.$site_info['default_country'].'" WHERE `key`="app_default_country"';
        $mysqli->query($sql);
        $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['mail_driver'] . '" WHERE `key`="mail_driver"';
        $mysqli->query($sql);
        if (in_array($site_info['mail_driver'], ['smtp', 'mailgun', 'mandrill', 'ses'])) {
            $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['smtp_hostname'] . '" WHERE `key`="mail_host"';
            $mysqli->query($sql);
            $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['smtp_port'] . '" WHERE `key`="mail_port"';
            $mysqli->query($sql);
            $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['smtp_encryption'] . '" WHERE `key`="mail_encryption"';
            $mysqli->query($sql);
            $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['smtp_username'] . '" WHERE `key`="mail_username"';
            $mysqli->query($sql);
            $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['smtp_password'] . '" WHERE `key`="mail_password"';
            $mysqli->query($sql);
        }
        if ($site_info['mail_driver']=='mailgun') {
            $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['mailgun_domain'] . '" WHERE `key`="mailgun_domain"';
            $mysqli->query($sql);
            $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['mailgun_secret'] . '" WHERE `key`="mailgun_secret"';
            $mysqli->query($sql);
        }
        if ($site_info['mail_driver']=='mandrill') {
            $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['mandrill_secret'] . '" WHERE `key`="mandrill_secret"';
            $mysqli->query($sql);
        }
        if ($site_info['mail_driver']=='ses') {
            $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['ses_key'] . '" WHERE `key`="ses_key"';
            $mysqli->query($sql);
            $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['ses_secret'] . '" WHERE `key`="ses_secret"';
            $mysqli->query($sql);
            $sql = 'UPDATE `' . $database['tables_prefix'] . 'settings` SET `value`="' . $site_info['ses_region'] . '" WHERE `key`="ses_region"';
            $mysqli->query($sql);
        }
    }

	/**
	 * @param $rawFilePath
	 * @param $filePath
	 * @param $database
	 * @return mixed|string
	 */
	private function setSqlWithDbPrefix($rawFilePath, $filePath, $database)
	{
		if (!file_exists($rawFilePath)) {
			return '';
		}

		// Read and replace prefix
        $sql = $this->getFile($rawFilePath);
		$sql = str_replace('<<prefix>>', $database['tables_prefix'], $sql);

		// Write file
        $this->writeFile($filePath, $sql);

		return $sql;
	}

	/**
	 * Get MySQLi resource
     * @param $database
     * @param bool $utf8
     * @return \mysqli
     */
	private function getMySQLiResource($database, $utf8 = false)
	{
		// MySQL parameters
		$mysql_host = isset($database['hostname']) ? $database['hostname'] : '';
		$mysql_port = isset($database['port']) ? $database['port'] : '';
		$mysql_username = isset($database['username']) ? $database['username'] : '';
		$mysql_password = isset($database['password']) ? $database['password'] : '';
		$mysql_database = isset($database['database_name']) ? $database['database_name'] : '';

		// Connect to MySQL server
		$mysqli = new \mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_database, $mysql_port);

		// Check connection
		if ($mysqli->connect_errno) {
			printf("Connect failed: %s\n", $mysqli->connect_error);
			exit();
		}

		// Change character set to utf8
		if ($utf8 and !$mysqli->set_charset('utf8')) {
			printf("Error loading character set utf8: %s\n", $mysqli->error);
			exit();
		}

		return $mysqli;
	}

    /**
     * @param $filename
     * @return string
     */
	private function getFile($filename)
    {
        $file = fopen($filename, 'r') or die('Unable to open file!');
        $buffer = fread($file, filesize($filename));
        fclose($file);

        return $buffer;
    }

    /**
     * @param $filename
     * @param $buffer
     */
    private function writeFile($filename, $buffer)
    {
        // Delete the file before writing
        if (file_exists($filename)) {
            unlink($filename);
        }

        // Write the new file
        $file = fopen($filename, 'w') or die('Unable to open file!');
        fwrite($file, $buffer);
        fclose($file);
    }

	/**
	 * @return string
	 */
	private function checkCaptchaSupport()
	{
		if (function_exists('getimagesize')) {
			return '';
		}

		if (extension_loaded('imagick')) {
			$imagick = new Imagick();
			$imagickFormats = $imagick->queryFormats('PNG');
		}

		if (extension_loaded('gd')) {
			$gdInfo = gd_info();
		}

		if (isset($imagickFormats) && in_array('PNG', $imagickFormats)) {
			return '';
		} elseif (isset($gdInfo)) {
			if ($gdInfo['FreeType Support']) {
				return '';
			}

			return 'GD installed,<br />FreeType support not installed';
		}

		return 'GD or ImageMagick not installed';
	}

    /**
     * @return bool|string
     */
    private static function getCountryCodeFromIPAddr()
    {
        if (isset($_COOKIE['ip_country_code'])) {
            $country_code = $_COOKIE['ip_country_code'];
        } else {
            // Localize the user's country
            try {
                $ip_addr = Ip::get();

                GeoIP::setIp($ip_addr);
                $country_code = GeoIP::getCountryCode();

                if (!is_string($country_code) or strlen($country_code) != 2) {
                    return null;
                }
            } catch (\Exception $e) {
                return null;
            }

            // Set data in cookie
            if (isset($_COOKIE['ip_country_code'])) {
                unset($_COOKIE['ip_country_code']);
            }
            setcookie('ip_country_code', $country_code);
        }
        return $country_code;
    }

    /**
     * @param Request $request
     * @return mixed|string
     */
    private function purchaseCodeChecker(Request $request)
    {
        try {
            $apiUrl = config('larapen.core.purchase_code_checker_url') . $request->purchase_code . '&item_id=' . $this->item_id;
            $data = file_get_contents($apiUrl);
        } catch (\Exception $e) {
            $data = json_encode(['valid' => false, 'message' => 'Invalid purchase code. Unknown error.']);
        }

        // Format object data
        $data = json_decode($data);

        return $data;
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
