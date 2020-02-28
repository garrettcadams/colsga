<?php
$atts = shortcode_atts(
	array(
		'TYPE'              => 'HERO',
		'_id'      => '',
		'heading'           => '',
		'heading_color'     => '',
		'heading_font_size' => '50px',
		'description'       => '',
		'description_color' => '',
		'description_font_size' => '17px',
		'bg_type'           => 'image',
		'bg_overlay'        => '',
		'image_bg'          => '',
		'img_size'          => 'large',
		'slider_bg'         => '',
		'toggle_list_of_suggestions' => 'enable',
		'maximum_terms_suggestion'      => 6,
		'taxonomy_position' => 'above_search_form',
		'toggle_dark_and_white_background' => 'disable',
		'search_form_position' => 'bottom',
		'search_form_background' => 'hero_formDark__3fCkB',
		'toggle_button' => 'enable',
		'button_icon' => '',
		'button_background_color' => '',
		'button_text_color' => '#fff',
		'button_size' => 'wil-btn--sm',
		'button_name' => 'Check out',
		'button_link' => '#',
		'taxonomy'  => 'listing_cat',
		'orderby'   => 'count',
		'listing_cats'   => '',
		'listing_locations'   => '',
		'extra_class'       => ''
	),
	$atts
);
wilcity_sc_render_hero($atts, $content);