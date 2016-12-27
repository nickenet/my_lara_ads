<?php
/**
 * LaraClassified - Geo Classified Ads Software
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
 */

namespace App\Larapen\Middleware;

use Closure;

class HttpsProtocol
{
	/**
	 * Redirects any non-secure requests to their secure counterparts.
	 *
	 * @param $request
	 * @param Closure $next
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function handle($request, Closure $next)
	{
		if (config('larapen.core.force_https') == true) {
			$request->setTrustedProxies([$request->getClientIp()]);
			if (!$request->secure()) {
				/* $request->server('HTTP_X_FORWARDED_PROTO') != 'https' */
				// Production is not currently secure
				// return redirect()->secure($request->getRequestUri());
			}
		}

		return $next($request);
	}
}
