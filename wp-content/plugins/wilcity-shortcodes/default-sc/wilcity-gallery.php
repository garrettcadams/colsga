<?php
use WilokeListingTools\Frontend\Gallery;
add_shortcode('wilcity_gallery', 'wilcityGallery');
function wilcityGallery($aAtts){
	$aAtts = shortcode_atts(
		array(
			'size'              => 'medium',
			'xs_gap'            => 5,
			'sm_gap'            => 5,
			'max_photos'        => 0,
			'additional_class'  => '',
			'item_class'        => 'col-xs-6 col-sm-3',
			'gallery_id'        => uniqid('gallery_id_'),
			'post_id'           => '',
            'gallery_key'       => 'gallery',
            'images'            => '',
            'is_showing_up_count' => 'yes'
		),
		$aAtts
	);

	if ( !empty($aAtts['post_id']) && empty($aAtts['images']) ){
		$aGallery = Gallery::parseGallery($aAtts['post_id'], $aAtts['size'], $aAtts['gallery_key']);
	}else{
		$aGallery = !is_array($aAtts['images']) ? json_decode($aAtts['images'], true) : $aAtts['images'];
    }
	?>
    <div id="<?php echo esc_attr($aAtts['gallery_id']); ?>" class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-magnific-wrapper')); ?>">
        <?php
        if ( !empty($aGallery) ){
            $wrapperClass = apply_filters('wilcity/filter/class-prefix', 'wilcity-magnific-via-jquery gallery_module__2AbLA ' . $aAtts['additional_class']);
            $countGallery = count($aGallery);
            $maxPhotos = $countGallery < $aAtts['max_photos'] ? $countGallery : $aAtts['max_photos'];
            $photoText = $countGallery > 1 ? esc_html__('Photos', 'wilcity-shortcodes')  : esc_html__('Photo', 'wilcity-shortcodes');
        ?>
        <div class="<?php echo esc_attr($wrapperClass); ?>">
            <div class="row" data-col-xs-gap="<?php echo esc_attr($aAtts['xs_gap']); ?>" data-col-sm-gap="<?php echo esc_attr($aAtts['sm_gap']); ?>">
                <?php
                foreach ( $aGallery as $order => $aItem ) :
                    if ( $order >= $maxPhotos ){
	                    $aAtts['item_class'] = ' hidden';
                    }
                ?>
                <div class="<?php echo esc_attr($aAtts['item_class']); ?>">
                    <div class="gallery-item_module__1wn6T">
                        <a href="<?php echo esc_url($aItem['full']); ?>">
                            <div class="imageCover_module__1VM4k">
                                <?php \WILCITY_SC\SCHelpers::renderLazyLoad($aItem[$aAtts['size']], array('divClass'=>'imageCover_img__3pxw7', 'isNotRenderImg'=>true)); ?>
                            </div>
                            <?php if ( $aAtts['is_showing_up_count'] == 'yes' && $countGallery > $aAtts['max_photos'] && $order == ($maxPhotos-1) ) : ?>
                            <div class="gallery-item_more__1nWfn pos-a-full">
                                <div class="gallery-item_wrap__1olrT">
                                    <div class="gallery-item_number__vrRlG"><?php echo esc_attr($maxPhotos); ?></div>
                                    <h4 class="gallery-item_title__2yStU"><?php echo esc_html($photoText); ?></h4>
                                </div>
                            </div>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
	<?php
}