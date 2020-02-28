<?php

namespace WilokeListingTools\Controllers;


trait GetSingleImage {
	public function getFeaturedImageData($postID){
		return array(
			array(
				'src' => get_the_post_thumbnail_url($postID),
				'imgID' => get_post_thumbnail_id($postID),
				'imgName' => get_the_title($postID)
			)
		);
	}
}