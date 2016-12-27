<div class="row" style="margin-bottom: 30px;">
	<div class="col-lg-12 page-content">
		<div class="inner-box">
			<?php
			if (config('settings.show_country_svgmap') and file_exists(public_path('images/maps/' . strtolower($country->get('code')) . '.svg'))):
				$class_col_sm = 'col-sm-5';
				$class_col_md = 'col-md-12';
			else:
				$class_col_sm = 'col-sm-8';
				$class_col_md = 'col-md-12';
			endif
			?>
			<div class="{{ $class_col_sm }} page-content no-margin no-padding">
				@if (isset($cities))
					<div class="relative" style="text-align: center;">
						<div class="row" style="padding-top: 30px; padding-bottom: 30px; text-align: left;">
							<div class="{{ $class_col_md }}">
								<div>
									<h2 class="title-3">
										<i class="icon-location-2"></i>&nbsp;
										{{ t('Choose a city') }}
									</h2>
									<div class="row" style="padding: 0 10px 0 20px;">
										@foreach ($cities as $key => $items)
											<ul class="cat-list col-xs-4 {{ (count($cities) == $key+1) ? 'cat-list-border' : '' }}">
												@foreach ($items as $k => $city)
													<li>
														@if ($city->id == 999999999)
															<a href="#selectRegion" id="dropdownMenu1" data-toggle="modal">{{ $city->name }}</a>
														@else
															<a href="{{ lurl(trans('routes.v-search-location',
															[
															'countryCode' => $country->get('icode'),
															'city' => slugify($city->name),
															'id' => $city->id
															])) }}">
																{{ $city->name }}
															</a>
														@endif
													</li>
												@endforeach
											</ul>
										@endforeach
									</div>
								</div>
							</div>
						</div>

						<a class="btn btn-lg btn-yellow" href="{{ lurl(trans('routes.create')) }}"
						   style="padding-left: 30px; padding-right: 30px; text-transform: none;">
							{{ t('Post a Free Classified Ad') }}
						</a>

					</div>
				@endif
			</div>

			<?php
			$theme = 'default';
			if (config('app.theme')) {
				if (file_exists(base_path() . '/resources/views/layouts/inc/tools/svgmap/' . config('app.theme') . '.blade.php')) {
					$theme = config('app.theme');
				}
			}
			?>
			@include('layouts.inc.tools.svgmap.' . $theme)


			<div class="col-sm-3 page-sidebar col-thin-left" style="padding: 30px 0 30px 0;">
				<ul class="list list-check">
					<li>{{ t('Sell anything for free') }}</li>
					<li>{{ t('Hundreds of buyers every day') }}</li>
					<li>{{ t('We sponsor your Ad') }}</li>
				</ul>
				<br><br>

				<span class="title-3">
					<a href="{{ lurl(trans('routes.v-search', ['countryCode' => $country->get('icode')])) }}">&raquo; {{ t('View all Ads') }}</a>
				</span>
			</div>

		</div>
	</div>
</div>

