@if (config('settings.show_country_svgmap'))
	@if (file_exists(public_path('images/maps/' . strtolower($country->get('code')) . '.svg')))

		<div id="countrymap" class="col-sm-3 page-sidebar col-thin-left">&nbsp;</div>

		@section('javascript')
			@parent
			<script src="{{ url('assets/plugins/twism/jquery.twism.js') }}"></script>
			<script>
				$(document).ready(function () {
					/* SVG Maps */
					@if(config('settings.show_country_svgmap'))
						$('#countrymap').twism("create",
						{
							map: "custom",
							customMap: '<?php echo 'images/maps/' . strtolower($country->get('code')) . '.svg'; ?>',
							backgroundColor: 'transparent',
							border: '#228B22',
							hoverBorder: "#228B22",
							borderWidth: 4,
							color: '#cae7ca',
							width: '300px',
							height: '300px',
							click: function(region) {
								if (typeof region == "undefined") {
									return false;
								}
								region = rawurlencode(region);
								var searchPage = '<?php echo lurl(trans('routes.v-search', ['countryCode' => $country->get('icode')])); ?>';
								window.location.replace(searchPage + '?r=' + region);
								window.location.href = searchPage + '?r=' + region;
							},
							hover: function(region_id) {
								if (typeof region_id == "undefined") {
									return false;
								}
								var selectedIdObj = document.getElementById(region_id);
								if (typeof selectedIdObj == "undefined") {
									return false;
								}
								selectedIdObj.style.fill = '#228B22';
								return;
							},
							unhover: function(region_id) {
								if (typeof region_id == "undefined") {
									return false;
								}
								var selectedIdObj = document.getElementById(region_id);
								if (typeof selectedIdObj == "undefined") {
									return false;
								}
								selectedIdObj.style.fill = '#cae7ca';
								return;
							}
							/* @fixme: hoverColor attribute doesn't work */
							/* hoverColor: '#2ecc71' */
						});
					@endif
				});

				function rawurlencode(str) {
					str = (str + '').toString();
					return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A');
				}
			</script>
		@endsection

	@endif
@endif