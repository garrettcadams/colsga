<?php
$atts = shortcode_atts(
	array(
		'TYPE'          => 'MAP',
		'_id'      => '',
		'type'          => '',
		'latlng'        => '',
		'max_zoom'      => '',
		'minimum_zoom'  => '',
		'default_zoom'  => '',
		'img_size'      => '',
		'style'         => 'grid',
		'orderby_fallback' => 'post_date',
		'orderby'       => 'menu_order',
		'order'         => 'DESC',
		'extra_class'   => ''
	),
	$atts
);

wilcityRenderMapSC($atts);