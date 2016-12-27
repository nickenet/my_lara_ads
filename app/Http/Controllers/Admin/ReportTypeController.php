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
use App\Http\Requests\Admin\ReportTypeRequest as StoreRequest;
use App\Http\Requests\Admin\ReportTypeRequest as UpdateRequest;

class ReportTypeController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\ReportType');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/report_type');
		$this->crud->setEntityNameStrings('report type', 'report types');
		$this->crud->enableDetailsRow();
		$this->crud->allowAccess(['details_row']);

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

		// FIELDS
		$this->crud->addField([
			'name' => 'name',
			'label' => 'Name',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter a name',
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
