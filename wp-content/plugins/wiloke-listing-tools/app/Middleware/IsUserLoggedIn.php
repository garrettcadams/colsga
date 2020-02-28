<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;
use WilokeListingTools\Frontend\User;

class IsUserLoggedIn implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$this->msg = esc_html__('You do not have permission to access this page', 'wiloke-listing-tools');

		$status = is_user_logged_in();

		if ( !$status ){
			$status = User::isUserLoggedIn(true);
		}

		if ( $status === false ){
			return false;
		}

		return true;
	}
}