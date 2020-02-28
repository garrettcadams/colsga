<?php

namespace WILCITY_APP\SidebarOnApp;

use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Frontend\PriceRange as PR;

class PriceRange {
	public function __construct() {
		add_filter('wilcity/mobile/sidebar/price_range', array($this, 'render'), 10, 2);
	}

	public function render($post, $aAtts){
		$symbol = PR::getSymbol($post);
		if ( !$symbol ){
			return '';
		}

		$maximumPrice = GetSettings::getPostMeta($post->ID, 'maximum_price');
		$minimumPrice = GetSettings::getPostMeta($post->ID, 'minimum_price');

		if ( empty($maximumPrice) && empty($minimumPrice) ){
			return false;
		}

		$currencyCode   = GetWilokeSubmission::getField('currency_code');
		$currencySymbol =  GetWilokeSubmission::getSymbol($currencyCode);
		$desc = GetSettings::getPostMeta($post->ID, 'price_range_desc');
		$currencyPos = GetWilokeSubmission::getField('currency_position');

		return json_encode(array(
			'minPrice'      => $minimumPrice,
			'maxPrice'      => $maximumPrice,
			'currencyCode'  => $currencyCode,
			'currencySymbol'=> $currencySymbol,
			'desc'          => $desc,
			'currencyPos'   => $currencyPos
		));
	}
}