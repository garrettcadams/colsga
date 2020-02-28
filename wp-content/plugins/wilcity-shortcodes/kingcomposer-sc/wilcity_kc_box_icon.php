<?php
$atts = shortcode_atts(
	array(
		'TYPE'              => 'BOX_ICON',
		'_id'      => '',
        'icon'              => '',
        'heading'           => '',
        'description'       => '',
		'extra_class'       => ''
	),
	$atts
);
wilcity_render_box_icon($atts);