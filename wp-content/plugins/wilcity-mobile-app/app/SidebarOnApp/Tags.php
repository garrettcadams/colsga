<?php

namespace WILCITY_APP\SidebarOnApp;


class Tags {
	public function __construct() {
		add_filter('wilcity/mobile/sidebar/tags', array($this, 'render'));
	}

	public function render($post){
		$aInfo = array();
		$aTerms = wp_get_post_terms($post->ID, 'listing_tag');

		if ( empty($aTerms) || is_wp_error($aTerms) ){
			return false;
		}

		foreach ( $aTerms as $order => $oTerm ){
			if ( empty( $oTerm ) || is_wp_error( $oTerm ) ) {
				continue;
			}
			$aInfo[] = \WilokeHelpers::getTermOriginalIcon($oTerm);
			$aInfo[$order]['name'] = $oTerm->name;
			$aInfo[$order]['term_id'] = $oTerm->term_id;
			$aInfo[$order]['slug'] = $oTerm->slug;
		}

		return json_encode($aInfo);
	}
}