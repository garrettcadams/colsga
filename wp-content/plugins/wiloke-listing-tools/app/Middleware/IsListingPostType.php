<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsListingPostType implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$this->msg = esc_html__('You do not have permission to access this page', WILOKE_LISTING_DOMAIN);

		if ( !isset($aOptions['postID']) || empty($aOptions['postID']) ){
			return false;
		}

		$aTypes = General::getPostTypeKeys(false, false);
		$postType = get_post_type($aOptions['postID']);
		return in_array($postType, $aTypes);
	}
}