<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetMyRoom {
	private function setMyRoom(){
		if ( empty($this->myRoom) ){
			SetSettings::deletePostMeta($this->listingID, 'my_room');
		}else{
			SetSettings::setPostMeta($this->listingID, 'my_room', $this->myRoom);
		}
	}
}