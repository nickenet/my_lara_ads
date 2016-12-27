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

namespace App\Http\Controllers\Account;

use App\Larapen\Helpers\Arr;
use App\Larapen\Helpers\Search;
use App\Larapen\Models\Ad;
use App\Larapen\Models\Category;
use App\Larapen\Models\SavedAd;
use App\Larapen\Models\SavedSearch;
use App\Larapen\Scopes\ActiveScope;
use App\Larapen\Scopes\ReviewedScope;
use App\Mail\AdDeleted;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Torann\LaravelMetaTags\Facades\MetaTag;

class AdsController extends AccountBaseController
{
    /**
     * @param $pagePath
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
	public function getPage($pagePath)
	{
        view()->share('pagePath', $pagePath);

		switch ($pagePath) {
			case 'myads':
				return $this->getMyAds();
				break;
			case 'archived':
				return $this->getArchivedAds($pagePath);
				break;
			case 'favourite':
				return $this->getFavouriteAds();
				break;
			case 'pending-approval':
				return $this->getPendingApprovalAds();
				break;
			default:
				abort(404);
		}
	}

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getMyAds()
    {
        $data = array();
        $data['ads'] = $this->my_ads->get();
        $data['type'] = 'myads';
        
        // Meta Tags
        MetaTag::set('title', t('My ads'));
        MetaTag::set('description', t('My ads on :app_name', ['app_name' => config('settings.app_name')]));

        return view('account.ads', $data);
    }

    /**
     * @param $pagePath
     * @param null $adId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getArchivedAds($pagePath, $adId = null)
    {
        // If repost
        if (str_contains(URL::current(), $pagePath.'/repost')) {
            $res = false;
            if (is_numeric($adId) and $adId > 0) {
                $res = Ad::find($adId)->update(['archived' => 0]);
            }
            if (!$res) {
				flash()->success(t("The repost has done successfully."));
            } else {
				flash()->error(t("The repost has failed. Please try again."));
            }
            
            return redirect($this->lang->get('abbr') . '/account/' . $pagePath);
        }
        
        $data = array();
        $data['ads'] = $this->archived_ads->get();
        
        // Meta Tags
        MetaTag::set('title', t('My archived ads'));
        MetaTag::set('description', t('My archived ads on :app_name', ['app_name' => config('settings.app_name')]));

        view()->share('pagePath', $pagePath);
        return view('account.ads', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFavouriteAds()
    {
        $data = array();
        $data['ads'] = $this->favourite_ads->get();
        
        // Meta Tags
        MetaTag::set('title', t('My favourite ads'));
        MetaTag::set('description', t('My favourite ads on :app_name', ['app_name' => config('settings.app_name')]));
        
        return view('account.ads', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPendingApprovalAds()
    {
        $data = array();
        $data['ads'] = $this->pending_ads->get();
        
        // Meta Tags
        MetaTag::set('title', t('My pending approval ads'));
        MetaTag::set('description', t('My pending approval ads on :app_name', ['app_name' => config('settings.app_name')]));
        
        return view('account.ads', $data);
    }

    /**
     * @param HttpRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSavedSearch(HttpRequest $request)
    {
        $data = array();
        
        // Get QueryString
        $tmp = parse_url(qsurl(url(Request::getRequestUri()), Request::all()));
        $query_string = (isset($tmp['query']) ? $tmp['query'] : 'false');
        
        // CATEGORIES COLLECTION
        $cats = Category::where('translation_lang', $this->lang->get('abbr'))->orderBy('lft')->get();
        $cats = collect($cats)->keyBy('translation_of');
        view()->share('cats', $cats);
        
        // Search
        $saved_search = SavedSearch::where('user_id', $this->user->id)->orderBy('created_at', 'DESC')->get();
        
        if (collect($saved_search)->keyBy('query')->keys()->contains($query_string)) {
            if (!is_null($saved_search) and count($saved_search) > 0) {
                $search = new Search($request, $this->country, $this->lang);
                $data = $search->fechAll();
            }
        }
        $data['saved_search'] = $saved_search;
        
        // Meta Tags
        MetaTag::set('title', t('My saved search'));
        MetaTag::set('description', t('My saved search on :app_name', ['app_name' => config('settings.app_name')]));

        view()->share('pagePath', 'saved-search');
        return view('account.saved-search', $data);
    }

	/**
	 * @param $pagePath
	 * @param null $adId
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
    public function delete($pagePath, $adId = null)
    {
        // Get Entries ID
        $ids = [];
        if (Input::has('ad')) {
            $ids = Input::get('ad');
        } else {
            $id = $adId;
			if (!is_numeric($id) and $id <= 0) {
				$ids = [];
			} else {
				$ids[] = $id;
			}
        }
        
        // Delete
        $nb = 0;
        if ($pagePath == 'favourite') {
            $saved_ads = SavedAd::where('user_id', $this->user->id)->whereIn('ad_id', $ids);
            if (!empty($saved_ads)) {
                $nb = $saved_ads->delete();
            }
        } elseif ($pagePath == 'saved-search') {
            $nb = SavedSearch::destroy($ids);
        } else {
            foreach ($ids as $id) {
                $ad = Ad::withoutGlobalScopes([ActiveScope::class, ReviewedScope::class])->find($id);
                if (!empty($ad)) {
                    $tmp_ad = Arr::toObject($ad->toArray());
                    
                    // Delete Ad
                    $nb = $ad->delete();
                    
                    // Send an Email confirmation
                    try {
                        Mail::send(new AdDeleted($tmp_ad));
                    } catch (\Exception $e) {
                        flash()->error($e->getMessage());
                    }
                }
            }
        }
        
        // Confirmation
        if ($nb == 0) {
            flash()->error(t("No deletion is done. Please try again."));
        } else {
            $count = count($ids);
            if ($count > 1) {
                flash()->success(t("x ads has been deleted successfully.", ['count' => $count]));
            } else {
                flash()->success(t("1 ad has been deleted successfully."));
            }
        }
        
        return redirect($this->lang->get('abbr') . '/account/' . $pagePath);
    }
}
