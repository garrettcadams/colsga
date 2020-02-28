<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;

trait GetWilokeToolSettings {
	public function getAvailableFields($isAutoGet=true, $listingType=''){
		if ( $isAutoGet ){
			$listingType = isset($_REQUEST['listing_type']) && !empty($_REQUEST['listing_type']) ? $_REQUEST['listing_type'] : General::getDefaultPostTypeKey(false, true);
		}

		$availableKey = General::getUsedSectionKey($listingType, true);

		return GetSettings::getOptions($availableKey, false, true, true);
	}
}