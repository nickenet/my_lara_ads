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

class BlacklistController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\Blacklist');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/blacklist');
		$this->crud->setEntityNameStrings('blacklist', 'blacklists');

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
			'name' => 'type',
			'label' => "Type"
		]);
		$this->crud->addColumn([
			'name' => 'entry',
			'label' => "Entry",
		]);

		// FIELDS
		$this->crud->addField([
			'name' => 'type',
			'label' => 'Type',
			'type' => 'enum',
		]);
		$this->crud->addField([
			'name' => 'entry',
			'label' => 'Entry',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter a value',
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
