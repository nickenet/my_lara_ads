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
use App\Http\Requests\Admin\CountryRequest as StoreRequest;
use App\Http\Requests\Admin\CountryRequest as UpdateRequest;

class CountryController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\Country');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/country');
		$this->crud->setEntityNameStrings('country', 'countries');
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
			'name' => 'tld',
			'label' => "Tld"
		]);
		$this->crud->addColumn([
			'name' => 'languages',
			'label' => "Languages"
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
			'name' => 'capital',
			'label' => "Capital (Optional)",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the country capital',
			],
		]);
		$this->crud->addField([
			'name' => 'continent_code',
			'label' => "Continent (Optional)",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the continent code (ISO: AF, AN, AS, EU, NA, OC, SA)',
			],
		]);
		$this->crud->addField([
			'name' => 'tld',
			'label' => "TLD (Optional)",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the country tld (E.g. .bj for Benin)',
			],
		]);
		$this->crud->addField([
			'label' => "Currency Code",
			'type' => 'select2',
			'name' => 'currency_code',
			'attribute' => 'name',
			'model' => "App\Larapen\Models\Currency",
		]);
		$this->crud->addField([
			'name' => 'phone',
			'label' => "Phone Ind. (Optional)",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the country phone ind. (E.g. +229 for Benin)',
			],
		]);
		$this->crud->addField([
			'name' => 'languages',
			'label' => "Languages",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the locale code (ISO) separate with comma',
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
