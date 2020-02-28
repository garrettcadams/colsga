<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetVideo {
	protected function setVideos(){
		if ( empty($this->aVideos) ){
			SetSettings::deletePostMeta($this->listingID, 'video_srcs');
		}else{
			SetSettings::setPostMeta($this->listingID, 'video_srcs', $this->aVideos);
		}
	}
}