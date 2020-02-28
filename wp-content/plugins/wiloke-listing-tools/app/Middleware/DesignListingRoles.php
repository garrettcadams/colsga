<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class DesignListingRoles implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$this->msg = esc_html__('You do not permission to access this page', 'wiloke-listing-tools');

		if ( !is_user_logged_in() ){
			return false;
		}

		if ( !isset($aOptions['postID']) || empty($aOptions['postID']) ){
			return false;
		}

		if ( !current_user_can('administrator') && get_post_field('post_author', $aOptions['postID']) != get_current_user_id() ){
			return false;
		}

		return true;
	}
}