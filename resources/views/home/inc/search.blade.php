<div class="container">
	<div class="intro">
		<div class="dtable hw100">
			<div class="dtable-cell hw100">
				<div class="container text-center">
					<?php
					/*
					<!--<h1 class="intro-title animated fadeInDown"> {{-- t('Daily good deals') --}} </h1>-->
					<p class="sub animateme fittext3 animated fadeIn">
						{!! t('Sell and Buy products and services on :app_name in Minutes', ['app_name' => mb_ucfirst(config('settings.app_name'))]) !!}
					</p>
					*/
					?>
					<div class="row search-row fadeInUp">
						<form id="seach" name="search" action="{{ lurl(trans('routes.v-search', ['countryCode' => $country->get('icode')])) }}"
							  method="GET">
							<div class="col-lg-5 col-sm-5 search-col relative">
								<i class="icon-docs icon-append"></i>
								<input type="text" name="q" class="form-control keyword has-icon" placeholder="{{ t('What?') }}" value="">
							</div>
							<div class="col-lg-5 col-sm-5 search-col relative locationicon">
								<i class="icon-location-2 icon-append"></i>
								<input type="hidden" id="l_search" name="l" value="">
								<input type="text" id="loc_search" name="location" class="form-control locinput input-rel searchtag-input has-icon"
									   placeholder="{{ t('Where?') }}" value="">
							</div>
							<div class="col-lg-2 col-sm-2 search-col">
								<button class="btn btn-primary btn-search btn-block"><i class="icon-search"></i> <strong>{{ t('Find') }}</strong>
								</button>
							</div>
							{!! csrf_field() !!}
						</form>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>