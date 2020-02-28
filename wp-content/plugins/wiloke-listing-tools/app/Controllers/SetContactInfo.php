<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetContactInfo {
	protected function setContactInfo(){
		if ( !empty($this->phone) ){
			SetSettings::setPostMeta($this->listingID, 'phone', $this->phone);
		}else{
			SetSettings::deletePostMeta($this->listingID, 'phone');
		}

		if ( !empty($this->website) ){
			SetSettings::setPostMeta($this->listingID, 'website', $this->website);
		}else{
			SetSettings::deletePostMeta($this->listingID, 'website');
		}

		if ( !empty($this->email) ){
			SetSettings::setPostMeta($this->listingID, 'email', $this->email);
		}else{
			SetSettings::deletePostMeta($this->listingID, 'email');
		}
	}
}