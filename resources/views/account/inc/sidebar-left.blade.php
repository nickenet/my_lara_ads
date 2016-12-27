<aside>
	<div class="inner-box">
		<div class="user-panel-sidebar">

			<div class="collapse-box">
				<h5 class="collapse-title no-border">
					@lang('global.My Account')&nbsp;
					<a href="#MyClassified" data-toggle="collapse" class="pull-right"><i class="fa fa-angle-down"></i></a>
				</h5>
				<div class="panel-collapse collapse in" id="MyClassified">
					<ul class="acc-list">
						<li>
							<a{!! ($pagePath=='') ? ' class="active"' : '' !!} href="{{ lurl('account') }}">
							<i class="icon-home"></i> @lang('global.Personal Home')
							</a>
						</li>
					</ul>
				</div>
			</div>
			<!-- /.collapse-box  -->

			<div class="collapse-box">
				<h5 class="collapse-title">
					@lang('global.My Ads')&nbsp;
					<a href="#MyAds" data-toggle="collapse" class="pull-right"><i class="fa fa-angle-down"></i></a>
				</h5>
				<div class="panel-collapse collapse in" id="MyAds">
					<ul class="acc-list">
						<li>
							<a{!! ($pagePath=='myads') ? ' class="active"' : '' !!} href="{{ lurl('account/myads') }}">
							<i class="icon-docs"></i> @lang('global.My ads')&nbsp;
							<span class="badge">{{ isset($count_my_ads) ? $count_my_ads : 0 }}</span>
							</a>
						</li>
						<li>
							<a{!! ($pagePath=='favourite') ? ' class="active"' : '' !!} href="{{ lurl('account/favourite') }}">
							<i class="icon-heart"></i> @lang('global.Favourite ads')&nbsp;
							<span class="badge">{{ isset($count_favourite_ads) ? $count_favourite_ads : 0 }}</span>
							</a>
						</li>
						<li>
							<a{!! ($pagePath=='saved-search') ? ' class="active"' : '' !!} href="{{ lurl('account/saved-search') }}">
							<i class="icon-star-circled"></i> @lang('global.Saved search')&nbsp;
							<span class="badge">{{ isset($count_saved_search) ? $count_saved_search : 0 }}</span>
							</a>
						</li>
						<li>
							<a{!! ($pagePath=='archived') ? ' class="active"' : '' !!} href="{{ lurl('account/archived') }}">
							<i class="icon-folder-close"></i> @lang('global.Archived ads')&nbsp;
							<span class="badge">{{ isset($count_archived_ads) ? $count_archived_ads : 0 }}</span>
							</a>
						</li>
						<li>
							<a{!! ($pagePath=='pending-approval') ? ' class="active"' : '' !!} href="{{ lurl('account/pending-approval') }}
							">
							<i class="icon-hourglass"></i> @lang('global.Pending approval')&nbsp;
							<span class="badge">{{ isset($count_pending_ads) ? $count_pending_ads : 0 }}</span>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<!-- /.collapse-box  -->

			<div class="collapse-box">
				<h5 class="collapse-title">
					@lang('global.Terminate Account')&nbsp;
					<a href="#TerminateAccount" data-toggle="collapse" class="pull-right"><i class="fa fa-angle-down"></i></a>
				</h5>
				<div class="panel-collapse collapse in" id="TerminateAccount">
					<ul class="acc-list">
						<li>
							<a{!! ($pagePath=='close') ? ' class="active"' : '' !!} href="{{ lurl('account/close') }}">
							<i class="icon-cancel-circled "></i> @lang('global.Close account')
							</a>
						</li>
					</ul>
				</div>
			</div>
			<!-- /.collapse-box  -->

		</div>
	</div>
	<!-- /.inner-box  -->
</aside>