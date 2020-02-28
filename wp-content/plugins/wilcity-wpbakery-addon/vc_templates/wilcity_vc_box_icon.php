<?php
function wilcityVCBoxIcon($atts){
	$atts = shortcode_atts(
		array(
			'TYPE'              => 'BOX_ICON',
			'icon'              => '',
			'heading'           => '',
			'description'       => '',
			'css'               => '',
			'extra_class'       => ''
		),
		$atts
	);
	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);
	ob_start();
	wilcity_render_box_icon($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode('wilcity_vc_box_icon', 'wilcityVCBoxIcon');