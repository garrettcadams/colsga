<?php
function wilcityVCCustomLogin($atts){
	$atts = shortcode_atts(
		array(
			'social_login_type'     => 'fb_default',
			'custom_shortcode'      => 'fb_default',
			'login_section_title'   => '',
			'register_section_title' => '',
			'social_login_shortcode' => '',
			'rp_section_title'  => '',
			'login_bg_img'      => '',
			'login_bg_color'    => '',
			'login_boxes'       => '',
			'css'               => '',
			'extra_class'       => ''
		),
		$atts
	);

	if ( !empty($atts['login_bg_img']) ){
		$atts['login_bg_img'] = wp_get_attachment_image_url($atts['login_bg_img'], 'large');
	}

	if ( !empty($atts['login_boxes']) ){
		$atts['login_boxes'] = vc_param_group_parse_atts($atts['login_boxes']);
	}

	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);

	ob_start();
	wilcity_render_custom_login_sc($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

add_shortcode('wilcity_vc_custom_login', 'wilcityVCCustomLogin');