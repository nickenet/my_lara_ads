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

use App\Larapen\Models\Pack;
use Larapen\CRUD\app\Http\Controllers\CrudController;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Requests\CrudRequest as StoreRequest;
use Backpack\CRUD\app\Http\Requests\CrudRequest as UpdateRequest;

class PaymentController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\Payment');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/payment');
		$this->crud->setEntityNameStrings('payment', 'payments');
		$this->crud->denyAccess(['create', 'delete']);
		$this->crud->orderBy('created_at', 'DESC');

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
			'name' => 'created_at',
			'label' => "Date",
		]);
		$this->crud->addColumn([
			'name' => 'ad_id',
			'label' => "Ad",
			'type' => "model_function",
			'function_name' => 'getAdTitleHtml',
		]);
		$this->crud->addColumn([
			'name' => 'pack_id',
			'label' => "Pack",
			'type' => "model_function",
			'function_name' => 'getPackNameHtml',
		]);
		$this->crud->addColumn([
			'name' => 'payment_method_id',
			'label' => "Payment Method",
			'model' => "App\Larapen\Models\PaymentMethod",
			'entity' => 'paymentMethod',
			'attribute' => 'name',
			'type' => 'select',
		]);

		// FIELDS
	}
    
    public function store(StoreRequest $request)
    {
        return parent::storeCrud();
    }
    
    public function update(UpdateRequest $request)
    {
        return parent::updateCrud();
    }
    
    // unused for this moment
    public function packs()
    {
        $entries = Pack::where('translation_lang', config('app.locale'))->orderBy('lft')->get();
        if (is_null($entries)) {
            return [];
        }
        
        $tab = [];
        foreach ($entries as $entry) {
            $tab[$entry->translation_of] = $entry->name;
        }
        
        return $tab;
    }
}
