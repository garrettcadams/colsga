<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetSocialNetworks {
	protected function setSocialNetworks(){
		if ( !empty($this->aSocialNetworks) ){
			SetSettings::setPostMeta($this->listingID, 'social_networks', $this->aSocialNetworks);
		}else{
			SetSettings::deletePostMeta($this->listingID, 'social_networks');
		}
	}
}