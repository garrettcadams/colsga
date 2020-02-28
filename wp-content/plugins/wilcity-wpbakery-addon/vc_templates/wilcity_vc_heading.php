<?php
function wilcityVcHeading($atts){
	$atts = shortcode_atts(
		array(
			'TYPE'              => 'HEADING',
			'blur_mark'         => '',
			'blur_mark_color'   => '',
			'heading'           => '',
			'heading_color'     => '#252c41',
			'description'       => '',
			'description_color' => '#70778b',
			'alignment'         => 'wil-text-center',
			'css'               => '',
			'extra_class'       => ''
		),
		$atts
	);
	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);

	ob_start();
	wilcity_render_heading($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode('wilcity_vc_heading', 'wilcityVcHeading');