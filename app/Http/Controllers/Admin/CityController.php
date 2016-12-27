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

use Larapen\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Admin\CityRequest as StoreRequest;
use App\Http\Requests\Admin\CityRequest as UpdateRequest;

class CityController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\City');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/city');
		$this->crud->setEntityNameStrings('city', 'cities');
		$this->crud->enableAjaxTable();

		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		$this->crud->addColumn([
			'name' => 'country_code',
			'label' => "Country Code",
		]);
		$this->crud->addColumn([
			'name' => 'name',
			'label' => "Local Name"
		]);
		$this->crud->addColumn([
			'name' => 'asciiname',
			'label' => "Name"
		]);
		$this->crud->addColumn([
			'name' => 'subadmin1_code',
			'label' => "Admin1 Code"
		]);
		$this->crud->addColumn([
			'name' => 'subadmin2_code',
			'label' => "Admin2 Code"
		]);
		$this->crud->addColumn([
			'name' => 'active',
			'label' => "Active",
			'type' => "model_function",
			'function_name' => 'getActiveHtml',
		]);

		// FIELDS
		$this->crud->addField([
			'name' => 'country_code',
			'label' => 'Country Code',
			'type' => 'select2',
			'attribute' => 'asciiname',
			'model' => "App\Larapen\Models\Country",
			'attributes' => [
				'placeholder' => 'Enter the country code (ISO Code)',
			],
		]);
		$this->crud->addField([
			'name' => 'name',
			'label' => 'Local Name',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the country local name',
			],
		]);
		$this->crud->addField([
			'name' => 'asciiname',
			'label' => "Name",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the country name (In English)',
			],
		]);
		$this->crud->addField([
			'name' => 'latitude',
			'label' => "Latitude",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the latitude',
			],
			'hint' => 'In decimal degrees (wgs84)',
		]);
		$this->crud->addField([
			'name' => 'longitude',
			'label' => "Longitude",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the longitude',
			],
			'hint' => 'In decimal degrees (wgs84)',
		]);
		$this->crud->addField([
			'name' => 'subadmin1_code',
			'label' => "Admin1 Code",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the admin1 code (example: 08) without the country code',
			],
			'hint' => 'Code for the first administrative division. Please check the admin1 code format here: <a href="http://download.geonames.org/export/dump/admin1CodesASCII.txt" target="_blank">http://download.geonames.org/export/dump/admin1CodesASCII.txt</a>',
		]);
		$this->crud->addField([
			'name' => 'subadmin2_code',
			'label' => "Admin2 Code",
			'type' => 'text',
			'attributes' => [
				'placeholder' => '(Optional) - Enter the admin2 code (example: 5883638) without the admin1 code',
			],
			'hint' => 'Code for the second administrative division. Please check the admin2 code format here: <a href="http://download.geonames.org/export/dump/admin2Codes.txt" target="_blank">http://download.geonames.org/export/dump/admin2Codes.txt</a>',
		]);
		$this->crud->addField([
			'name' => 'population',
			'label' => "Population",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the population',
			],
		]);
		$this->crud->addField([
			'name' => 'time_zone',
			'label' => "Time Zone ID",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the time zone ID (example: Europe/Paris)',
			],
			'hint' => 'Please check the TimeZoneId code format here: <a href="http://download.geonames.org/export/dump/timeZones.txt" target="_blank">http://download.geonames.org/export/dump/timeZones.txt</a>',
		]);
		$this->crud->addField([
			'name' => 'active',
			'label' => "Active",
			'type' => 'checkbox'
		]);
	}
    
    public function store(StoreRequest $request)
    {
        return parent::storeCrud();
    }
    
    public function update(UpdateRequest $request)
    {
        return parent::updateCrud();
    }
}
