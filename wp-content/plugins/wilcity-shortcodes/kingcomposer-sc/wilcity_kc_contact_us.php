<?php
$atts = shortcode_atts(
	array(
		'TYPE'                  => 'ContactUs',
		'_id'      => '',
		'contact_info_heading'  => '',
		'contact_form_heading'  => '',
		'contact_form_7'        => '',
		'contact_form_shortcode'=> '',
		'contact_info'          => array(),
		'extra_class'           => ''
	),
	$atts
);

wilcity_sc_render_contact_us($atts);