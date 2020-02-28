<?php
$atts = shortcode_atts(
	array(
		'TYPE'          => 'INTRO_BOX',
		'_id'      => '',
		'bg_img'        => '',
		'video_intro'   => '',
		'intro'         => '',
		'extra_class'   => ''
	),
	$atts
);
wilcity_render_intro_box($atts);