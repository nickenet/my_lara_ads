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

use App\Larapen\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class Country extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'countries';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $appends = ['icode'];
    protected $visible = ['code', 'name', 'asciiname', 'icode', 'currency_code', 'phone', 'languages', 'currency'];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    // public $timestamps = false;
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'name', 'asciiname', 'capital', 'continent', 'tld', 'currency_code', 'phone', 'languages', 'active'];
    
    /**
     * The attributes that should be hidden for arrays
     *
     * @var array
     */
    // protected $hidden = [];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'created_at'];
    
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope(new ActiveScope());
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

		// Install country's decoration
		$html .= ' - ';
		if ($this->active == 1) {
			$html .= '<a href="" id="install' . $id . '" class="ajax-request btn btn-xs btn-success" ' . $data . '><i class="fa fa-download"></i> Installed</a>';
		} else {
			$html .= '<a href="" id="install' . $id . '" class="ajax-request btn btn-xs btn-default" ' . $data . '><i class="fa fa-download"></i> Install</a>';
		}

		return $html;
	}
    
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function currency()
    {
        return $this->belongsTo('App\Larapen\Models\Currency', 'currency_code', 'code');
    }
    public function continent()
    {
        return $this->belongsTo('App\Larapen\Models\Continent', 'continent_code', 'code');
    }
    
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        if (Request::segment(1) == config('backpack.base.route_prefix', 'admin')) {
            return $query;
        }
        
        return $query->where('active', 1);
    }
    
    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */
    public function getIcodeAttribute()
    {
        return strtolower($this->attributes['code']);
    }
    
    public function getIdAttribute($value)
    {
        return $this->attributes['code'];
    }
    
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
