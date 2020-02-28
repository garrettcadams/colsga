<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Controllers\RegisterLoginController;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class CanRegister implements InterfaceMiddleware {
	public $msg = '';

	public function handle( array $aOptions ) {
		if ( is_user_logged_in() || !RegisterLoginController::canRegister() ){
			$this->msg = sprintf( esc_html__('You are ineligible to register for %s' , 'wiloke-listing-tools'), get_option('blogname'));
			return false;
		}
		return true;
	}
}