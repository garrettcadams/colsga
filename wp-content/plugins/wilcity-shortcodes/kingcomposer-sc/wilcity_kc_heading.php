<?php
$atts = shortcode_atts(
	array(
		'TYPE'      => 'HEADING',
		'_id'      => '',
		'blur_mark'         => '',
		'blur_mark_color'   => '',
		'heading'           => '',
		'heading_color'     => '#252c41',
		'description'       => '',
		'description_color' => '#70778b',
		'alignment'         => 'wil-text-center',
		'extra_class'       => ''
	),
	$atts
);
wilcity_render_heading($atts);