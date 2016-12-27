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

use App\Larapen\Models\AdType;
use App\Larapen\Models\Category;
use Illuminate\Support\Facades\Input;
use Larapen\CRUD\app\Http\Controllers\CrudController;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Admin\AdRequest as StoreRequest;
use App\Http\Requests\Admin\AdRequest as UpdateRequest;

class AdController extends CrudController
{
    public function __construct()
    {
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\Ad');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/ad');
		$this->crud->setEntityNameStrings('ad', 'ads');
		$this->crud->enableAjaxTable();
		$this->crud->denyAccess(['create']);

		// Filters
		if (Input::has('active')) {
			if (Input::get('active') == 0) {
				$this->crud->addClause('where', 'active', '=', 0);
				if (config('settings.ads_review_activation')) {
					$this->crud->addClause('orWhere', 'reviewed', '=', 0);
				}
			}
			if (Input::get('active') == 1) {
				$this->crud->addClause('where', 'active', '=', 1);
				if (config('settings.ads_review_activation')) {
					$this->crud->addClause('where', 'reviewed', '=', 1);
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		$this->crud->addColumn([
			'name' => 'created_at',
			'label' => "Date",
			'type' => 'date',
		]);
		$this->crud->addColumn([
			'name' => 'title',
			'label' => "Title",
			'type' => "model_function",
			'function_name' => 'getTitleHtml',
		]);
		$this->crud->addColumn([
			'name' => 'price',
			'label' => "Price",
		]);
		$this->crud->addColumn([
			'name' => 'seller_name',
			'label' => "Saller Name",
		]);
		$this->crud->addColumn([
			'name' => 'country_code',
			'label' => "Country",
		]);
		$this->crud->addColumn([
			'name' => 'city_id',
			'label' => "City",
			'type' => "model_function",
			'function_name' => 'getCityHtml',
		]);
		$this->crud->addColumn([
			'name' => 'active',
			'label' => "Active",
			'type' => "model_function",
			'function_name' => 'getActiveHtml',
		]);
		$this->crud->addColumn([
			'name' => 'reviewed',
			'label' => "Reviewed",
			'type' => "model_function",
			'function_name' => 'getReviewedHtml',
		]);

		// FIELDS
		$this->crud->addField([
			'label' => "Pictures",
			'name' => 'pictures', // Entity method
			'entity' => 'pictures', // Entity method
			'attribute' => 'filename',
			'type' => 'read_images',
		]);
		$this->crud->addField([
			'label' => "Ad Type",
			'name' => 'ad_type_id',
			'type' => 'select_from_array',
			'options' => $this->adType(),
			'allows_null' => false,
		]);
		$this->crud->addField([
			'label' => "Category",
			'name' => 'category_id',
			'type' => 'select_from_array',
			'options' => $this->categories(),
			'allows_null' => false,
		]);
		$this->crud->addField([
			'name' => 'title',
			'label' => 'Title',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter a Title',
			],
		]);
		$this->crud->addField([
			'name' => 'description',
			'label' => "Description",
			'type' => 'textarea',
			'attributes' => [
				'placeholder' => 'Enter a Description',
			],
		]);
		$this->crud->addField([
			'name' => 'new',
			'label' => "Condition",
			'type' => 'select_from_array',
			'options' => [0 => t('Used'), 1 => t('New')],
			'allows_null' => false,
		]);
		$this->crud->addField([
			'name' => 'price',
			'label' => "Price",
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter a Price (or Salary)',
			],
		]);
		$this->crud->addField([
			'name' => 'negotiable',
			'label' => "Negotiable Price",
			'type' => 'checkbox',
		]);
		$this->crud->addField([
			'name' => 'resume',
			'label' => 'Resume (Only if need - Supported file extensions: pdf, doc, docx, jpg or png)',
			'type' => 'browse',
		]);
		$this->crud->addField([
			'name' => 'seller_name',
			'label' => 'User Name',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'User Name',
			],
		]);
		$this->crud->addField([
			'name' => 'seller_email',
			'label' => 'User Email',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'User Email',
			],
		]);
		$this->crud->addField([
			'name' => 'seller_phone',
			'label' => 'User Phone',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'User Phone',
			],
		]);
		$this->crud->addField([
			'name' => 'seller_phone_hidden',
			'label' => "Hide seller phone",
			'type' => 'checkbox',
		]);
		/*$this->crud->addField([
			'name' => 'address',
			'label' => 'Address',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter an Address',
			],
		]);*/
		$this->crud->addField([
			'name' => 'archived',
			'label' => "Archived",
			'type' => 'checkbox'
		]);
		$this->crud->addField([
			'name' => 'active',
			'label' => "Active",
			'type' => 'checkbox'
		]);
		$this->crud->addField([
			'name' => 'reviewed',
			'label' => "Reviewed",
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

    public function adType()
    {
        $entries = AdType::where('translation_lang', config('app.locale'))->get();
        if (is_null($entries)) {
            return [];
        }

        $tab = [];
        foreach ($entries as $entry) {
            $tab[$entry->translation_of] = $entry->name;
        }

        return $tab;
    }

    public function categories()
    {
        $entries = Category::where('translation_lang', config('app.locale'))->orderBy('lft')->get();
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
