<?php
function wilcityVCTeamIntroSlider($atts){
	$atts = shortcode_atts(
		array(
			'TYPE'          => 'TEAM_INTRO_SLIDER',
			'get_by'        => 'administrator',
			'members'       => array(),
			'extra_class'   => '',
			'css'           => ''
		),
		$atts
	);
	if ( !empty($atts['members']) ){
		$atts['members'] = vc_param_group_parse_atts($atts['members']);
	}else{
		$atts['members'] = array();
	}

	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);

	ob_start();
	wilcity_render_team_intro_slider($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode('wilcity_vc_team_intro_slider', 'wilcityVCTeamIntroSlider');