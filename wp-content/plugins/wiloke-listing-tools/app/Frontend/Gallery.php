<?php

namespace WilokeListingTools\Frontend;


use WilokeListingTools\Framework\Helpers\GetSettings;

class Gallery {
	public static function createGalleryInfo($galleryID, $link, $aSizes=array('thumbnail', 'full')){
		$aItem['title']      = get_the_title($galleryID);
		$aItem['link']       = $link;
		foreach ( $aSizes as $size ){
			$aItem[$size]  = wp_get_attachment_image_url($galleryID, $size);
		}
		return $aItem;
	}

	public static function parseGallery($postID, $size='medium', $metaKey='gallery'){
		$aRawGallery = GetSettings::getPostMeta($postID, $metaKey);

		if ( empty($aRawGallery) ){
			return false;
		}

		$aGallery = array();

		foreach ($aRawGallery as $galleryID => $link){
			$aGallery[] = self::createGalleryInfo($galleryID, $link, array('thumbnail', 'full', $size));
		}

		return $aGallery;
	}
}