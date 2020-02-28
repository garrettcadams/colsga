<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsOwnerOfPost implements InterfaceMiddleware {
	public $msg;
	public function handle( array $aOptions ) {
		$this->msg = esc_html__('You do not have permission to access this page', 'wiloke-listing-tools');

		if ( get_post_field('post_author', $aOptions['postID']) != $aOptions['userID'] ){
			return false;
		}

		return true;
	}
}