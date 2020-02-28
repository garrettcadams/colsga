<?php
namespace WilokeListingTools\Middleware;

use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsClaimAvailable implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$this->msg = esc_html__('OOps! The listing has been claimed', 'wilcity-paid-claim');

		$claimStatus = GetSettings::getPostMeta($aOptions['postID'], 'claim_status');
		if ( $claimStatus == 'claimed' ){
			return false;
		}

		return true;
	}
}