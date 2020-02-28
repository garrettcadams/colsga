<?php
global $post, $wilcityArgs;
use WilokeListingTools\Framework\Helpers\GetSettings;

$aRawGallery = GetSettings::getPostMeta($post->ID, 'gallery');
if ( empty($aRawGallery) ){
	return '';
}

$aGallery = array();

foreach ($aRawGallery as $galleryID => $link){
	$aItem['title']      = get_the_title($galleryID);
	$aItem['link']       = $link;
	$aItem['thumbnail']  = wp_get_attachment_image_url($galleryID, 'thumbnail');
	$aItem['full']       = wp_get_attachment_image_url($galleryID, 'full');
	$aItem['src']        = wp_get_attachment_image_url($galleryID, 'full');
	$aGallery[] = $aItem;
}
$numberOfPhotos = count($aRawGallery);
?>
<div class="content-box_module__333d9">
	<single-header icon="<?php echo esc_attr($wilcityArgs['icon']); ?>" heading="<?php echo esc_attr($wilcityArgs['name']); ?>"></single-header>
	<div class="content-box_body__3tSRB">
		<gallery item-class="col-xs-6 col-sm-3" gallery-id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-home-page-section')); ?>" :number-of-items="<?php echo abs($numberOfPhotos); ?>" size="<?php echo esc_attr(apply_filters('wilcity/single-listing/gallery/size', 'thumbnail')); ?>" raw-gallery="<?php echo esc_attr(json_encode($aGallery)); ?>" is-show-on-home="yes"></gallery>
	</div>
</div>
