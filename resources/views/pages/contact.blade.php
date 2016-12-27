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
<?php
// Get city for Google Maps
$city = \App\Larapen\Models\City::where('country_code', $country->get('code'))->orderBy('population', 'desc')->first();
?>
@section('javascript-top')
	@parent
	<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemaps.key') }}" type="text/javascript"></script>
@endsection

@section('search')
	@parent
	@include('pages.inc.contact-intro')
@endsection

@section('content')
	<div class="main-container">
		<div class="container">
			<div class="row clearfix">


				@if (count($errors) > 0)
					<div class="col-lg-12">
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<h5><strong>{{ t('Oops ! An error has occurred. Please correct the red fields in the form') }}</strong></h5>
							<ul class="list list-check">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
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


				<div class="col-md-4">
					<div class="contact_info">
						<h5 class="list-title gray"><strong>{{ trans('page.Contact Us') }}</strong></h5>

						<div class="contact-info ">
							<div class="address">
								<div style="margin-bottom: 20px;">
									<p>{!! TextToImage::make(config('settings.app_email'), IMAGETYPE_PNG, ['color' => '#000000']) !!}</p>
								</div>
								<div>
									<p><strong> <a href="{{ lurl(trans('routes.login')) }}">{{ trans('page.Login Area') }}</a></strong></p>
									<p><strong> <a href="{{ lurl(trans('routes.faq')) }}">{{ trans('page.Knowledge Base') }}</a></strong></p>
								</div>
							</div>
						</div>

						<div class="social-list">
							<a href="{{ config('settings.twitter_url') }}" target="_blank"><i class="fa fa-twitter fa-lg "></i></a>
							<a href="{{ config('settings.facebook_page_url') }}" target="_blank"><i class="fa fa-facebook fa-lg "></i></a>
							<!--<a href="#"><i class="fa fa-google-plus fa-lg "></i></a>-->
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="contact-form">
						<h5 class="list-title gray"><strong>{{ trans('page.Contact Us') }}</strong></h5>

						<form class="form-horizontal" method="post" action="{{ lurl(trans('routes.contact')) }}">
							{!! csrf_field() !!}
							<fieldset>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group required <?php echo ($errors->has('first_name')) ? 'has-error' : ''; ?>">
											<div class="col-md-12">
												<input id="first_name" name="first_name" type="text" placeholder="{{ t('First Name') }}"
													   class="form-control" value="{{ old('first_name') }}">
											</div>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group required <?php echo ($errors->has('last_name')) ? 'has-error' : ''; ?>">
											<div class="col-md-12">
												<input id="last_name" name="last_name" type="text" placeholder="{{ t('Last Name') }}"
													   class="form-control" value="{{ old('last_name') }}">
											</div>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group required <?php echo ($errors->has('company_name')) ? 'has-error' : ''; ?>">
											<div class="col-md-12">
												<input id="company_name" name="company_name" type="text" placeholder="{{ t('Company Name') }}"
													   class="form-control" value="{{ old('company_name') }}">
											</div>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group required <?php echo ($errors->has('email')) ? 'has-error' : ''; ?>">
											<div class="col-md-12">
												<input id="email" name="email" type="text" placeholder="{{ t('Email Address') }}" class="form-control"
													   value="{{ old('email') }}">
											</div>
										</div>
									</div>

									<div class="col-lg-12">
										<div class="form-group required <?php echo ($errors->has('message')) ? 'has-error' : ''; ?>">
											<div class="col-md-12">
												<textarea class="form-control" id="message" name="message" placeholder="{{ t('Message') }}"
														  rows="7">{{ old('message') }}</textarea>
											</div>
										</div>

										<!-- Captcha -->
										@if (config('settings.activation_recaptcha'))
											<div class="form-group required <?php echo ($errors->has('g-recaptcha-response')) ? 'has-error' : ''; ?>">
												<div class="col-md-12 control-label" for="g-recaptcha-response">
													{!! Recaptcha::render(['lang' => $lang->get('abbr')]) !!}
												</div>
											</div>
										@endif

										<div class="form-group">
											<div class="col-md-12 ">
												<button type="submit" class="btn btn-primary btn-lg">{{ t('Submit') }}</button>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('javascript')
	@parent
	<script src="{{ url('assets/js/form-validation.js') }}"></script>
	<script>
		$(document).ready(function () {
			genGoogleMaps(
					'<?php echo config('services.googlemaps.key'); ?>',
					'<?php echo (!is_null($city)) ? $city->name . ', ' . $country->get('name') : $country->get('name') ?>',
					'<?php echo $lang->get('abbr'); ?>'
			);
		})
	</script>
@endsection
