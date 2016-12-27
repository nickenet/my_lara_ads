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
@section('content')
	<div class="main-container">
		<div class="container">
			<div class="row">

				@if (count($errors) > 0)
					<div class="col-lg-12">
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
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


				@if (config('settings.activation_social_login'))
					<div class="container text-center" style="margin-bottom: 30px;">
						<div class="row">
							<div class="btn btn-fb" style="width: 194px; margin-right: 1px;">
								<a href="{{ lurl('auth/facebook') }}" class="btn-fb"><i class="icon-facebook"></i> Facebook</a>
							</div>
							<div class="btn btn-danger" style="width: 194px; margin-left: 1px;">
								<a href="{{ lurl('auth/google') }}" class="btn-danger"><i class="icon-googleplus-rect"></i> Google+</a>
							</div>
						</div>
					</div>
				@endif


				<div class="col-sm-5 login-box">
					<form id="loginForm" role="form" method="POST" action="{{ lurl('login') }}">
						{!! csrf_field() !!}
						<div class="panel panel-default">
							<div class="panel-intro text-center">
								<h2 class="logo-title">
									<strong><span class="logo-icon"></span>{{ t('Log in') }}</strong>
								</h2>
							</div>
							<div class="panel-body">
								<div class="form-group <?php echo ($errors->has('email')) ? 'has-error' : ''; ?>">
									<label for="email" class="control-label">{{ t('Email Address') }}:</label>
									<div class="input-icon"><i class="icon-user fa"></i>
										<input id="email" name="email" type="text" placeholder="{{ t('Email Address') }}" class="form-control email"
											   value="{{ (session('email')) ? session('email') : old('email') }}">
									</div>
								</div>
								<div class="form-group <?php echo ($errors->has('password')) ? 'has-error' : ''; ?>">
									<label for="password" class="control-label">{{ t('Password') }}:</label>
									<div class="input-icon"><i class="icon-lock fa"></i>
										<input id="password" name="password" type="password" class="form-control" placeholder="{{ t('Password') }}">
									</div>
								</div>
								<div class="form-group">
									<button id="loginBtn" class="btn btn-primary btn-block"> {{ t('Login') }} </button>
								</div>
							</div>
							<div class="panel-footer">
								<label class="checkbox pull-left" style="padding-left: 20px;">
									<input type="checkbox" value="1" name="remember" id="remember"> {{ t('Keep me logged in') }} </label>
								<p class="text-center pull-right"><a href="<?php echo lurl('password/reset'); ?>"> {{ t('Lost your password?') }} </a>
								</p>
								<div style=" clear:both"></div>
							</div>
						</div>
					</form>

					<div class="login-box-btm text-center">
						<p> {{ t('Don\'t have an account?') }} <br>
							<a href="<?php echo lurl(trans('routes.signup')); ?>"><strong>{{ t('Sign Up') }} !</strong> </a></p>
					</div>
				</div>

				<?php /*@include('layouts.inc.social.horizontal')*/ ?>
			</div>
		</div>
	</div>
@endsection

@section('javascript')
	@parent

	<script language="javascript">
		$(document).ready(function () {
			$("#loginBtn").click(function () {
				$("#loginForm").submit();
				return false;
			});
		});
	</script>
@endsection
