<?php
$atts = shortcode_atts(
	array(
		'TYPE'      => 'WAVE',
		'_id'      => '',
		'heading'               => '',
		'description'           => '',
		'btn_group'             => array(),
		'left_gradient_color'   => '#f06292',
		'right_gradient_color'  => '#f97f5f',
		'extra_class'           => ''
	),
	$atts
);
wilcity_render_wiloke_wave($atts);