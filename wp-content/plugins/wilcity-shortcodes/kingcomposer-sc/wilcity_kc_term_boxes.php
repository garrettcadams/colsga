<?php
$atts = shortcode_atts(
	array(
		'TYPE'      => 'TERM_BOXES',
		'heading' => '',
		'_id'      => '',
		'heading_color' => '',
		'description' => '',
		'description_color' => '',
		'header_desc_text_align' => '',
		'items_per_row' => 'col-lg-3',
		'taxonomy'      => 'listing_cat',
		'listing_cats'  => '',
		'is_show_parent_only'  => 'no',
		'listing_locations' => '',
		'listing_tags'  => '',
		'is_hide_empty'  => 'no',
		'orderby'       => 'count',
		'toggle_box_gradient' => 'enable',
		'left_gradient_color' => '#006bf7',
		'right_gradient_color' => '#f06292',
		'order'       => 'DESC',
		'number'       => '',
		'extra_class'   => '',
		'css_custom'        => ''
	),
	$atts
);

wilcity_sc_render_term_boxes($atts);