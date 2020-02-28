<?php
$atts = shortcode_atts(
	array(
		'TYPE'          => 'AUTHOR_SLIDER',
		'role__in'      => 'administrator,contributor',
		'orderby'       => 'post_count',
		'number'        => 8,
		'extra_class'   => ''
	),
	$atts
);
wilcity_render_author_slider($atts);