<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WILCITY_SC\SCHelpers;
use WilokeListingTools\Controllers\ReviewController;

function wilcityVcListingGridLayout($atts){
	$atts = shortcode_atts(
		array(
			'TYPE'      => 'GRID',
			'heading'           => '',
			'heading_color'     => '#252c41',
			'desc'       => '',
			'toggle_viewmore'       => 'disable',
			'border'       => 'border-gray-0',
			'viewmore_btn_name'       => 'View more',
			'style'      => 'grid',
			'desc_color' => '#70778b',
			'header_desc_text_align' => 'wil-text-center',
			'post_type' => 'listing',
			'from'      => 'all',
			'maximum_posts_on_lg_screen'    => 'col-lg-3',
			'maximum_posts_on_md_screen'    => 'col-md-4',
			'maximum_posts_on_sm_screen'    => 'col-sm-6',
			'img_size'          => 'wilcity_img_360x200',
			'orderby'           => '',
			'unit'              => 'km',
			'radius'            => 10,
			'tabname'           => '',
			'posts_per_page'    => 6,
			'listing_cats'      => '',
			'listing_locations' => '',
			'listing_ids'       => '',
			'listing_tags'      => '',
			'css'               => '',
			'extra_class'       => ''
		),
		$atts
	);

	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);
	ob_start();
	wilcity_sc_render_grid($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

add_shortcode('wilcity_vc_listing_grip_layout', 'wilcityVcListingGridLayout');