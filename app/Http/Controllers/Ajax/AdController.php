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

use App\Larapen\Models\Ad;
use App\Http\Controllers\FrontController;
use App\Larapen\Models\SavedAd;
use App\Larapen\Models\SavedSearch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Larapen\TextToImage\Facades\TextToImage;

class AdController extends FrontController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveAd(Request $request)
    {
        $ad_id = $request->input('ad_id');
        
        $status = 0;
        if (Auth::check()) {
            $user = Auth::user();
            
            $saved_ad = SavedAd::where('user_id', $user->id)->where('ad_id', $ad_id);
            if ($saved_ad->count() > 0) {
                // Delete Save Ads
                $saved_ad->delete();
            } else {
                // Store Save Ads
                $saved_ad = new SavedAd(array(
                    'user_id' => $user->id,
                    'ad_id' => $ad_id,
                ));
                $saved_ad->save();
                $status = 1;
            }
        }
        
        $result = [
            'logged' => (Auth::check()) ? $user->id : 0,
            'adId' => $ad_id,
            'status' => $status,
            'loginUrl' => url($this->lang->get('abbr') . '/' . trans('routes.login')),
        ];
        
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function saveSearch(Request $request)
    {
        $query_url = $request->input('url');
        $tmp = parse_url($query_url);
        $query = $tmp['query'];
        parse_str($query, $tab);
        $keyword = $tab['q'];
        $count_ads = $request->input('count_ads');
        if ($keyword == '') {
            return response()->json([], 200, [], JSON_UNESCAPED_UNICODE);
        }
        
        $status = 0;
        if (Auth::check()) {
            $user = Auth::user();
            
            $saveSearch = SavedSearch::where('user_id', $user->id)->where('keyword', $keyword)->where('query', $query);
            if ($saveSearch->count() > 0) {
                // Delete Save Search
                $saveSearch->delete();
            } else {
                // Store Save Ads
                $saveSearch = new SavedSearch(array(
                    'user_id' => $user->id,
                    'keyword' => $keyword,
                    'query' => $query,
                    'count' => $count_ads,
                ));
                $saveSearch->save();
                $status = 1;
            }
        }
        
        $result = [
            'logged' => (Auth::check()) ? $user->id : 0,
            'query' => $query,
            'status' => $status,
            'loginUrl' => url($this->lang->get('abbr') . '/' . trans('routes.login')),
        ];
        
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function getPhone(Request $request)
    {
        $ad_id = $request->input('ad_id', 0);
        
        $ad = Ad::active()->where('id', $ad_id)->first();
        
        if (empty($ad)) {
            return response()->json(['error' => ['message' => t("Error. Ad doesn't exists."),], 404]);
        }
        
        $ad->seller_phone = TextToImage::make($ad->seller_phone, IMAGETYPE_PNG, ['color' => '#FFFFFF']);
        
        return response()->json(['phone' => $ad->seller_phone], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
