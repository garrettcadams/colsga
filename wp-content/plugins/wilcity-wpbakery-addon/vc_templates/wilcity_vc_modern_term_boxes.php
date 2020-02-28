<?php
function wilcityVCModernTermBoxes($atts){
	$atts = shortcode_atts(
		array(
			'TYPE'              => 'MODERN_TERM_BOXES',
			'heading'           => '',
			'heading_color'     => '#252c41',
			'description'       => '',
			'description_color' => '#70778b',
			'header_desc_text_align'         => 'wil-text-center',
			'items_per_row'     => 'col-lg-3',
			'taxonomy'          => 'listing_cat',
			'listing_cats'      => '',
			'col_gap'           => 20,
			'listing_locations' => '',
			'image_size'        => 'wilcity_560x300',
			'listing_tags'      => '',
			'orderby'           => 'count',
			'number'            => '',
			'order'             => 'DESC',
			'is_show_parent_only' => 'no',
			'is_hide_empty' => 'no',
			'css'               => '',
			'extra_class'       => ''
		),
		$atts
	);

	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);

	ob_start();
	wilcity_sc_render_modern_term_boxes($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode('wilcity_vc_modern_term_boxes', 'wilcityVCModernTermBoxes');