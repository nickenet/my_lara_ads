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
use App\Http\Requests\Admin\CurrencyRequest as StoreRequest;
use App\Http\Requests\Admin\CurrencyRequest as UpdateRequest;

class CurrencyController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\Currency');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/currency');
		$this->crud->setEntityNameStrings('currency', 'currencies');
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
			'label' => "Name"
		]);
		$this->crud->addColumn([
			'name' => 'html_entity',
			'label' => "Html Entity"
		]);
		$this->crud->addColumn([
			'name' => 'in_left',
			'label' => "Symbol in left",
			'type' => "model_function",
			'function_name' => 'getPositionHtml',
		]);

		// FIELDS
		$this->crud->addField([
			'name' => 'code',
			'label' => 'Code',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the currency code (ISO Code)',
			],
		]);
		$this->crud->addField([
			'name' => 'name',
			'label' => 'Name',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the currency name',
			],
		]);
		$this->crud->addField([
			'name' => 'html_entity',
			'label' => 'Html Entity',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the html entity code',
			],
		]);
		$this->crud->addField([
			'name' => 'font_arial',
			'label' => 'Font Arial',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the font arial code',
			],
		]);
		$this->crud->addField([
			'name' => 'font_code2000',
			'label' => 'Font Code2000',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the font code2000 code',
			],
		]);
		$this->crud->addField([
			'name' => 'unicode_decimal',
			'label' => 'Unicode Decimal',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the unicode decimal code',
			],
		]);
		$this->crud->addField([
			'name' => 'unicode_hex',
			'label' => 'Unicode Hex',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the unicode hex code',
			],
		]);
		$this->crud->addField([
			'name' => 'in_left',
			'label' => "Symbol in left",
			'type' => 'checkbox'
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
