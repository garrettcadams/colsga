<?php
$atts = shortcode_atts(
	array(
		'TYPE'      => 'MODERN_TERM_BOXES',
		'heading' => '',
		'_id'      => '',
		'heading_color' => '',
		'description' => '',
		'description_color' => '',
		'header_desc_text_align' => '',
		'items_per_row' => 'col-lg-3',
		'taxonomy'      => 'listing_cat',
		'listing_cats'  => '',
		'col_gap'  => 20,
		'listing_locations' => '',
		'number' => 6,
		'image_size' => 'wilcity_560x300',
		'listing_tags'  => '',
		'is_hide_empty'  => 'no',
		'is_show_parent_only'  => 'no',
		'orderby'       => 'count',
		'order'         => 'DESC',
		'extra_class'   => '',
		'css_custom'        => ''
	),
	$atts
);
wilcity_sc_render_modern_term_boxes($atts);