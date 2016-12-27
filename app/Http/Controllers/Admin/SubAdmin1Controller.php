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
use App\Http\Requests\Admin\SubAdmin1Request as StoreRequest;
use App\Http\Requests\Admin\SubAdmin1Request as UpdateRequest;

class SubAdmin1Controller extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\SubAdmin1');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/loc_admin1');
		$this->crud->setEntityNameStrings('admin 1', 'admins 1');
		$this->crud->enableAjaxTable();

		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		$this->crud->addColumn([
			'name' => 'code',
			'label' => "Code"
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
			'name' => 'active',
			'label' => "Active",
			'type' => "model_function",
			'function_name' => 'getActiveHtml',
		]);

		// FIELDS
		$this->crud->addField([
			'name' => 'code',
			'label' => 'Code',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Country code (example: CA) (dot) Admin1 code (example: 08) => Example: CA.08',
			],
			'hint' => 'Please check the admin1 code format here: <a href="http://download.geonames.org/export/dump/admin1CodesASCII.txt" target="_blank">http://download.geonames.org/export/dump/admin1CodesASCII.txt</a>',
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
