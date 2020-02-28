<?php
use \WILCITY_SC\SCHelpers;

$atts = shortcode_atts(
	array(
		'TYPE'      => 'LISTINGS_SLIDER',
		'_id'      => '',
		'maximum_posts'             => 8,
		'maximum_posts_on_extra_lg_screen'=> 6,
		'maximum_posts_on_lg_screen'=> 5,
		'maximum_posts_on_md_screen'=> 5,
		'maximum_posts_on_sm_screen'=> 2,
		'maximum_posts_on_extra_sm_screen'=> 2,
		'heading'                   => '',
		'heading_color'             => '',
		'desc'                      => '',
		'desc_color'                => '',
		'post_type'                 => 'listing',
		'listing_ids'               => '',
		'from'                      => 'all',
		'viewmore_btn_name'         => 'View more',
		'toggle_gradient'           => 'enable',
		'left_gradient'             => '#ed6392',
		'right_gradient'            => '#006bf7',
		'gradient_opacity'          => '0.3',
		'listing_tags'              => '',
		'listing_cats'              => '',
		'listing_locations'         => '',
		'orderby_fallback'          => '',
		'orderby'                   => '',
		'autoplay'              	=> 100000,
		'order'                     => 'DESC',
		'custom_taxonomy_key'       => '',
		'custom_taxonomies_id'      => '',
		'toggle_viewmore'           => 'disable',
		'header_desc_text_align'    => 'wil-text-center',
		'mobile_img_size'        => '',
		'desktop_image_size'        => '',
		'extra_class'               => '',
		'css_custom'                => ''
	),
	$atts
);

wilcity_render_slider($atts);