<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsReviewExists implements InterfaceMiddleware {
	public $msg;
	public function handle( array $aOptions ) {
		$this->msg = esc_html__('The review does not exist', 'wiloke-listing-tools');

		if ( !isset($aOptions['reviewID']) || empty($aOptions['reviewID']) || get_post_status($aOptions['reviewID']) !== 'publish' ){
			return false;
		}

		return true;
	}
}