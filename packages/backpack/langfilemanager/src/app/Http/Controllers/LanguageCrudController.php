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

namespace Larapen\LangFileManager\app\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Larapen\CRUD\app\Http\Controllers\CrudController;
use Backpack\LangFileManager\app\Services\LangFiles;
use Backpack\LangFileManager\app\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

// VALIDATION: change the requests to match your own file names if you need form validation
use Larapen\LangFileManager\app\Http\Requests\LanguageRequest as StoreRequest;
use Larapen\LangFileManager\app\Http\Requests\LanguageRequest as UpdateRequest;

class LanguageCrudController extends CrudController
{
	public function __construct()
	{
		parent::__construct();

		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->crud->setModel('App\Larapen\Models\Language');
		$this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/language');
		$this->crud->setEntityNameStrings('language', 'languages');

		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		$this->crud->addColumn([
			'name' => 'name',
			'label' => trans('backpack::langfilemanager.language_name'),
		]);
		$this->crud->addColumn([
			'name' => 'active',
			'label' => trans('backpack::langfilemanager.active'),
			'type' => "model_function",
			'function_name' => 'getActiveHtml',
		]);
		$this->crud->addColumn([
			'name' => 'default',
			'label' => trans('backpack::langfilemanager.default'),
			'type' => "model_function",
			'function_name' => 'getDefaultHtml',
		]);

		// FIELDS
		$this->crud->addField([
			'name' => 'name',
			'label' => trans('backpack::langfilemanager.language_name'),
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter a name',
			],
		]);
		$this->crud->addField([
			'name' => 'native',
			'label' => trans('backpack::langfilemanager.native_name'),
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the native name',
			],
		]);
		$this->crud->addField([
			'name' => 'abbr',
			'label' => trans('backpack::langfilemanager.code_iso639-1'),
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the language code',
			],
		]);
		$this->crud->addField([
			'name' => 'locale',
			'label' => 'Locale Code (E.g. en_US)',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the locale code',
			],
		]);
		$this->crud->addField([
			'name' => 'script',
			'label' => 'Script',
			'type' => 'text',
			'attributes' => [
				'placeholder' => 'Enter the script code (latn, etc.)',
			],
		]);
		$this->crud->addField([
			'name' => 'flag',
			'label' => trans('backpack::langfilemanager.flag_image'),
			'type' => 'browse',
			'attributes' => [
				'placeholder' => 'Enter the language icon',
			],
		]);
		$this->crud->addField([
			'name' => 'active',
			'label' => trans('backpack::langfilemanager.active'),
			'type' => 'checkbox',
		]);
		$this->crud->addField([
			'name' => 'default',
			'label' => trans('backpack::langfilemanager.default'),
			'type' => 'checkbox',
		]);
	}

	public function store(StoreRequest $request)
	{
		$defaultLang = Language::where('default', 1)->first();

		// Copy the default language folder to the new language folder
		\File::copyDirectory(resource_path('lang/'.$defaultLang->abbr), resource_path('lang/'.$request->input('abbr')));

		return parent::storeCrud();
	}

	public function update(UpdateRequest $request)
	{
		// Set default language
		if (Input::has('default')) {
			if (Input::get('default') == 1 or Input::get('default') == 'on') {
				$model = $this->crud->getModel();

                // Unset the old default language
                $langs = $model::whereIn('active', [0, 1])->update(['default' => 0]);

                // Set the new default language
                $lang = $model::where('abbr', Input::get('abbr'))->update(['default' => 1]);
			}
		}

		return parent::updateCrud();
	}

	/**
	 * After delete remove also the language folder.
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function destroy($id)
	{
		$language = Language::find($id);
		$destroyResult = parent::destroy($id);

		if ($destroyResult) {
			\File::deleteDirectory(resource_path('lang/'.$language->abbr));
		}

		return $destroyResult;
	}

	public function showTexts(LangFiles $langfile, Language $languages, $lang = '', $file = 'site')
	{
		// SECURITY
		// check if that file isn't forbidden in the config file
		if (in_array($file, config('backpack.langfilemanager.language_ignore'))) {
			abort('403', trans('backpack::langfilemanager.cant_edit_online'));
		}

		if ($lang) {
			$langfile->setLanguage($lang);
		}

		$langfile->setFile($file);
		$this->data['crud'] = $this->crud;
		$this->data['currentFile'] = $file;
		$this->data['currentLang'] = $lang ?: config('app.locale');
		$this->data['currentLangObj'] = Language::where('abbr', '=', $this->data['currentLang'])->first();
		$this->data['browsingLangObj'] = Language::where('abbr', '=', config('app.locale'))->first();
		$this->data['languages'] = $languages->orderBy('name')->get();
		$this->data['langFiles'] = $langfile->getlangFiles();
		$this->data['fileArray'] = $langfile->getFileContent();
		$this->data['langfile'] = $langfile;
		$this->data['title'] = trans('backpack::langfilemanager.translations');

		return view('langfilemanager::translations', $this->data);
	}

	public function updateTexts(LangFiles $langfile, Request $request, $lang = '', $file = 'site')
	{
		// SECURITY
		// check if that file isn't forbidden in the config file
		if (in_array($file, config('backpack.langfilemanager.language_ignore'))) {
			abort('403', trans('backpack::langfilemanager.cant_edit_online'));
		}

		$message = trans('error.error_general');
		$status = false;

		if ($lang) {
			$langfile->setLanguage($lang);
		}

		$langfile->setFile($file);

		$fields = $langfile->testFields($request->all());
		if (empty($fields)) {
			if ($langfile->setFileContent($request->all())) {
				\Alert::success(trans('backpack::langfilemanager.saved'))->flash();
				$status = true;
			}
		} else {
			$message = trans('admin.language.fields_required');
			\Alert::error(trans('backpack::langfilemanager.please_fill_all_fields'))->flash();
		}

		return redirect()->back();
	}
}
