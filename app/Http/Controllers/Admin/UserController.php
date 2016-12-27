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

use App\Larapen\Models\Gender;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Larapen\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Support\Facades\Auth;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Admin\UserRequest as StoreRequest;
use App\Http\Requests\Admin\UserRequest as UpdateRequest;

class UserController extends CrudController
{
    public function __construct()
    {
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\User');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/user');
		$this->crud->setEntityNameStrings('user', 'users');
		$this->crud->enableAjaxTable();

		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		if (Request::segment(2) != 'account') {
			// COLUMNS
			$this->crud->addColumn([
				'name' => "id",
				'label' => "ID"
			]);
			$this->crud->addColumn([
				'name' => "name",
				'label' => "Name",
			]);
			$this->crud->addColumn([
				'name' => "email",
				'label' => "Email",
			]);
			$this->crud->addColumn([
				'name' => "user_type_id",
				'label' => "Type",
				'model' => "App\Larapen\Models\UserType",
				'entity' => "userType",
				'attribute' => "name",
				'type' => "select",
			]);
			$this->crud->addColumn([
				'label' => "Country",
				'name' => "country_code",
				'model' => "App\Larapen\Models\Country",
				'entity' => "country",
				'attribute' => "asciiname",
				'type' => "select",
			]);
			$this->crud->addColumn([
				'name' => "active",
				'label' => "Active",
				'type' => "model_function",
				'function_name' => "getActiveHtml",
			]);

			// FIELDS
			$this->crud->addField([
				'name' => "email",
				'label' => "Email Address",
				'type' => "email",
				'attributes' => [
					'placeholder' => "User's Email Address",
				],
			]);
			$this->crud->addField([
				'name' => "password",
				'label' => "Password",
				'type' => "password",
				'attributes' => [
					'placeholder' => "Enter a Password",
				],
			], 'create');
			$this->crud->addField([
				'label' => "Gender",
				'name' => "gender_id",
				'type' => "select_from_array",
				'options' => $this->gender(),
				'allows_null' => false,
			]);
			$this->crud->addField([
				'name' => "name",
				'label' => "Name",
				'type' => "text",
				'attributes' => [
					'placeholder' => "First Name and Last Name",
				],
			]);
			$this->crud->addField([
				'name' => "about",
				'label' => "About",
				'type' => "textarea",
				'attributes' => [
					'placeholder' => "About the user",
				],
			]);
			$this->crud->addField([
				'name' => "phone",
				'label' => "Phone",
				'type' => "text",
				'attributes' => [
					'placeholder' => "Enter a Phone",
				],
			]);
			$this->crud->addField([
				'name' => "phone_hidden",
				'label' => "Phone hidden",
				'type' => "checkbox",
			]);
			$this->crud->addField([
				'label' => "Country",
				'name' => "country_code",
				'model' => "App\Larapen\Models\Country",
				'entity' => "country",
				'attribute' => "asciiname",
				'type' => "select2",
			]);

			$this->crud->addField([
				'name' => "user_type_id",
				'label' => "Type",
				'model' => "App\Larapen\Models\UserType",
				'entity' => "userType",
				'attribute' => "name",
				'type' => "select2",
			]);
			$this->crud->addField([
				'name' => "is_admin",
				'label' => "Is admin",
				'type' => "checkbox",
			]);
			$this->crud->addField([
				'name' => "active",
				'label' => "Active",
				'type' => "checkbox",
			]);
			/*
			$this->crud->addField([
				'name' => "receive_newsletter",
				'label' => "Receive newsletter",
				'type' => "checkbox",
			]);
			$this->crud->addField([
				'name' => "receive_advice",
				'label' => "Receive advice",
				'type' => "checkbox",
			]);
			*/
			$this->crud->addField([
				'name' => "blocked",
				'label' => "Blocked",
				'type' => "checkbox",
			]);
		}

        // Encrypt password
        if (Input::has('password')) {
            Input::merge(array('password' => bcrypt(Input::get('password'))));
        }
    }
    
    public function store(StoreRequest $request)
    {
        return parent::storeCrud();
    }
    
    public function update(UpdateRequest $request)
    {
        return parent::updateCrud();
    }
    
    public function account()
    {
		// FIELDS
		$this->crud->addField([
			'label' => "Gender",
			'name' => "gender_id",
			'type' => "select_from_array",
			'options' => $this->gender(),
			'allows_null' => false,
		]);
		$this->crud->addField([
			'name' => "name",
			'label' => "Name",
			'type' => "text",
			'placeholder' => "First Name and Last Name",
		]);
		$this->crud->addField([
			'name' => "about",
			'label' => "About",
			'type' => "textarea",
			'placeholder' => "About the user",
		]);
		$this->crud->addField([
			'name' => "email",
			'label' => "Email Address",
			'type' => "email",
			'placeholder' => "Admin user's Email Address",
		]);
		$this->crud->addField([
			'name' => "password",
			'label' => "Password",
			'type' => "password",
			'placeholder' => "Enter a password",
		]);
		$this->crud->addField([
			'name' => "phone",
			'label' => "Phone",
			'type' => "text",
			'placeholder' => "Phone",
		]);
		$this->crud->addField([
			'name' => "phone_hidden",
			'label' => "Phone hidden",
			'type' => "checkbox",
		]);
		$this->crud->addField([
			'label' => "Country",
			'name' => "country_code",
			'model' => "App\Larapen\Models\Country",
			'entity' => "country",
			'attribute' => "asciiname",
			'type' => "select2",
		]);
		$this->crud->addField([
			'name' => "user_type_id",
			'label' => "Type",
			'model' => "App\Larapen\Models\UserType",
			'entity' => "userType",
			'attribute' => "name",
			'type' => "select2",
		]);
		/*
		$this->crud->addField([
			'name' => "receive_newsletter",
			'label' => "Receive newsletter",
			'type' => "checkbox",
		]);
		$this->crud->addField([
			'name' => "receive_advice",
			'label' => "Receive advice",
			'type' => "checkbox",
		]);
		*/
        
        // Get logged user
        if (Auth::check()) {
            return $this->edit(Auth::user()->id);
        } else {
            abort(403, 'Not allowed.');
        }
    }
    
    public function gender()
    {
        $entries = Gender::where('translation_lang', config('app.locale'))->get();
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
