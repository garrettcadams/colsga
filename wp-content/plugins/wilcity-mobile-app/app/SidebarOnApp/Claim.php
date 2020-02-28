<?php

namespace WILCITY_APP\SidebarOnApp;


class Claim {
	public function __construct() {
		add_filter('wilcity/mobile/sidebar/claim', array($this, 'render'), 10, 2);
	}

	public function render($post, $aAtts){
		return '';
		$status = \WilokeListingTools\Framework\Helpers\GetSettings::getPostMeta($post->ID, 'claim_status');
		if ( $status == 'not_claim' ){
			return false;
		}
	}
}