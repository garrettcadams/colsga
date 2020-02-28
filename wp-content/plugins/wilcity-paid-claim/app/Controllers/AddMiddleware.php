<?php

namespace WilcityPaidClaim\Controllers;


class AddMiddleware {
	public function __construct() {
		add_filter('wiloke-listing-tools/config/middleware', array($this, 'addMiddleware'));
	}

	public function addMiddleware($aMiddleware){
		$aPlus = wilokeListingToolsRepository()->setConfigDir(WILCITY_PC_DIR . 'config/')->get('middleware');
		return array_merge($aMiddleware, $aPlus);
	}
}