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
use Backpack\CRUD\app\Http\Requests\CrudRequest as StoreRequest;
use Backpack\CRUD\app\Http\Requests\CrudRequest as UpdateRequest;

class AdvertisingController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\Advertising');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/advertising');
		$this->crud->setEntityNameStrings('advertising', 'advertisings');
		$this->crud->denyAccess(['create', 'delete']);

		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		$this->crud->addColumn([
			'name' => 'id',
			'label' => "ID"
		]);
		$this->crud->addColumn([
			'name' => 'slug',
			'label' => "Slug"
		]);
		$this->crud->addColumn([
			'name' => 'provider_name',
			'label' => "Provider Name"
		]);
		$this->crud->addColumn([
			'name' => 'active',
			'label' => "Active",
			'type' => "model_function",
			'function_name' => 'getActiveHtml',
		]);

		// FIELDS
		$this->crud->addField([
			'name' => 'provider_name',
			'label' => 'Provider Name',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter a Name',
			],
		]);
		$this->crud->addField([
			'name' => 'tracking_code_large',
			'label' => "Tracking Code (Large)",
			'type' => 'textarea',
			'attributes' => [
				'placeholder' => 'Enter the Code',
			],
		]);
		$this->crud->addField([
			'name' => 'tracking_code_medium',
			'label' => "Tracking Code (Tablet)",
			'type' => 'textarea',
			'attributes' => [
				'placeholder' => 'Enter the Code',
			],
		]);
		$this->crud->addField([
			'name' => 'tracking_code_small',
			'label' => "Tracking Code (Phone)",
			'type' => 'textarea',
			'attributes' => [
				'placeholder' => 'Enter the Code',
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
