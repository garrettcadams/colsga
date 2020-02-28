<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsListingBeingReviewed implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$this->msg = esc_html__('Your listing is being reviewed by our support staff. Please be patient and wait for an email from us.', 'wiloke-listing-tools');

		$postStatus = isset($aOptions['postStatus']) ? $aOptions['postStatus'] : get_post_status($aOptions['postID']);

		return $postStatus != 'pending';
	}
}