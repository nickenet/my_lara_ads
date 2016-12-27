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

namespace App\Http\Controllers\Ajax;

use App\Larapen\Models\City;
use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;

class AutocompleteController extends FrontController
{
    /**
     * AutocompleteController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Sub-Location => Cities
     *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function getCities()
    {
        $query = Input::get('query');
        $suggestions = [];
        
        if (strlen($query) > 0) {
            $cities = Cache::remember(md5($this->country->get('code') . '-citiesStartWith-' . $query), $this->cache_expire, function () use ($query) {
                return City::where('country_code', $this->country->get('code'))->where(function ($sql) use ($query) {
                    $sql->where('name', 'LIKE', $query . '%');
                    $sql->orWhere('name', 'LIKE', '%' . $query);
                })->orderBy('name')->get(['id as data', 'name as value'])->take(25)->toArray();
            });
            
            if (!is_null($cities)) {
                $suggestions = $cities;
            }
        }
        $result = [
            'query' => $query,
            'suggestions' => $suggestions,
        ];
        
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
