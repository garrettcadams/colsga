<?php
add_shortcode('wilcity_vc_hero', 'wilcityVcHero');
function wilcityVcHero($atts, $content){
	$atts = shortcode_atts(
		array(
			'TYPE'              => 'HERO',
			'heading'           => '',
			'heading_color'     => '',
			'heading_font_size' => '',
			'description'       => '',
			'description_color' => '',
			'description_font_size' => '',
			'bg_type'           => 'image',
			'img_size'          => 'large',
			'bg_overlay'        => '',
			'maximum_terms_suggestion'      => 6,
			'image_bg'          => '',
			'slider_bg'         => '',
			'toggle_list_of_suggestions' => 'enable',
			'taxonomy_position' => 'above_search_form',
			'toggle_dark_and_white_background' => 'disable',
			'search_form_position' => 'bottom',
			'search_form_background' => 'hero_formDark__3fCkB',
			'toggle_button' => 'enable',
			'button_icon' => '',
			'button_name' => 'Check out',
			'button_background_color' => '',
			'button_text_color' => '#fff',
			'button_size' => 'wil-btn--sm',
			'button_link' => '',
			'taxonomy'  => 'listing_cat',
			'orderby'   => 'count',
			'listing_cats'   => '',
			'listing_locations'   => '',
			'extra_class'       => ''
		),
		$atts
	);
	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);
	if ( !empty($atts['image_bg']) ){
		$atts['image_bg'] = wp_get_attachment_image_url($atts['image_bg'], 'large');
	}

	ob_start();
	wilcity_sc_render_hero($atts, $content);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
