<?php
/**
 * Created by PhpStorm.
 * User: pirates
 * Date: 11/4/17
 * Time: 8:59 PM
 */

namespace WilokeListingTools\Framework\Routing;


interface InterfaceMiddleware {

	/**
	 * Handle incoming request
	 *
	 * @param array $aRequest
	 * @param \Closure $next
	 *
	 * @return mixed
	 */
	public function handle(array $aOptions);
}