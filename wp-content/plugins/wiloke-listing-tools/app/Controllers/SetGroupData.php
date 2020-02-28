<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetGroupData {
	public function setGroup(){
		if ( empty($this->aGroupData) ){
			return true;
		}

		foreach ($this->aGroupData as $key => $aData){
			SetSettings::setPostMeta($this->listingID, $key, $aData);
		}
	}
}