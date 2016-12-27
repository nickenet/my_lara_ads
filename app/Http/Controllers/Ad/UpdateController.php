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

namespace App\Http\Controllers\Ad;

use App\Larapen\Helpers\Ip;
use App\Larapen\Helpers\Rules;
use App\Larapen\Models\Ad;
use App\Larapen\Models\AdType;
use App\Larapen\Models\Category;
use App\Larapen\Models\Pack;
use App\Larapen\Models\PaymentMethod;
use App\Larapen\Models\City;
use App\Larapen\Models\Picture;
use App\Larapen\Scopes\ActiveScope;
use App\Larapen\Scopes\ReviewedScope;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Torann\LaravelMetaTags\Facades\MetaTag;
use App\Larapen\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Larapen\Helpers\Localization\Country as CountryLocalization;

class UpdateController extends FrontController
{
    /**
     * UpdateController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        /*
         * References
         */
        $this->countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries(), $this->lang->get('abbr'));
        view()->share('countries', $this->countries);
    }
    
    /**
     * Show the form the create a new ad post.
     *
     * @param $adId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getForm($adId)
    {
        $data = array();
        
        // Get Ad
        // GET ADS INFO
        $ad = Ad::withoutGlobalScopes([ActiveScope::class, ReviewedScope::class])->where('user_id', $this->user->id)->where('id', $adId)->with([
            'user',
            'country',
            'category',
            'adType',
            'city',
            'pictures'
        ])->first();
        
        if (is_null($ad)) {
            abort(404);
        }
        view()->share('ad', $ad);
        
        
        /*
         * References
         */
        $data['categories'] = Category::where('parent_id', 0)->where('translation_lang', $this->lang->get('abbr'))->with([
            'children' => function ($query) {
                $query->where('translation_lang', $this->lang->get('abbr'));
            }
        ])->orderBy('lft')->get();
        $data['ad_types'] = AdType::where('translation_lang', $this->lang->get('abbr'))->get();
        $data['states'] = City::where('country_code', $this->country->get('code'))->where('feature_code', 'ADM1')->get()->all();
        $data['packs'] = Pack::where('translation_lang', $this->lang->get('abbr'))->with('currency')->get();
        $data['payment_methods'] = PaymentMethod::orderBy('lft')->get();
        
        // Debug
        //echo '<pre>'; print_r($data['categories']->toArray()); echo '</pre><hr>'; exit();
        
        // Meta Tags
        MetaTag::set('title', t('Update My Ad'));
        MetaTag::set('description', t('Update My Ad'));
        
        return view('ad.update.index', $data);
    }
    
    /**
     * Store a new ad post.
     *
     * @param $adId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postForm($adId, Request $request)
    {
        // Form validation
        $validator = Validator::make($request->all(), Rules::Ad($request, 'PUT'));
        if ($validator->fails()) {
            // BugFix with : $request->except('pictures')
            return back()->withErrors($validator)->withInput($request->except('pictures'));
        }
        
        // Get Ad
        $ad = Ad::withoutGlobalScopes([ActiveScope::class, ReviewedScope::class])->where('user_id', $this->user->id)->where('id', $adId)->first();
        if (empty($ad)) {
            abort(404);
        }
        
        
        // Update Ad
        // @todo: In this version user can't change the country her Ad! Please add a SELECT BOX in the post view to activate this functionality.
        // $ad->country_code = $request->input('country_code');
        $ad->category_id = $request->input('category');
        $ad->ad_type_id = $request->input('ad_type');
        $ad->title = $request->input('title');
        $ad->description = $request->input('description');
        $ad->new = $request->input('new');
        $ad->price = $request->input('price');
        $ad->negotiable = $request->input('negotiable');
        $ad->seller_name = $request->input('seller_name');
        $ad->seller_email = $request->input('seller_email');
        $ad->seller_phone = $request->input('seller_phone');
        $ad->seller_phone_hidden = $request->input('seller_phone_hidden');
        $ad->ip_addr = Ip::get();
        $ad->save();
        
        
        // Get Country Code
        $country_code = ($ad->country_code != '') ? strtolower($ad->country_code) : strtolower($this->country->get('code'));

        // UPLOAD FILES : PICTURES
        // Paths setting
        $destination_path = 'uploads/pictures/';
        $prefix_filename = $country_code . '/' . $ad->id . '/';
        $full_destination_path = public_path() . '/' . $destination_path . $prefix_filename;

        // Upload
        if ($request->file('pictures')) {
            // Proccess file request
            $files = $request->file('pictures');
            $count_files = count($files);
            if ($count_files > 0) {
                $i = 0;
                // Generate filename
                $filename_gen = uniqid('img_');
                foreach ($files as $key => $file) {
                    // Check empty fields
                    if (is_null($file) or count($file) <= 0) {
                        continue;
                    }

                    try {
                        // Create destination path if not exists
                        if (!File::exists($full_destination_path)) {
                            File::makeDirectory($full_destination_path, 0755, true);
                        }

                        // Delete old file if new file has uploaded
                        // Check if current Ad have a pictures
                        $picture = Picture::find($key);
                        if (!is_null($picture)) {
                            // Delete old file
                            $picture->delete($picture->id);
                        }

                        // Get file extention
                        $extension = $file->getClientOriginalExtension();

                        // Build the new filename
                        $file_number = ($count_files > 1) ? '_' . $i : '';
                        $new_filename = strtolower($prefix_filename . $filename_gen . $file_number . '.' . $extension);

                        // Save Picture (with resizing)
                        Image::make($file)->resize(1000, 1000, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($destination_path . $new_filename);

                        // Ad Picture in database
                        $picture = new Picture(array(
                            'ad_id' => $ad->id,
                            'filename' => $new_filename,
                        ));
                        $picture->save();
                    } catch (\Exception $e) {
                        flash()->error($e->getMessage());
                    }

                    $i++;
                }
            }
        }


        // UPLOAD FILE : RESUME
        // Paths setting
        $destination_path = 'uploads/resumes/';
        $prefix_filename = $country_code . '/' . $ad->id . '/';
        $full_destination_path = public_path() . '/' . $destination_path . $prefix_filename;

        // Upload
        if ($request->hasFile('resume')) {
            // Proccess file request
            $file = $request->file('resume');
            if ($file->isValid()) {
                // Create destination path if not exists
                if (!File::exists($full_destination_path)) {
                    File::makeDirectory($full_destination_path, 0755, true);
                }
                
                // Get file extention
                $extension = $file->getClientOriginalExtension();
                
                // Build the new filename
                $filename_gen = uniqid('resume_');
                $new_filename = strtolower($prefix_filename . $filename_gen . '.' . $extension);

                // Delete old file if new file has uploaded
                if (!empty($ad->resume)) {
                    if (is_file(public_path() . '/' . $destination_path . $ad->resume)) {
                        @unlink(public_path() . '/' . $destination_path . $ad->resume);
                    }
                }
                
                // Save Resume on the server
                $file->move($full_destination_path, $new_filename);
                
                // Ad Resume in database
                $ad->resume = $new_filename;
                $ad->save();
            }
        }
        
        
        $country_code = strtoupper($this->country->get('code'));
        if ($this->countries->has($country_code)) {
            $urlPath =  $this->lang->get('abbr') . '/' . slugify($ad->title) . '/' . $ad->id . '.html';
        } else {
            $urlPath = '/';
        }
        
        $message = t("Your Ad has been updated.");
        flash()->success($message);
        
        return redirect($urlPath);
    }

    /**
     * @param $adId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function success($adId)
    {
        if (!session('success')) {
            return redirect($this->lang->get('abbr') . '/account/myads');
        }
        
        return view('ad.update.success');
    }
}
