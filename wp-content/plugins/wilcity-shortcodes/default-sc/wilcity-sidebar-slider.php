<?php
use \WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\SingleListing;

add_shortcode('wilcity_sidebar_slider', 'wilcityRenderSidebarSlider');
function wilcityRenderSidebarSlider($aArgs){
	global $post;
	$aAtts = is_array($aArgs['atts']) ? $aArgs['atts'] : \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'      => '',
			'style'     => 'list',
			'icon'      => 'la la-clock-o',
			'desc'      => '',
			'aArgs'     => '',
			'aMetaData' => array('rating', 'address')
		)
	);

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/promotions', '', $post, $aAtts);
	}

	$query = new WP_Query($aAtts['aArgs']);

	if ( !$query->have_posts() ){
		wp_reset_postdata();
		return '';
	}
	// 4013
	$size = apply_filters('wilcity/listing-sidebar-slider/image/size', 'wilcity_290x165');
	ob_start();
	?>
	<div class="wil-single-sidebar-slider-wrapper content-box_module__333d9">
		<?php wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon']); ?>
        <div class="content-box_body__3tSRB">
            <div class="swiper__module swiper-container swiper--button-abs" data-options='{"slidesPerView":"auto","spaceBetween":10,speed:1000,"autoplay":true,"loop":true}'>
                <div class="swiper-wrapper">
		            <?php
		            while ( $query->have_posts() ){
			            SingleListing::setListingPromotionShownUp($query->post->ID);
			            $query->the_post();
			            wilcity_render_grid_item($query->post, array('img_size'=>$size, 'isSlider'=>true,'style'=>'default'));
		            } wp_reset_postdata();
		            ?>
                </div>
                <div class="swiper-button-custom">
                    <div class="swiper-button-prev-custom"><i class='la la-angle-left'></i></div>
                    <div class="swiper-button-next-custom"><i class='la la-angle-right'></i></div>
                </div>
			</div>
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}