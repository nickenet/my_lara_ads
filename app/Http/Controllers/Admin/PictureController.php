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

class PictureController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\Picture');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/picture');
		$this->crud->setEntityNameStrings('picture', 'pictures');
		$this->crud->enableAjaxTable();

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
			'name' => 'filename',
			'label' => "Filename",
			'type' => "model_function",
			'function_name' => 'getFilenameHtml',
		]);
		$this->crud->addColumn([
			'name' => 'ad_id',
			'label' => "Ad",
			'type' => "model_function",
			'function_name' => 'getAdTitleHtml',
		]);
		$this->crud->addColumn([
			'name' => 'active',
			'label' => "Active",
			'type' => "model_function",
			'function_name' => 'getActiveHtml',
		]);

		// FIELDS
		$this->crud->addField([
			'name' => 'ad_id',
			'label' => 'Ad',
			'model' => "App\Larapen\Models\Ad",
			'entity' => 'ad',
			'attribute' => 'title',
			'type' => 'select2',
		]);
		$this->crud->addField([
			'name' => 'filename',
			'label' => 'Picture',
			'type' => 'browse'
		]);
		$this->crud->addField([
			'name' => 'active',
			'label' => "Active",
			'type' => 'checkbox'
		]);
	}
    
    public function store(StoreRequest $request)
    {
        $request = $this->fixFilePath($request);
        return parent::storeCrud($request);
    }
    
    public function update(UpdateRequest $request)
    {
        $request = $this->fixFilePath($request);
        return parent::updateCrud($request);
    }

    private function fixFilePath($request)
    {
        if ($request->has('filename')) {
            $filename = str_replace('uploads/pictures/', '', $request->get('filename'));
            $request->merge(array('filename' => $filename));
        }
        return $request;
    }
}
