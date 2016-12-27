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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\FrontController;
use App\Larapen\Models\Ad;
use App\Larapen\Models\AdType;
use App\Larapen\Models\Advertising;
use App\Larapen\Models\Category;
use App\Larapen\Models\City;
use App\Larapen\Models\Country;
use App\Larapen\Models\Language;
use App\Larapen\Models\Pack;
use App\Larapen\Models\Picture;
use App\Larapen\Models\SubAdmin1;
use App\Larapen\Models\SubAdmin2;
use App\Larapen\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as HttpRequest;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AjaxController extends FrontController
{
	/**
	 * @param $table
	 * @param $field
	 * @param HttpRequest $request
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function saveAjaxRequest($table, $field, HttpRequest $request)
    {
        $primaryKey = $request->input('primaryKey');
        $status = 0;
		$result = ['table' => $table, 'field' => $field, 'primaryKey' => $primaryKey, 'status' => $status];

		// Check parameters
		if (!Auth::check() or Auth::user()->is_admin != 1) {
			return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
		}
		if (!Schema::hasTable($table)) {
			return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
		}
		if(!Schema::hasColumn($table, $field)) {
			return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
		}
		$sql = 'SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "'.DB::getTablePrefix() . $table.'" AND COLUMN_NAME = "'.$field.'"';
		$info = DB::select(DB::raw($sql));
		if (empty($info)) {
			return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
		} else {
			if (isset($info[0]) and isset($info[0]->DATA_TYPE)) {
				if ($info[0]->DATA_TYPE != 'tinyint') {
					return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
				}
			} else {
				return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
			}
		}


		// Get table data
		switch ($table) {
			case 'ads':
				$item = Ad::find($primaryKey);
				break;
			case 'ad_type':
				$item = AdType::find($primaryKey);
				break;
			case 'advertising':
				$item = Advertising::find($primaryKey);
				break;
			case 'categories':
				$item = Category::find($primaryKey);
				break;
			case 'cities':
				$item = City::find($primaryKey);
				break;
			case 'countries':
				$item = Country::find($primaryKey);
				break;
			case 'languages':
				$item = Language::find($primaryKey);
				break;
			case 'packs':
				$item = Pack::find($primaryKey);
				break;
			case 'pictures':
				$item = Picture::find($primaryKey);
				break;
			case 'subadmin1':
				$item = SubAdmin1::find($primaryKey);
				break;
			case 'subadmin2':
				$item = SubAdmin2::find($primaryKey);
				break;
			case 'users':
				$item = User::find($primaryKey);
				break;
		}

		// Check item
		if (empty($item)) {
			return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
		}

		// UPDATE - the tinyint field

		// Geonames country data installation
		if ($table == 'countries' and $field == 'active') {
			if (strtolower(config('settings.app_default_country')) != strtolower($item->code)) {
				$resImport = false;
				if ($item->{$field} == 0) {
					$resImport = $this->importGeonamesSql($item->code);
				} else {
					$resImport = $this->removeGeonamesDatabyCountryCode($item->code);
				}

				// Save data
				if ($resImport) {
					$item->{$field} = ($item->{$field} == 0) ? 1 : 0;
					$item->save();
				}

				$isDefaultCountry = 0;
			} else {
				$isDefaultCountry = 1;
				$resImport = true;
			}
		}
		else
		{
			// Save data
			$item->{$field} = ($item->{$field} == 0) ? 1 : 0;
			$item->save();
		}


		// JS data
		$result = ['table' => $table, 'field' => $field, 'primaryKey' => $primaryKey, 'status' => 1, 'fieldValue' => $item->{$field}];

		if (isset($isDefaultCountry)) {
			$result['isDefaultCountry'] = $isDefaultCountry;
		}
		if (isset($resImport)) {
			$result['resImport'] = $resImport;
		}

        
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }



	private function importGeonamesSql($countryCode)
	{
		// Remove all country data
		$this->removeGeonamesDatabyCountryCode($countryCode);

		// Default country SQL file
		$filename = 'database/geonames/countries/' . strtolower($countryCode) .'.sql';
		$rawFilePath = storage_path($filename);
		$filePath = storage_path('app/'.$filename);

		// Check if rawFilePath exists
		if (!file_exists($rawFilePath)) {
			return false;
		}

		// Read and replace the database tables prefix
		$file = fopen($rawFilePath, 'r') or die('Unable to open file!');
		$sql = fread($file, filesize($rawFilePath));
		fclose($file);
		$sql = str_replace('<<prefix>>', DB::getTablePrefix(), $sql);


		// Create a new SQL file
		if (file_exists($filePath)) {
			unlink($filePath);
		}
		$file = fopen($filePath, 'w') or die('Unable to open file!');
		fwrite($file, $sql);
		fclose($file);


		try {
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

				// Add this line to the current segment
				$tmpline .= $line;
				// If it has a semicolon at the end, it's the end of the query
				if (substr(trim($line), -1, 1) == ';') {
					// Perform the query
					DB::unprepared($tmpline);
					// Reset temp variable to empty
					$tmpline = '';
				}
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Error when importing required data : '. $e->getMessage();
			echo '<pre>'; print_r($msg); echo '</pre>'; exit();
		}

		// Delete the SQL file
		if (file_exists($filePath)) {
			unlink($filePath);
		}

		return true;
	}

	/**
	 * @param $countryCode
	 * @return bool
	 */
	private function removeGeonamesDatabyCountryCode($countryCode)
	{
		$deletedRows = SubAdmin1::where('code', 'LIKE', $countryCode . '.%')->delete();
		$deletedRows = SubAdmin2::where('code', 'LIKE', $countryCode . '.%')->delete();
		$deletedRows = City::where('country_code', 'LIKE', $countryCode)->delete();

		return true;
	}
}
