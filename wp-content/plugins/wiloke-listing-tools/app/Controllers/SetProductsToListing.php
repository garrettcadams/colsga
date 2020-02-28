<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetProductsToListing {
	private function setProductsToListing(){
		if ( empty($this->aMyProducts) ){
			SetSettings::deletePostMeta($this->listingID, 'my_products');
		}else{
			SetSettings::setPostMeta($this->listingID, 'my_products', $this->aMyProducts);
		}
	}
}