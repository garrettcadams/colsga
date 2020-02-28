<?php
function wilcityVcHeroSearchForm( $atts ) {
	$atts = shortcode_atts(
		array(
			'TYPE'         => 'SEARCH_FORM',
			'is_using_tab' => 'no',
			'items'        => array(),
			'extra_class'  => ''
		),
		$atts
	);
	if ( ! empty( $atts['items'] ) ) {
		$atts['items'] = vc_param_group_parse_atts( $atts['items'] );
	} else {
		$atts['items'] = array();
	}

	ob_start();
	wilcity_sc_render_hero_search_form( $atts );
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

add_shortcode( 'wilcity_vc_search_form', 'wilcityVcHeroSearchForm' );