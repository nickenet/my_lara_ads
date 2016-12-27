<?php
/**
 * LaraClassified - Geo Classified Ads Software
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

namespace App\Larapen\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class ReviewedScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
	 * @param Builder $builder
	 * @param Model $model
	 * @return $this|Builder
	 */
    public function apply(Builder $builder, Model $model)
    {
        if (Request::segment(1) == config('backpack.base.route_prefix', 'admin')) {
            return $builder;
        }

        if (config('settings.ads_review_activation')) {
            return $builder->where('reviewed', 1);
        }
    
        return $builder;
    }
}
