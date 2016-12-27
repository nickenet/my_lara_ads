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
use App\Http\Requests\Admin\PackRequest as StoreRequest;
use App\Http\Requests\Admin\PackRequest as UpdateRequest;

class PackController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\Pack');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/pack');
		$this->crud->setEntityNameStrings('pack', 'packs');
		$this->crud->enableReorder('name', 1);
		$this->crud->enableDetailsRow();
		$this->crud->allowAccess(['reorder', 'details_row']);
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
			'name' => 'name',
			'label' => "Name"
		]);
		$this->crud->addColumn([
			'name' => 'price',
			'label' => "Price"
		]);
		$this->crud->addColumn([
			'name' => 'currency_code',
			'label' => "Currency"
		]);
		$this->crud->addColumn([
			'name' => 'active',
			'label' => "Active",
			'type' => "model_function",
			'function_name' => 'getActiveHtml',
		]);

		// FIELDS
		$this->crud->addField([
			'name' => 'name',
			'label' => 'Name',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter a name',
			],
		]);
		$this->crud->addField([
			'name' => 'description',
			'label' => 'Description',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter a description',
			],
		]);
		$this->crud->addField([
			'name' => 'price',
			'label' => 'Price',
			'type' => 'text',
			'placeholder' => 'Enter a price'
		]);
		$this->crud->addField([
			'label' => "Currency",
			'name' => 'currency_code',
			'model' => "App\Larapen\Models\Currency",
			'entity' => 'currency',
			'attribute' => 'name',
			'type' => 'select2',
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
