{{--
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
--}}
@extends('layouts.master')

@section('search')
	@parent
@endsection

@section('content')
	<div class="main-container inner-page">
		<div class="container">
			<div class="section-content">
				<div class="row">

					<h1 class="text-center title-1"><strong>{{ trans('page.Privacy') }}</strong></h1>
					<hr class="center-block small text-hr">

					<div class="col-md-12 page-content">
						<div class="inner-box relative">
							<div class="row">
								<div class="col-sm-12 page-content">
									<div class="text-content text-left">
										{!! trans('page.privacy_content', ['app_name' => mb_ucfirst(config('settings.app_name')), 'domain' => getDomain(), 'country' => $country->get('name')]) !!}
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
@endsection

@section('info')
@endsection

@section('javascript')
	@parent
@endsection
