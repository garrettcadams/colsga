<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetListingBelongsToPlanID {
	protected function setListingBelongsTo(){
		$oldPlan = GetSettings::getPostMeta($this->listingID, 'belongs_to');
		if ( $oldPlan != $this->planID ){
			SetSettings::setPostMeta($this->listingID, 'belongs_to', $this->planID);
		}
	}
}