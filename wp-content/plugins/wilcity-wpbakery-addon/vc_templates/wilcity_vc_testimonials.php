<?php
function wilcityVcTestimonials($atts){
	$atts = shortcode_atts(
		array(
			'TYPE'      => 'TESTIMONIAL',
			'icon'        => 'la la-quote-right',
			'testimonials'=> '',
			'autoplay'    => '',
			'css'         => '',
			'extra_class' => ''
		),
		$atts
	);

	if ( !empty($atts['testimonials']) ){
		$atts['testimonials'] = vc_param_group_parse_atts($atts['testimonials']);
	}else{
		$atts['testimonials'] = array();
	}
	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);

	ob_start();
	wilcity_sc_render_testimonials($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode('wilcity_vc_testimonials', 'wilcityVcTestimonials');