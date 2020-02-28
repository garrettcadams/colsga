<?php
$atts = shortcode_atts(
	array(
		'TYPE'      => 'TESTIMONIAL',
		'_id'      => '',
		'icon'        => 'la la-quote-right',
        'testimonials'=> array(),
        'autoplay'    => '',
		'extra_class' => ''
	),
	$atts
);
wilcity_sc_render_testimonials($atts);