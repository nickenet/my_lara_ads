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
use App\Http\Requests\Admin\TimeZoneRequest as StoreRequest;
use App\Http\Requests\Admin\TimeZoneRequest as UpdateRequest;

class TimeZoneController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\TimeZone');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/time_zone');
		$this->crud->setEntityNameStrings('time zone', 'time zones');

		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		$this->crud->addColumn([
			'name' => 'country_code',
			'label' => "Country Code"
		]);
		$this->crud->addColumn([
			'name' => 'time_zone_id',
			'label' => "Time Zone"
		]);
		$this->crud->addColumn([
			'name' => 'gmt',
			'label' => "GMT"
		]);
		$this->crud->addColumn([
			'name' => 'dst',
			'label' => "DST"
		]);
		$this->crud->addColumn([
			'name' => 'raw',
			'label' => "RAW"
		]);
		$this->crud->addColumn([
			'name' => 'active',
			'label' => "Active",
			'type' => "model_function",
			'function_name' => 'getActiveHtml',
		]);

		// FIELDS
		$this->crud->addField([
			'label' => "Country Code",
			'type' => 'select2',
			'name' => 'country_code',
			'attribute' => 'asciiname',
			'model' => "App\Larapen\Models\Country",
		]);
		$this->crud->addField([
			'name' => 'time_zone_id',
			'label' => 'Time Zone',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the TimeZone (ISO)',
			],
			'hint' => 'Please check the TimeZoneId code format here: <a href="http://download.geonames.org/export/dump/timeZones.txt" target="_blank">http://download.geonames.org/export/dump/timeZones.txt</a>',
		]);
		$this->crud->addField([
			'name' => 'gmt',
			'label' => "GMT",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the GMT value (ISO)',
			],
		]);
		$this->crud->addField([
			'name' => 'dst',
			'label' => "DST",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the DST value (ISO)',
			],
		]);
		$this->crud->addField([
			'name' => 'raw',
			'label' => "GMT",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the RAW value (ISO)',
			],
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
