<?php

namespace WilokeListingTools\Controllers;


trait BelongsToLocation {
	protected function belongsToLocation(){
		if ( empty($this->location) ){
			return false;
		}
		wp_set_post_terms($this->listingID, $this->location, 'listing_location');
	}
}


