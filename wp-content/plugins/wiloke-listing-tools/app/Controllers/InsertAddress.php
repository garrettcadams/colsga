<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\MetaBoxes\Listing;

trait InsertAddress {
	protected function insertAddress(){
		if ( empty($this->aGoogleAddress) || empty($this->aGoogleAddress['latLng']) || empty($this->aGoogleAddress['address']) ){
			Listing::removeGoogleAddress($this->listingID);
			return false;
		}

		$aParseLatLng = explode(',', $this->aGoogleAddress['latLng']);

		Listing::saveData($this->listingID, array(
			'lat'        => $aParseLatLng[0],
			'lng'        => $aParseLatLng[1],
			'address'    => $this->aGoogleAddress['address']
		));
	}
}