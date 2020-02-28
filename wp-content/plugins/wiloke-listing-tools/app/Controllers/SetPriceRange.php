<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetPriceRange {
	protected function setPriceRange(){
		$aArgs = array('price_range', 'price_range_desc', 'minimum_price', 'maximum_price');
		foreach ($aArgs as $priceKey){
			if ( isset($this->aPriceRange[$priceKey]) ){
				SetSettings::setPostMeta($this->listingID, $priceKey, $this->aPriceRange[$priceKey]);
			}else{
				SetSettings::deletePostMeta($this->listingID, $priceKey);
			}
		}
	}
}