<?php

function wilcityVcWilokeWave($atts){
	$atts = shortcode_atts(
		array(
			'TYPE'                  => 'WAVE',
			'heading'               => '',
			'description'           => '',
			'btn_group'             => array(),
			'left_gradient_color'   => '#f06292',
			'right_gradient_color'  => '#f97f5f',
			'css'                   => '',
			'extra_class'           => ''
		),
		$atts
	);
	if ( !empty($atts['btn_group']) ){
		$atts['btn_group'] = vc_param_group_parse_atts($atts['btn_group']);
	}else{
		$atts['btn_group'] = array();
	}
	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);


	ob_start();
	wilcity_render_wiloke_wave($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

add_shortcode('wilcity_vc_wiloke_wave', 'wilcityVcWilokeWave');