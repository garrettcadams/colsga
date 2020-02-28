<?php
global $post, $wiloke, $wilcityGallerySettings;
$type = isset($wiloke->aThemeOptions['listing_template']) ? $wiloke->aThemeOptions['listing_template'] : 'featured_image_fullwidth';

if ( $type == 'slider' ){
	$wilcityGallerySettings = \WilokeListingTools\Framework\Helpers\GetSettings::getPostMeta($post->ID, 'gallery');
	if ( empty($wilcityGallerySettings) ){
		$type = 'featured_image_fullwidth';
    }
}

switch ($type){
    case 'slider':
	    get_template_part('single-listing/header-slider');
        break;
    default:
        get_template_part('single-listing/header-featuredimage-fullwidth');
        break;
}
