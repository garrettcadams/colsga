<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsTemporaryHiddenPost implements InterfaceMiddleware {
	public $msg;
	public function handle( array $aOptions ) {
		$this->msg = esc_html__('You do not have permission to access this area.', 'wiloke-listing-tools');

		if ( !isset($aOptions['postID']) || empty($aOptions['postID']) ){
			return false;
		}

		if ( (get_post_field('post_status', $aOptions['postID']) != 'temporary_close') ){
			return false;
		}

		return true;
	}
}