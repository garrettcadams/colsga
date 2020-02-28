<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetMyPosts{
	private function setMyPosts(){
		if ( empty($this->myPosts) ){
			SetSettings::deletePostMeta($this->listingID, 'my_posts');
		}else{
			SetSettings::setPostMeta($this->listingID, 'my_posts', $this->myPosts);
		}
	}
}