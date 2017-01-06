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

use App\Larapen\Models\Category;
use Illuminate\Support\Facades\Request;
use Larapen\CRUD\app\Http\Controllers\CrudController;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Admin\CategoryRequest as StoreRequest;
use App\Http\Requests\Admin\CategoryRequest as UpdateRequest;

class CategoryController extends CrudController
{
    public function __construct()
    {
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\Category');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/category');
		$this->crud->setEntityNameStrings('category', 'categories');
		$this->crud->enableReorder('name', 2);
		$this->crud->enableDetailsRow();
		$this->crud->allowAccess(['reorder', 'details_row']);
		//$this->crud->enableAjaxTable();
		$this->crud->orderBy('id', 'DESC');

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
			'label' => "Category Name"
		]);
		$this->crud->addColumn([
			'name' => 'active',
			'label' => "Active",
			'type' => "model_function",
			'function_name' => 'getActiveHtml',
		]);

		// FIELDS
		if (Request::segment(3) == 'create') {
			$this->crud->addField([
				'name' => 'parent_id',
				'label' => 'Parent',
				'type' => 'select_from_array',
				'options' => $this->categories(),
                                'value' =>'',
				'allows_null' => false,
			]);
		}
		$this->crud->addField([
			'name' => 'name',
			'label' => 'Name',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter a name',
			],
		]);
		$this->crud->addField([
			'name' => 'slug',
			'label' => 'Slug (URL)',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Will be automatically generated from your name, if left empty.',
			],
			'hint' => 'Will be automatically generated from your name, if left empty.',
		]);
		$this->crud->addField([
			'name' => 'description',
			'label' => 'Description',
			'type' => 'textarea',
			'attributes' => [
				'placeholder' => 'Enter a description',
			],
		]);
		$this->crud->addField([
			'name' => 'picture',
			'label' => 'Picture',
			'type' => 'browse'
		]);
		$this->crud->addField([
			'name' => 'type',
			'label' => 'Type',
			'type' => 'enum',
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
    
    public function categories()
    {
        $currentId = 0;
        if (Request::segment(4) == 'edit' and is_numeric(Request::segment(3))) {
            $currentId = Request::segment(3);
        }
        
        $entries = Category::where('translation_lang', config('app.locale'))->where('parent_id', 0)->orderBy('lft')->get();
        if (is_null($entries)) {
            return [];
        }
        
        $tab = [];
        $tab[0] = 'Root';
        foreach ($entries as $entry) {
            if ($entry->id != $currentId) {
                $tab[$entry->translation_of] = '- ' . $entry->name;
            }
        }
        
        return $tab;
    }
}
