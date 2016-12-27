<!-- =========================================================== -->

<!-- Small boxes (Stat box) -->
<div class="row">
	<div class="col-lg-3 col-xs-6">
		<!-- small box -->
		<div class="small-box bg-yellow">
			<div class="inner">
				<h3>{{ $countUnactivatedAds }}</h3>

				<p>{{ t('Unactivated ads') }}</p>
			</div>
			<div class="icon">
				<i class="fa fa-edit"></i>
			</div>
			<a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/ad?active=0') }}" class="small-box-footer">
				{{ t('View more') }} <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<!-- ./col -->

	<div class="col-lg-3 col-xs-6">
		<!-- small box -->
		<div class="small-box bg-aqua">
			<div class="inner">
				<h3>{{ $countActivatedAds }}</h3>

				<p>{{ t('Activated ads') }}</p>
			</div>
			<div class="icon">
				<i class="fa fa-check-circle-o"></i>
			</div>
			<a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/ad?active=1') }}" class="small-box-footer">
				{{ t('View more') }} <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<!-- ./col -->

	<div class="col-lg-3 col-xs-6">
		<!-- small box -->
		<div class="small-box bg-green">
			<div class="inner">
				<h3>{{ $countUsers }}</h3>

				<p>{{ t('User Registrations') }}</p>
			</div>
			<div class="icon">
				<i class="ion ion-person-add"></i>
			</div>
			<a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/user') }}" class="small-box-footer">
				{{ t('View more') }} <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<!-- ./col -->

	<div class="col-lg-3 col-xs-6">
		<!-- small box -->
		<div class="small-box bg-red">
			<div class="inner">
				<h3>{{ $countCountries }}</h3>

				<p>{{ t('Activated countries') }}</p>
			</div>
			<div class="icon">
				<i class="fa fa-globe"></i>
			</div>
			<a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/country') }}" class="small-box-footer">
				{{ t('View more') }} <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<!-- ./col -->
</div>
<!-- /.row -->

<!-- =========================================================== -->
