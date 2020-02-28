<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsPublishingListing implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$this->msg = esc_html__('This article does not exists', 'wiloke-listing-tools');

		if ( !isset($aOptions['postID']) || empty($aOptions['postID']) ){
			return false;
		}

		return get_post_status($aOptions['postID']) == 'publish' && get_post_type($aOptions['postID']) == 'listing';
	}
}