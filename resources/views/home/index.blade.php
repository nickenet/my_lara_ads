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

@section('css')
@endsection

@section('search')
	@parent
	@include('home.inc.search')
@endsection

@section('content')
	<div class="main-container" id="homepage">
		<div class="container">
			<div class="row">
				@if (session('message'))
					<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						{{ session('message') }}
					</div>
				@endif

				@if (Session::has('flash_notification.message'))
					<div class="container" style="margin-bottom: -10px; margin-top: -10px;">
						<div class="row">
							<div class="col-lg-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif
			</div>

			@include('home.inc.locations')
			@include('layouts.inc.carousel')
			@include('home.inc.categories')
			@include('home.inc.bottom-info')

		</div>
	</div>
@endsection

@section('modal-location')
	@include('layouts.inc.modal.location')
@endsection

@section('javascript')
	@parent
	<script>
		var stateId = '<?php echo (isset($city)) ? $country->get('code').'.'.$city->subadmin1_code : '0' ?>';
	</script>
	<script type="text/javascript" src="{{ url('assets/js/app/load.cities.js') }}"></script>
@endsection
