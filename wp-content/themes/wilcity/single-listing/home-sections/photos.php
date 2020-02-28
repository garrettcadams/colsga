<?php
global $post, $wilcityArgs;
use WilokeListingTools\Framework\Helpers\GetSettings;

$aRawGallery = GetSettings::getPostMeta($post->ID, 'gallery');
if ( empty($aRawGallery) ){
	return '';
}
$url = get_permalink($post->ID);
$aGallery = array();

foreach ($aRawGallery as $galleryID => $link){
	$aItem['title']      = get_the_title($galleryID);
	$aItem['medium']     = wp_get_attachment_image_url($galleryID, apply_filters('wilcity/single-listing/gallery/size', 'medium'));
	$aItem['full']       = wp_get_attachment_image_url($galleryID, 'full');
	$aGallery[] = $aItem;
}
$numberOfPhotos = isset($wilcityArgs['maximumItemsOnHome']) && !empty($wilcityArgs['maximumItemsOnHome']) ? $wilcityArgs['maximumItemsOnHome'] : 4;
?>
<!-- content-box_module__333d9 -->
<div class="content-box_module__333d9">
	<single-header icon="<?php echo esc_attr($wilcityArgs['icon']); ?>" heading="<?php echo esc_attr($wilcityArgs['name']); ?>"></single-header>
	<div class="content-box_body__3tSRB">
		<gallery item-class="col-xs-6 col-sm-3" gallery-id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-home-page-section')); ?>" size="medium" :number-of-items="<?php echo abs($numberOfPhotos); ?>" raw-gallery="<?php echo esc_attr(json_encode($aGallery)); ?>" is-show-on-home="yes"></gallery>
	</div>
	<footer class="content-box_footer__kswf3">
        <switch-tab-btn wrapper-class="wil-text-center list_link__2rDA1 text-ellipsis color-primary--hover" tab-key="photos" page-url="<?php echo esc_url($url); ?>" tab-title="<?php echo esc_attr(\WilokeListingTools\Frontend\SingleListing::renderTabTitle(__('Photos', 'wilcity'))); ?>">
            <template slot="insideTab">
	            <?php esc_html_e('See All', 'wilcity'); ?>
            </template>
        </switch-tab-btn>
    </footer>
</div><!-- End / content-box_module__333d9 -->
