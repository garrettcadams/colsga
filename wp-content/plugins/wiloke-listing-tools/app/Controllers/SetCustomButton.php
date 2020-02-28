<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetCustomButton {
	private function setCustomButtonToListing($listingID, $aButtonSettings){
		global $wpdb;

		if ( isset($aButtonSettings['button_link']) && !empty($aButtonSettings['button_link']) ){
			SetSettings::setPostMeta($listingID, 'button_link', $wpdb->_real_escape(trim($aButtonSettings['button_link'])));
		}else{
			SetSettings::deletePostMeta($listingID, 'button_link');
		}

		if ( isset($aButtonSettings['button_icon']) && !empty($aButtonSettings['button_icon']) ){
			SetSettings::setPostMeta($listingID, 'button_icon', $wpdb->_real_escape(trim($aButtonSettings['button_icon'])));
		}

		if ( isset($aButtonSettings['button_name']) && !empty($aButtonSettings['button_name']) ){
			SetSettings::setPostMeta($listingID, 'button_name', $wpdb->_real_escape(trim($aButtonSettings['button_name'])));
		}
	}
}