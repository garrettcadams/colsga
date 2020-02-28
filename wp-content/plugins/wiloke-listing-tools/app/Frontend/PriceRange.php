<?php

namespace WilokeListingTools\Frontend;


use WilokeListingTools\Framework\Helpers\GetSettings;

class PriceRange {
	public static function getSymbol($post, $type=null){
		$symbol = apply_filters('wilcity/price-range/symbol',  '$', $post);
		if ( empty($type) ){
			$type = GetSettings::getPostMeta($post->ID, 'price_range');
		}
		if ( empty($type) ){
			return false;
		}

		switch ($type){
			case 'nottosay':
				return '';
			case 'cheap':
				return $symbol;
			case 'moderate':
				return $symbol.$symbol;
				break;
			case 'expensive':
				return $symbol.$symbol.$symbol;
				break;
			default:
				return $symbol.$symbol.$symbol.$symbol;
				break;
		}
	}
}