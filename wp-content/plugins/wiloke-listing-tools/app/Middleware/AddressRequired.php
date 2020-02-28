<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class AddressRequired implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$this->msg = esc_html__('The address field is required', 'wiloke-listing-tools');

		if ( !isset($aOptions['address']) || empty($aOptions['address']) ){
			return false;
		}

		if ( empty($aOptions['address']) || empty($aOptions['address']['lat']) || empty($aOptions['address']['lng']) ){
			return false;
		}

		return true;
	}
}