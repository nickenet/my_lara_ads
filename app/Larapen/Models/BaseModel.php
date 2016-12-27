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

namespace App\Larapen\Models;

use Illuminate\Database\Eloquent\Model;
use Prologue\Alerts\Facades\Alert;
use Auth;
use Illuminate\Support\Facades\Request;
use Larapen\CRUD\CrudTrait;

class BaseModel extends Model
{
    use CrudTrait;
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

	public function getActiveHtml()
	{
		if (!isset($this->active)) return false;

		$id = $this->{$this->primaryKey};
		$lineId = 'active' . $id;
		$data = 'data-table="' . $this->getTable() . '" 
			data-field="active" 
			data-line-id="' . $lineId . '" 
			data-id="' . $id . '" 
			data-value="' . $this->active . '"';

		// Decoration
		if ($this->active == 1) {
			$html = '<i id="' . $lineId . '" class="fa fa-check-square-o" aria-hidden="true"></i>';
		} else {
			$html = '<i id="' . $lineId . '" class="fa fa-square-o" aria-hidden="true"></i>';
		}
		$html = '<a href="" class="ajax-request" ' . $data . '>' . $html . '</a>';

		return $html;
	}
    
    // ...
}
