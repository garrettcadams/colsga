<?php

namespace WilokeListingTools\Controllers;


trait BelongsToTags {
	protected function belongsToTags(){
		wp_set_post_terms($this->listingID, $this->aTags, 'listing_tag');
	}
}