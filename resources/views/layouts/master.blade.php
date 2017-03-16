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
<?php
	$fullUrl = url(\Illuminate\Support\Facades\Request::getRequestUri());
?>
<!DOCTYPE html>
<html lang="{{ ($lang->has('abbr')) ? $lang->get('abbr') : 'en' }}">
<head>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	@if (isset($lang) and isset($country) and $country->has('lang'))
		@if ($lang->get('abbr') != $country->get('lang')->get('abbr'))
			<meta name="robots" content="noindex">
			<meta name="googlebot" content="noindex">
		@endif
	@endif
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="apple-mobile-web-app-title" content="{{ config('settings.app_name') }}">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ url('/assets/ico/apple-touch-icon-144-precomposed.png') }}">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{ url('/assets/ico/apple-touch-icon-114-precomposed.png') }}">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{ url('/assets/ico/apple-touch-icon-72-precomposed.png') }}">
	<link rel="apple-touch-icon-precomposed" href="{{ url('/assets/ico/apple-touch-icon-57-precomposed.png') }}">
	<link rel="shortcut icon" href="{{ url('/assets/ico/favicon.png') }}">
	<title>{{ MetaTag::get('title') }}</title>
	{!! MetaTag::tag('description') !!}
	<link rel="canonical" href="{{ $fullUrl }}"/>
	@foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
		@if (strtolower($localeCode) != strtolower($lang->get('abbr')))
			<link rel="alternate" href="{{ LaravelLocalization::getLocalizedURL($localeCode) }}" hreflang="{{ strtolower($localeCode) }}"/>
		@endif
	@endforeach
	@if (count($dns_prefetch) > 0)
		@foreach($dns_prefetch as $dns)
			<link rel="dns-prefetch" href="{{ $dns }}">
		@endforeach
	@endif
	@if (config('services.facebook.client_id'))
		<meta property="fb:app_id" content="{{ config('services.facebook.client_id') }}"/>
	@endif
	{!! $og->renderTags() !!}
	{!! MetaTag::twitterCard() !!}
	@if (config('settings.google_site_verification'))
		<meta name="google-site-verification" content="{{ config('settings.google_site_verification') }}"/>
	@endif
	@if (config('settings.msvalidate'))
		<meta name="msvalidate.01" content="{{ config('settings.msvalidate') }}" />
	@endif
	@if (config('settings.alexa_verify_id'))
		<meta name="alexaVerifyID" content="{{ config('settings.alexa_verify_id') }}"/>
	@endif
	<link href="{{ url('/assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ url('/assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
	<link href="{{ url('/assets/css/style.css') . '?v=' . time() }}" rel="stylesheet">
	<link href="{{ url('/assets/css/style/default.min.css') . '?v=' . time() }}" rel="stylesheet">
	@if (config('app.theme'))
		<link href="{{ url('/assets/css/style/' . config('app.theme') . '.css') . '?v=' . time() }}" rel="stylesheet">
	@endif
	<link href="{{ url('/assets/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css"/>
	<link href="{{ url('/assets/css/owl.carousel.css') }}" rel="stylesheet">
	<link href="{{ url('/assets/css/owl.theme.css') }}" rel="stylesheet">
	<link href="{{ url('/assets/css/flags/flags.css') }}" rel="stylesheet">
	@if (config('settings.custom_css'))
	<style type="text/css">
		<?php
		$custom_css = config('settings.custom_css');
		$custom_css = preg_replace('/<[^>]+>/i', '', $custom_css);

		echo $custom_css . "\n";
		?>
	</style>
	@endif
	@section('css')
	@show
	@section('javascript-top')
	@show


	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->

	<script>
		paceOptions = {
			elements: true
		};
	</script>
	<script src="{{ url('/assets/js/pace.min.js') }}"></script>
</head>
<body>
    <h1></h1>
<div id="wrapper">

	@section('header')
		@if (Auth::check())
			@include('layouts.inc.header', ['user' => $user])
		@else
			@include('layouts.inc.header')
		@endif
	@show

	@section('search')
	@show

	@if (isset($site_country_info))
		<div class="container" style="margin-bottom: -30px; margin-top: 20px;">
			<div class="row">
				<div class="col-lg-12">
					<div class="alert alert-warning">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						{!! $site_country_info !!}
					</div>
				</div>
			</div>
		</div>
	@endif

	@yield('content')

	@section('info')
	@show

	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				@section('footer')
					@include('layouts.inc.footer')
				@show
			</div>
		</div>
	</div>

</div>

@section('modal-location')
@show
@section('modal-abuse')
@show
@section('modal-message')
@show

@section('javascript')
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"> </script> -->
<script src="{{ url('/assets/js/jquery/1.10.1/jquery-1.10.1.js') }}"></script>
<script src="{{ url('/assets/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ url('/assets/plugins/retina/1.3.0/retina.js') }}" type="text/javascript"></script>
<script src="{{ url('/assets/js/jquery.matchHeight-min.js') }}"></script>
<script src="{{ url('/assets/plugins/jquery.fs.scroller/jquery.fs.scroller.min.js') }}"></script>
<script src="{{ url('/assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ url('/assets/plugins/SocialShare/SocialShare.min.js') }}"></script>
<script src="{{ url('/assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ url('/assets/js/hideMaxListItem-min.js') }}"></script>
<script language="javascript">
	var siteUrl = '<?php echo url('/'); ?>';
	var languageCode = '<?php echo $lang->get('abbr'); ?>';
	var langLayout = {
		'hideMaxListItems': {
			'moreText': "{{ t('View More') }}",
			'lessText': "{{ t('View Less') }}"
		}
	};
	$(document).ready(function () {
		/* Select Boxes */
		$(".selecter").select2({
			language: '<?php echo $lang->get('abbr'); ?>',
			dropdownAutoWidth: 'true',
			minimumResultsForSearch: Infinity
		});
		/* Searchable Select Boxes */
		$(".sselecter").select2({
			language: '<?php echo $lang->get('abbr'); ?>',
			dropdownAutoWidth: 'true',
		});

		/* Social Share */
		$('.share').ShareLink({
			title: '<?php echo addslashes(MetaTag::get('title')); ?>',
			text: '<?php echo addslashes(MetaTag::get('title')); ?>',
			url: '<?php echo $fullUrl; ?>'
		});
	});
</script>
<script src="{{ url('/assets/js/script.js?time=' . time()) }}"></script>
<script type="text/javascript" src="{{ url('/assets/plugins/autocomplete/jquery.mockjax.js') }}"></script>
<script type="text/javascript" src="{{ url('/assets/plugins/autocomplete/jquery.autocomplete.min.js') }}"></script>
<script type="text/javascript" src="{{ url('/assets/js/app/autocomplete.cities.js') }}"></script>
@show

{!! config('settings.seo_google_analytics') !!}
</body>
</html>