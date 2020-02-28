<?php
use \WILCITY_SC\SCHelpers;
function wilcityVCListingsSlider($atts){
	$atts = shortcode_atts(
		array(
			'TYPE'      => 'LISTINGS_SLIDER',
			'maximum_posts'             => 8,
			'maximum_posts_on_extra_lg_screen'=> 6,
			'maximum_posts_on_lg_screen'=> 5,
			'maximum_posts_on_md_screen'=> 5,
			'maximum_posts_on_sm_screen'=> 2,
			'maximum_posts_on_extra_sm_screen'=> 2,
			'heading'           => '',
			'heading_color'     => '#252c41',
			'desc'       => '',
			'desc_color' => '#70778b',
			'header_desc_text_align'    => 'wil-text-center',
			'toggle_viewmore'           => 'disable',
			'viewmore_btn_name'       => 'View more',
			'style'                     => 'grid',
			'post_type'                 => 'listing',
			'from'                      => 'all',
			'listing_tags'              => '',
			'listing_cats'              => '',
			'listing_locations'         => '',
			'orderby'                   => '',
			'autoplay'              	=> 3000,
			'toggle_gradient'           => 'enable',
			'left_gradient'             => '#006bf7',
			'right_gradient'            => '#ed6392',
			'gradient_opacity'          => '0.3',
			'desktop_image_size'        => '',
			'listing_ids'               => '',
			'extra_class'               => '',
			'css_custom'                => '',
			'css'                       => ''
		),
		$atts
	);

	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);
	ob_start();
	wilcity_render_slider($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

add_shortcode('wilcity_vc_listings_slider', 'wilcityVCListingsSlider');