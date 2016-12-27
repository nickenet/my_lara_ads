@if (Auth::check())
	<?php
	// Get site mini stats
	if (config('settings.ads_review_activation')) {
		$countUnactivatedAds = \App\Larapen\Models\Ad::where('active', 0)->orWhere('reviewed', 0)->count();
		$countActivatedAds = \App\Larapen\Models\Ad::where('active', 1)->where('reviewed', 1)->count();
	} else {
		$countUnactivatedAds = \App\Larapen\Models\Ad::where('active', 0)->count();
		$countActivatedAds = \App\Larapen\Models\Ad::where('active', 1)->count();
	}
	$countUnactivatedUsers = \App\Larapen\Models\User::where('is_admin', 0)->where('active', 0)->count();
	$countActivatedUsers = \App\Larapen\Models\User::where('is_admin', 0)->where('active', 1)->count();
	?>
	<!-- Left side column. contains the sidebar -->
	<aside class="main-sidebar">
		<!-- sidebar: style can be found in sidebar.less -->
		<section class="sidebar">

			<!-- Sidebar user panel -->
			<div class="user-panel">
				<div class="pull-left image">
					<img src="{{ Gravatar::fallback(url('images/user.jpg'))->get(Auth::user()->email) }}" class="img-circle" alt="User Image">
				</div>
				<div class="pull-left info">
					<p>{{ Auth::user()->name }}</p>
					<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
				</div>
			</div>

			<!-- sidebar menu: : style can be found in sidebar.less -->
			<ul class="sidebar-menu">
				<li class="header">{{ trans('backpack::base.administration') }}</li>
				<!-- ================================================ -->
				<!-- ==== Recommended place for admin menu items ==== -->
				<!-- ================================================ -->
				<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/dashboard') }}"><i class="fa fa-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>

				<li class="treeview">
					<a href="#"><i class="fa fa-table"></i><span>Ads</span><i class="fa fa-angle-left pull-right"></i></a>
					<ul class="treeview-menu">
						<li>
							<a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/ad') }}">
								<i class="fa fa-table"></i> List
								<span class="pull-right-container">
									<small class="label pull-right bg-green">{{ $countActivatedAds }}</small>
									<small class="label pull-right bg-red">{{ $countUnactivatedAds }}</small>
								</span>
							</a>
						</li>
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/category') }}"><i class="fa fa-folder"></i> Categories</a></li>
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/picture') }}"><i class="fa fa-picture-o"></i> Pictures</a></li>
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/item_type') }}"><i class="fa fa-cog"></i> <span>Type</span></a></li>
					</ul>
				</li>

				<li class="treeview">
					<a href="#"><i class="fa fa-users"></i> <span>Users</span> <i class="fa fa-angle-left pull-right"></i></a>
					<ul class="treeview-menu">
						<li>
							<a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/user') }}">
								<i class="fa fa-table"></i> List
								<span class="pull-right-container">
									<small class="label pull-right bg-green">{{ $countActivatedUsers }}</small>
									<small class="label pull-right bg-red">{{ $countUnactivatedUsers }}</small>
								</span>
							</a>
						</li>
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/gender') }}"><i class="fa fa-venus-mars"></i> Title</a></li>
					</ul>
				</li>

				<li class="treeview">
					<a href="#"><i class="fa fa-usd"></i> <span>Payments</span> <i class="fa fa-angle-left pull-right"></i></a>
					<ul class="treeview-menu">
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/payment') }}"><i class="fa fa-table"></i> <span>List</span></a></li>
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/pack') }}"><i class="fa fa-pie-chart"></i> <span>Packs</span></a></li>
					</ul>
				</li>

				<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/advertising') }}"><i class="fa fa-life-ring"></i> <span>Advertising</span></a></li>

				<li class="treeview">
					<a href="#"><i class="fa fa-globe"></i> <span>International</span> <i class="fa fa-angle-left pull-right"></i></a>
					<ul class="treeview-menu">
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/language') }}"><i class="fa fa-circle-o"></i> Languages</a></li>
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/country') }}"><i class="fa fa-circle-o"></i> Countries</a></li>
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/currency') }}"><i class="fa fa-circle-o"></i> Currencies</a></li>
						<?php /*<li class="treeview">
						<a href="#"><i class="fa fa-globe"></i> <span>Translations</span> <i class="fa fa-angle-left pull-right"></i></a>
						<ul class="treeview-menu">
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/language/texts') }}"><i class="fa fa-language"></i> Site texts</a></li>
						</ul>
						</li>*/ ?>
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/time_zone') }}"><i class="fa fa-circle-o"></i> TimeZones</a></li>
					</ul>
				</li>

				<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/elfinder') }}"><i class="fa fa-files-o"></i> <span>File manager</span></a></li>
				<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/setting') }}"><i class="fa fa-cog"></i> <span>Settings</span></a></li>
				<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/blacklist') }}"><i class="fa fa-ban"></i> Blacklist</a></li>
				<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/report_type') }}"><i class="fa fa-cog"></i> <span>Report Types</span></a></li>

				<li class="treeview">
					<a href="#"><i class="fa fa-globe"></i> <span>Locations</span> <i class="fa fa-angle-left pull-right"></i></a>
					<ul class="treeview-menu">
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/loc_admin1') }}"><i class="fa fa-circle-o"></i> Loc. admin 1</a></li>
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/loc_admin2') }}"><i class="fa fa-circle-o"></i> Loc. admin 2</a></li>
						<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/city') }}"><i class="fa fa-circle-o"></i> Cities</a></li>
					</ul>
				</li>

				<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/backup') }}"><i class="fa fa-hdd-o"></i> <span>Backups</span></a></li>


				<!-- ======================================= -->
				<li class="header">{{ trans('backpack::base.user') }}</li>
				<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/account') }}"><i class="fa fa-sign-out"></i> <span>My account</span></a></li>
				<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/logout') }}"><i class="fa fa-sign-out"></i> <span>{{ trans('backpack::base.logout') }}</span></a></li>
			</ul>

		</section>
		<!-- /.sidebar -->
	</aside>
@endif
