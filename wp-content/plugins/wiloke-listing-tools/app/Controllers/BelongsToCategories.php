<?php

namespace WilokeListingTools\Controllers;


trait BelongsToCategories {
	protected function belongsToCategories(){
		if ( empty($this->category) ){
			return false;
		}
		wp_set_post_terms($this->listingID, $this->category, 'listing_cat');
	}
}