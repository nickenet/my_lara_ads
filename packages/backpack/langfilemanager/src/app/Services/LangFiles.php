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

namespace Larapen\LangFileManager\app\Services;

class LangFiles extends \Backpack\LangFileManager\app\Services\LangFiles
{
    private $lang;
    private $file = 'crud';
    
    public function __construct()
    {
        parent::__construct();
        
        $this->lang = config('app.locale');
    }

	/**
	 * get the language files that can be edited, to ignore a file add it in the config/admin file to language_ignore key.
	 * @return	array
	 */
	public function getlangFiles()
	{
		$fileList = [];

		foreach (scandir($this->getLangPath(), SCANDIR_SORT_DESCENDING) as $file) {
			$fileName = str_replace('.php', '', $file);

			if (! in_array($fileName, array_merge(['.', '..'], config('backpack.langfilemanager.language_ignore')))) {
				$fileList[] = [
					'name' => ucfirst(str_replace('_', ' ', $fileName)),
					'url' => url(config('backpack.base.route_prefix', 'admin')."/language/texts/{$this->lang}/{$fileName}"),
					'active' => $fileName == $this->file,
				];
			}
		}

		// Sort files by name for better readability
		usort($fileList, function ($a, $b) {
			return strnatcmp($a['name'], $b['name']);
		});

		return $fileList;
	}

    private function getLangPath()
    {
        return base_path("resources/lang/{$this->lang}/");
    }
}
