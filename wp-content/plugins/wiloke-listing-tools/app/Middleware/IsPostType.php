<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsPostType implements InterfaceMiddleware{
	public $msg;
	public function handle( array $aOptions ) {
		$this->msg = esc_html__('This post type does not exist', 'wiloke-listing-tools');

		if ( get_post_type($aOptions['postID']) != $aOptions['postType'] ){
			return false;
		}
		return true;
	}
}