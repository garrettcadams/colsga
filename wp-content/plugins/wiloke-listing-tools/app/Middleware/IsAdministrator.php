<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsAdministrator implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		if ( !current_user_can('administrator') ){
			$this->msg = esc_html__('Whoops! You do not have permission to access this page', 'wiloke-listing-tools');
			return false;
		}

		return true;
	}
}