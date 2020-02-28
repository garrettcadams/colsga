<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;
use WilokeListingTools\Frontend\User;

class CanSubmissionListing implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		if ( !User::canSubmitListing() ){
			$this->msg = esc_html__('Oops! Sorry, but you do not have permission to submit a listing', 'wiloke-listing-tools');
			return false;
		}

		return true;
	}
}