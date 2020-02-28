<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsAllowEditingPublishedListing implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$this->msg = esc_html__('We do not allow editing a published listing', 'wiloke-listing-tools');

		$status = GetWilokeSubmission::getField('published_listing_editable');
		return $status != 'not_allow';
	}
}