<?php
$atts = shortcode_atts(
	array(
		'TYPE'      => 'EXTERNAL_LOGIN',
		'_id'      => '',
		'login_section_title' => '',
		'login_bg_img' => '',
		'login_bg_color' => '',
		'login_boxes' => '',
	),
	$atts
);

wilcity_render_external_login_sc($atts);