<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsPublishedPost implements InterfaceMiddleware {
	public $msg;
	public function handle( array $aOptions ) {
		$this->msg = esc_html__('Oops! This post does not exist or it has not published yet.', 'wiloke-listing-tools');

		if ( !isset($aOptions['postID']) || empty($aOptions['postID']) ){
			return false;
		}

		if ( (get_post_field('post_status', $aOptions['postID']) != 'publish') ){
			return false;
		}

		return true;
	}
}