<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;
use WilokeListingTools\Frontend\User;

class BeforeSubmitMessage implements InterfaceMiddleware {
	public $msg = '';

	public function handle( array $aOptions ) {
		if ( !isset($aOptions['receiveID']) || empty($aOptions['receiveID']) || !User::userIDExists($aOptions['receiveID']) ){
			$this->msg = esc_html__('The user does not exists', 'wiloke-listing-tools');
			return false;
		}
		return true;
	}
}