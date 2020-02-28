<?php
use \WILCITY_SC\SCHelpers;

$atts = shortcode_atts(
	array(
		'TYPE'      => 'EVENTS_SLIDER',
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
		'post_type'                 => 'event',
		'from'                      => 'all',
		'listing_tags'              => '',
		'viewmore_btn_name'         => 'View more',
		'header_desc_text_align'    => '',
		'toggle_viewmore'           => 'disable',
		'listing_cats'              => '',
		'listing_locations'         => '',
		'orderby'                   => '',
		'custom_taxonomy_key'       => '',
		'custom_taxonomies_id'      => '',
		'is_auto_play'              => 'disable',
		'mobile_img_size'           => '',
		'image_size'                => '',
		'extra_class'               => '',
		'css_custom'                => '',
		'toggle_gradient'           => 'enable',
		'left_gradient'             => '#ed6392',
		'right_gradient'            => '#006bf7',
		'gradient_opacity'          => '0.3'
	),
	$atts
);

wilcity_render_slider($atts);