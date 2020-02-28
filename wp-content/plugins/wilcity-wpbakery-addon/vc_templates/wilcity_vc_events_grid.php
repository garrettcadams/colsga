<?php
function wilcityVCEventsGrid($atts){
	$atts = shortcode_atts(
		array(
			'post_type'         => 'event',
			'heading'           => '',
			'heading_color'     => '#252c41',
			'desc'       => '',
			'desc_color' => '#70778b',
			'header_desc_text_align'         => 'wil-text-center',
			'toggle_viewmore'       => 'disable',
			'viewmore_btn_name'       => 'View more',
			'listing_tags'      => '',
			'listing_cats'      => '',
			'listing_locations' => '',
			'event_ids' => '',
			'maximum_posts_on_lg_screen'    => 'col-lg-3',
			'maximum_posts_on_md_screen'    => 'col-md-4',
			'maximum_posts_on_sm_screen'    => 'col-sm-6',
			'from'      => 'all',
			'orderby'   => 'post_date',
			'listing_ids'       => '',
			'img_size'          => 'wilcity_img_360x200',
			'posts_per_page'    => 6,
			'css'               => '',
			'extra_class'       => ''
		),
		$atts
	);

	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);

	ob_start();
	wilcity_sc_render_events_grid($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

add_shortcode('wilcity_vc_events_grid', 'wilcityVCEventsGrid');