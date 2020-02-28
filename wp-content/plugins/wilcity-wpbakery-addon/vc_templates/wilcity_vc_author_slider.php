<?php
function wilcityAuthorSlider($atts){
	$atts = shortcode_atts(
		array(
			'TYPE'          => 'AUTHOR_SLIDER',
			'role__in'      => 'administrator,contributor',
			'orderby'       => 'post_count',
			'number'        => 8,
			'css'           => '',
			'extra_class'   => ''
		),
		$atts
	);
	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);

	ob_start();
	wilcity_render_author_slider($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode('wilcity_vc_author_slider', 'wilcityAuthorSlider');