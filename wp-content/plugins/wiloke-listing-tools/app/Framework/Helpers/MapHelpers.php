<?php

namespace WilokeListingTools\Framework\Helpers;


class MapHelpers {
	public static function getMapMarker($postID){
		$oTerm = \WilokeHelpers::getTermByPostID($postID, 'listing_cat');
		if ( !$oTerm ){
			return false;
		}
		$iconURL = GetSettings::getTermMeta($oTerm->term_id, 'icon_img');
		return $iconURL ? $iconURL : get_template_directory_uri() . '/assets/img/wilcity.png';
	}
}