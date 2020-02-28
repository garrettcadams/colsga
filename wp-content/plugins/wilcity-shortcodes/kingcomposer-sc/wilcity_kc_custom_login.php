<?php
$atts = shortcode_atts(
	array(
		'TYPE'      => 'EXTERNAL_LOGIN',
		'_id'      => '',
		'social_login_type' => 'fb_default',
		'login_section_title' => '',
		'social_login_shortcode' => '',
		'register_section_title' => '',
		'rp_section_title' => '',
		'login_bg_img' => '',
		'login_bg_color' => '',
		'login_boxes' => '',
	),
	$atts
);

wilcity_render_custom_login_sc($atts);