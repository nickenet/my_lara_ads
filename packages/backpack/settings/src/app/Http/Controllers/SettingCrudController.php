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

namespace Larapen\Settings\app\Http\Controllers;

use App\Http\Requests;
use Larapen\CRUD\app\Http\Controllers\CrudController;
use Backpack\Settings\app\Http\Requests\SettingRequest as StoreRequest;
use Backpack\Settings\app\Http\Requests\SettingRequest as UpdateRequest;

class SettingCrudController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		$this->crud->setModel("Larapen\Settings\app\Models\Setting");
		$this->crud->setEntityNameStrings('setting', 'settings');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin').'/setting');
		$this->crud->enableReorder('name', 2);
		$this->crud->allowAccess(['reorder']);
		$this->crud->denyAccess(['create', 'delete']);

		// COLUMNS
		$this->crud->addColumn([
			'name' => 'lft',
			'label' => "#"
		]);
		$this->crud->addColumn([
			'name' => 'name',
			'label' => "Name"
		]);
		$this->crud->addColumn([
			'name' => 'value',
			'label' => "Value",
			'type' => "model_function",
			'function_name' => 'getLogoHtml',
		]);
		$this->crud->addColumn([
			'name' => 'description',
			'label' => "Description"
		]);

		// FIELDS
		$this->crud->addField([
			'name' => 'name',
			'label' => 'Name',
			'type' => 'text',
			'attributes' => [
				'disabled' => 'disabled',
			],
		]);
		$this->crud->addField([
			'name' => 'description',
			'label' => 'Description',
			'type' => 'textarea',
			'attributes' => [
				'disabled' => 'disabled',
			],
		]);
		$this->crud->addField([
			'name' => 'value',
			'label' => 'Value',
			'type' => 'text'
		]);
	}

	/**
	 * Display all rows in the database for this entity.
	 * This overwrites the default CrudController behaviour:
	 * - instead of showing all entries, only show the "active" ones.
	 *
	 * @return Response
	 */
	public function index()
	{
		// if view_table_permission is false, abort
		$this->crud->hasAccessOrFail('list');
		$this->crud->addClause('where', 'active', 1); // <---- this is where it's different from CrudController::index()

		$this->data['entries'] = $this->crud->getEntries();
		$this->data['crud'] = $this->crud;
		$this->data['title'] = ucfirst($this->crud->entity_name_plural);

		// load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
		return view('crud::list', $this->data);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$this->crud->hasAccessOrFail('update');

		$this->data['entry'] = $this->crud->getEntry($id);
		$this->crud->addField((array) json_decode($this->data['entry']->field)); // <---- this is where it's different
		$this->data['crud'] = $this->crud;
		$this->data['fields'] = $this->crud->getUpdateFields($id);
		$this->data['title'] = trans('backpack::crud.edit').' '.$this->crud->entity_name;

		$this->data['id'] = $id;

		// load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
		return view('crud::edit', $this->data);
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
