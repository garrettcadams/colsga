<?php
function wilcityVCContactUs($atts){
	$atts = shortcode_atts(
		array(
			'TYPE'                  => 'ContactUs',
			'contact_info_heading'  => '',
			'contact_form_heading'  => '',
			'contact_form_7'        => '',
			'contact_form_shortcode'=> '',
			'contact_info'          => array(),
			'extra_class'           => '',
			'css'                   => ''
		),
		$atts
	);

	if ( !empty($atts['contact_info']) ){
		$atts['contact_info'] = vc_param_group_parse_atts($atts['contact_info']);
	}else{
		$atts['contact_info'] = array();
	}
	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);

	ob_start();
	wilcity_sc_render_contact_us($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode('wilcity_vc_contact_us', 'wilcityVCContactUs');