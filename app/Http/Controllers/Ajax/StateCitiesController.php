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

use App\Larapen\Models\SubAdmin1;
use App\Larapen\Models\City;
use App\Http\Controllers\FrontController;
use Illuminate\Http\Request;

class StateCitiesController extends FrontController
{
    /**
     * StateCitiesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function getCities(Request $request)
    {
        $language_code = $request->input('language_code');
        $sub_admin1_code = $request->input('full_state_code');
        $tmp = explode('.', $sub_admin1_code);
        $state_id = end($tmp);
        $curr_search = unserialize(base64_decode($request->input('curr_search')));
		// Remove Region filter if exists
		if (isset($curr_search['r'])) {
			unset($curr_search['r']);
		}
        $_token = $request->input('_token');
        
        $state = SubAdmin1::find($sub_admin1_code);
        $cities = City::where('country_code', '=', $this->country->get('code'))
            ->where('subadmin1_code', '=', $state_id)->take(60)
            ->orderBy('population', 'DESC')
            ->orderBy('name')
            ->get();
        
        if (!isset($state) or !isset($cities)) {
            return response()->json([], 200, [], JSON_UNESCAPED_UNICODE);
        }
        
        $col = round($cities->count() / 3, 0, PHP_ROUND_HALF_EVEN); // count + 1 (All Cities)
        $col = ($col > 0) ? $col : 1;
        
        $cities = $cities->chunk($col);
        
        $html = '';
        $i = 0;
        foreach ($cities as $col) {
            $html .= '<div class="col-md-4">';
            $html .= '<ul class="list-link list-unstyled">';
            $j = 0;
            foreach ($col as $city) {
                if ($i == 0 and $j == 0) {
                    $pathUri = $language_code.'/'.t('v-search', ['countryCode' => $this->country->get('icode')], 'routes', $language_code);
                    $url = url($pathUri);
                    $html .= '<li> <a href="' . $url . '">' . t('All Cities', [], 'global', $language_code) . '</a> </li>';
                }
                // Build URL
                $pathUri = $language_code.'/'.t('v-search', ['countryCode' => $this->country->get('icode')], 'routes', $language_code);
                $params = ['d' => $this->country->get('icode'), 'l' => $city->id, '_token' => $_token];
                $url = qsurl($pathUri, array_merge($curr_search, $params), null, false);

                // Print
                $html .= '<li>';
                $html .= '<a href="' . $url . '" title="' . $city->name . '">';
                $html .= $city->name;
                $html .= '</a>';
                $html .= '</li>';
                $j++;
            }
            $html .= '</ul>';
            $html .= '</div>';
            $i++;
        }
        
        $result = [
            'selectState' => $state->name,
            'stateCities' => $html,
        ];
        
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
