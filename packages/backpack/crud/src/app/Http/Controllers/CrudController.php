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

namespace Larapen\CRUD\app\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Prologue\Alerts\Facades\Alert;

// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Requests\CrudRequest as StoreRequest;
use Backpack\CRUD\app\Http\Requests\CrudRequest as UpdateRequest;

class CrudController extends \Backpack\CRUD\app\Http\Controllers\CrudController
{
    public function __construct()
    {
        // Fix add enum fields
        $enumFields = ['active', 'reviewed', 'archived', 'default', 'blocked', 'closed'];
        foreach ($enumFields as $field) {
            if (Input::has($field)) {
                if (Input::get($field) == 'on') {
                    Input::merge([$field => 1]);
                }
            }
        }

        parent::__construct();

		/* SET CONFIG */
		// App name
		config(['app.name' => config('settings.app_name')]);
		// Auth
		config(['auth.passwords.users.email' => 'vendor.backpack.base.auth.emails.password']);
		// Mail
		config(['mail.driver' => env('MAIL_DRIVER', config('settings.mail_driver'))]);
		config(['mail.host' => env('MAIL_HOST', config('settings.mail_host'))]);
		config(['mail.port' => env('MAIL_PORT', config('settings.mail_port'))]);
		config(['mail.encryption' => env('MAIL_ENCRYPTION', config('settings.mail_encryption'))]);
		config(['mail.username' => env('MAIL_USERNAME', config('settings.mail_username'))]);
		config(['mail.password' => env('MAIL_PASSWORD', config('settings.mail_password'))]);
		config(['mail.from.address' => config('settings.app_email')]);
		config(['mail.from.name' => config('settings.app_name')]);
		// Mailgun
		config(['services.mailgun.domain' => env('MAILGUN_DOMAIN', config('settings.mailgun_domain'))]);
		config(['services.mailgun.secret' => env('MAILGUN_SECRET', config('settings.mailgun_secret'))]);
		// Mandrill
		config(['services.mandrill.secret' => env('MANDRILL_SECRET', config('settings.mandrill_secret'))]);
		// Amazon SES
		config(['services.ses.key' => env('SES_KEY', config('settings.ses_key'))]);
		config(['services.ses.secret' => env('SES_SECRET', config('settings.ses_secret'))]);
		config(['services.ses.region' => env('SES_REGION', config('settings.ses_region'))]);
    }

	/**
	 * Display all rows in the database for this entity.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->crud->hasAccessOrFail('list');

		$this->data['crud'] = $this->crud;
		$this->data['title'] = ucfirst($this->crud->entity_name_plural);

		// get all entries if AJAX is not enabled
		if (! $this->data['crud']->ajaxTable()) {
			//$this->data['entries'] = $this->data['crud']->getEntries();

			$model = $this->crud->getModel();
			if (property_exists($model, 'translatable'))
			{
				$this->data['entries'] = $model::where('translation_lang', \Lang::locale())->get();
			}
			else
			{
				$this->data['entries'] = $model::all();
			}
		}

		// load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
		// $this->crud->getListView() returns 'list' by default, or 'list_ajax' if ajax was enabled
		return view('crud::list', $this->data);
	}

	/**
	 * @param UpdateRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function storeCrud(StoreRequest $request = null)
	{
		try {
			return parent::storeCrud();
		}
		catch (\Exception $e)
		{
			// Get error message
			if (isset($e->errorInfo) && isset($e->errorInfo[2]) && !empty($e->errorInfo[2])) {
				$msg = $e->errorInfo[2];
			} else {
				$msg = $e->getMessage();
			}

			// Error notification
			Alert::error('Error found - [' . $e->getCode() . '] : ' . $msg . '.')->flash();

			// fallback to global request instance
			if (is_null($request)) {
				$request = \Request::instance();
			}

			return \Redirect::to($request->input('redirect_after_save'));
		}
	}

	/**
	 * @param UpdateRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function updateCrud(UpdateRequest $request = null)
	{
		try {
			return parent::updateCrud();
		}
		catch (\Exception $e)
		{
			// Get error message
			if (isset($e->errorInfo) && isset($e->errorInfo[2]) && !empty($e->errorInfo[2])) {
				$msg = $e->errorInfo[2];
			} else {
				$msg = $e->getMessage();
			}

			// Error notification
			Alert::error('Error found - [' . $e->getCode() . '] : ' . $msg . '.')->flash();

			return \Redirect::to($this->crud->route);
		}
	}










	/**
	 *  Reorder the items in the database using the Nested Set pattern.
	 *
	 *	Database columns needed: id, parent_id, lft, rgt, depth, name/title
	 *
     * @param bool $lang
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function reorder($lang = false)
	{
		$this->crud->hasAccessOrFail('reorder');

		if (! $this->crud->isReorderEnabled()) {
			abort(403, 'Reorder is disabled.');
		}

		// get all results for that entity
		//$this->data['entries'] = $this->crud->getEntries();
		if ($lang == false)
		{
			$lang = \Lang::locale();
		}
		$model = $this->crud->getModel();
		if (property_exists($model, 'translatable'))
		{
			$this->data['entries'] = $model::where('translation_lang', $lang)->get();
			$this->data['languages'] = \App\Larapen\Models\Language::where('active', 1)->get();
			$this->data['active_language'] = $lang;
		}
		else
		{
			$this->data['entries'] = $model::all();
		}

		$this->data['crud'] = $this->crud;
		$this->data['title'] = trans('backpack::crud.reorder').' '.$this->crud->entity_name;

		// load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
		return view('crud::reorder', $this->data);
	}

    /**
     * Save the new order, using the Nested Set pattern.
     *
     * Database columns needed: id, parent_id, lft, rgt, depth, name/title
     *
     * @return bool|string
     */
    public function saveReorder()
    {
        // if reorder_table_permission is false, abort
        $this->crud->hasAccessOrFail('reorder');

        if (! $this->crud->isReorderEnabled()) {
            abort(403, 'Reorder is disabled.');
        }

        $model = $this->crud->getModel();
        $count = 0;
        $all_entries = \Request::input('tree');

        if (count($all_entries)) {
            foreach ($all_entries as $key => $entry) {
                if ($entry['item_id'] != "" && $entry['item_id'] != null) {
                    $item = $model::find($entry['item_id']);
                    $item->parent_id = $entry['parent_id'];
                    $item->depth = $entry['depth'];
                    $item->lft = $entry['left'];
                    $item->rgt = $entry['right'];
                    $item->save();

                    $count++;
                }
            }
        }
        else
        {
            return false;
        }

        return 'success for '.$count." items";
    }










	/**
	 * Used with AJAX in the list view (datatables) to show extra information about that row that didn't fit in the table.
	 * It defaults to showing all connected translations and their CRUD buttons.
	 *
	 * It's enabled by:
	 * - setting the $crud['details_row'] variable to true;
	 * - adding the details route for the entity; ex: Route::get('page/{id}/details', 'PageCrudController@showDetailsRow');
	 */
	public function showDetailsRow($id)
	{
		// get the info for that entry
		$model = $this->crud->getModel();
		$this->data['entry'] = $model::find($id);
		$this->data['entry']->addFakes($this->getFakeColumnsAsArray());
		$this->data['original_entry'] = $this->data['entry'];
		$this->data['crud'] = $this->crud;

		if (property_exists($model, 'translatable'))
		{
			$this->data['translations'] = $this->data['entry']->translations();

			// create a list of languages the item is not translated in
			$this->data['languages'] = \App\Larapen\Models\Language::where('active', 1)->get();
			$this->data['languages_already_translated_in'] = $this->data['entry']->translationLanguages();
			$this->data['languages_to_translate_in'] = $this->data['languages']->diff($this->data['languages_already_translated_in']);
			$this->data['languages_to_translate_in'] = $this->data['languages_to_translate_in']->reject(function ($item) {
				return $item->abbr == \Lang::locale();
			});
		}

		// load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
		return view('crud::details_row', $this->data);
	}

	/**
	 * Duplicate an existing item into another language and open it for editing.
	 */
	public function translateItem($id, $lang)
	{
		$model = $this->crud->getModel();
		$this->data['entry'] = $model::find($id);
		// check if there isn't a translation already
		$existing_translation = $this->data['entry']->translation($lang);

		if ($existing_translation)
		{
			$new_entry = $existing_translation;
		}
		else
		{
			// get the info for that entry
			$new_entry_attributes = $this->data['entry']->getAttributes();
			$new_entry_attributes['translation_lang'] = $lang;
			$new_entry_attributes['translation_of'] = $id;
			$new_entry_attributes = array_except($new_entry_attributes, 'id');

			$new_entry = $model::create($new_entry_attributes);

			if (empty($new_entry)) {
				$this->data['entry'] = $model::find($id);
				$new_entry = $this->data['entry']->translation($lang);
			}
		}

		// redirect to the edit form for that translation
		return redirect(str_replace($id.'/', $new_entry->id.'/', str_replace('translate/'.$lang, 'edit', \Request::url())));
	}

	/**
	 * COMMODITY FUNCTIONS
	 */

	/**
	 * Returns an array of database columns names, that are used to store fake values.
	 * Returns ['extras'] if no columns have been found.
	 *
	 */
	protected function getFakeColumnsAsArray() {

		$this->prepareFields();

		$fake_field_columns_to_encode = [];

		foreach ($this->crud->fields as $k => $field) {
			// if it's a fake field
			if (isset($this->crud->fields[$k]['fake']) && $this->crud->fields[$k]['fake']==true) {
				// add it to the request in its appropriate variable - the one defined, if defined
				if (isset($this->crud->fields[$k]['store_in'])) {
					if(!in_array($this->crud->fields[$k]['store_in'], $fake_field_columns_to_encode, true)){
						array_push($fake_field_columns_to_encode, $this->crud->fields[$k]['store_in']);
					}
				}
				else //otherwise in the one defined in the $crud variable
				{
					if(!in_array('extras', $fake_field_columns_to_encode, true)){
						array_push($fake_field_columns_to_encode, 'extras');
					}
				}
			}
		}

		if (!count($fake_field_columns_to_encode)) {
			return ['extras'];
		}

		return $fake_field_columns_to_encode;
	}

	/**
	 * Prepare the fields to be shown, stored, updated or created.
	 *
	 * Makes sure $this->crud->fields is in the proper format (array of arrays);
	 * Makes sure $this->crud->fields also contains the id of the current item;
	 * Makes sure $this->crud->fields also contains the values for each field;
	 *
	 */
	protected function prepareFields($entry = false)
	{
		// if the fields have been defined separately for create and update, use that
		if (!isset($this->crud->fields)) {
			if (isset($this->crud->create_fields)) {
				$this->crud->fields = $this->crud->create_fields;
			} elseif (isset($this->crud['update_fields'])) {
				$this->crud->fields = $this->crud['update_fields'];
			}
		}

		// PREREQUISITES CHECK:
		// if the fields aren't set, trigger error
		if (!isset($this->crud->fields)) {
			abort(500, "The CRUD fields are not defined.");
		}

		// if the fields are defined as a string, transform it to a proper array
		if (!is_array($this->crud->fields)) {
			$current_fields_array = explode(",", $this->crud->fields);
			$proper_fields_array = array();

			foreach ($current_fields_array as $key => $field) {
				$proper_fields_array[] = [
					'name' => $field,
					'label' => ucfirst($field), // TODO: also replace _ with space
					'type' => 'text' // TODO: choose different types of fields depending on the MySQL column type
				];
			}

			$this->crud->fields = $proper_fields_array;
		}

		// if no field type is defined, assume the "text" field type
		foreach ($this->crud->fields as $k => $field) {
			if (!isset($this->crud->fields[$k]['type'])) {
				$this->crud->fields[$k]['type'] = 'text';
			}
		}

		// if an entry was passed, we're preparing for the update form, not create
		if ($entry) {
			// put the values in the same 'fields' variable
			$fields = $this->crud->fields;

			foreach ($fields as $k => $field) {
				// set the value
				if (!isset($this->crud->fields[$k]['value'])) {
					$this->crud->fields[$k]['value'] = $entry->{$field['name']};
				}
			}

			// always have a hidden input for the entry id
			$this->crud->fields[] = array(
				'name' => 'id',
				'value' => $entry->id,
				'type' => 'hidden'
			);
		}
	}
}
