<?php
function wilcityVCRectangleTermBoxes( $atts ) {
	$atts = shortcode_atts(
		array(
			'TYPE'                   => 'TERM_BOXES',
			'heading'                => '',
			'heading_color'          => '',
			'description'            => '',
			'description_color'      => '',
			'header_desc_text_align' => '',
			'items_per_row'          => 'col-lg-3',
			'taxonomy'               => 'listing_cat',
			'listing_cats'           => '',
			'is_show_parent_only'    => 'no',
			'listing_locations'      => '',
			'listing_tags'           => '',
			'is_hide_empty'          => 'no',
			'image_size'             => 'image_size',
			'orderby'                => 'count',
			'order'                  => 'DESC',
			'number'                 => 4,
			'extra_class'            => '',
			'css_custom'             => ''
		),
		$atts
	);

	$atts = apply_filters( 'wilcity/vc/parse_sc_atts', $atts );

	ob_start();
	wilcity_render_rectangle_term_boxes( $atts );
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

add_shortcode( 'wilcity_vc_rectangle_term_boxes', 'wilcityVCRectangleTermBoxes' );