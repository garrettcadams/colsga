<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetSinglePrice {
	protected function setSinglePrice(){
		if ( empty($this->singlePrice) ){
			SetSettings::deletePostMeta($this->listingID, 'single_price');
		}else{
			SetSettings::setPostMeta($this->listingID, 'single_price', $this->singlePrice);
		}
	}
}