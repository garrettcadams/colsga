<?php
$atts = shortcode_atts(
	array(
		'TYPE'          => 'TEAM_INTRO_SLIDER',
		'_id'      => '',
		'get_by'        => 'administrator',
		'members'       => array(),
		'extra_class'   => ''
	),
	$atts
);
wilcity_render_team_intro_slider($atts);