<?php
$atts = shortcode_atts(
	array(
		'TYPE'      => 'MASONRY_TERM_BOXES',
		'heading' => '',
		'_id'      => '',
		'heading_color' => '',
		'description' => '',
		'description_color' => '',
		'header_desc_text_align' => '',
		'taxonomy'      => 'listing_cat',
		'col_gap'      => 30,
		'listing_cats'  => '',
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

wilcity_render_term_masonry_items($atts);