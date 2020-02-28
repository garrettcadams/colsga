<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsReviewEnabled implements InterfaceMiddleware {
	public $msg;
	public function handle( array $aOptions ) {
		$this->msg = esc_html__('The review is closed', 'wiloke-listing-tools');
		$status = GetSettings::getOptions(General::getReviewKey('toggle', get_post_type($aOptions['postID'])));

		if ( empty($status) || $status == 'disable' ){
			return false;
		}

		return true;
	}
}