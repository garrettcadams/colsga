<?php
add_shortcode('wilcity_vc_term_boxes', 'wilcityVCTermBoxes');
function wilcityVCTermBoxes($atts){
	$atts = shortcode_atts(
		array(
			'TYPE'      => 'TERM_BOXES',
			'heading'           => '',
			'heading_color'     => '#252c41',
			'description'       => '',
			'description_color' => '#70778b',
			'header_desc_text_align'         => 'wil-text-center',
			'items_per_row' => 'col-lg-3',
			'taxonomy'      => 'listing_cat',
			'listing_cats'  => '',
			'listing_locations' => '',
			'listing_tags'  => '',
			'orderby'       => 'count',
			'toggle_box_gradient' => 'enable',
			'left_gradient_color' => '#006bf7',
			'right_gradient_color' => '#f06292',
			'order'       => 'DESC',
			'is_show_parent_only' => 'no',
			'is_hide_empty' => 'no',
			'number'        => '',
			'css'        => '',
			'extra_class'   => ''
		),
		$atts
	);
	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);

	ob_start();
	wilcity_sc_render_term_boxes($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}