<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Submission;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsSupportedPostTypeAddListing implements InterfaceMiddleware {
	public $msg;
	public function handle( array $aOptions ) {
		$this->msg = esc_html__('You do not have permission to access this page', 'wiloke-listing-tools');

		$aPostTypes = Submission::getSupportedPostTypes();
		return in_array($aOptions['postType'], $aPostTypes);
	}
}