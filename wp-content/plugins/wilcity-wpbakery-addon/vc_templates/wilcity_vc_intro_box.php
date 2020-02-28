<?php
function wilcityVCIntroBox($atts, $content){
	$atts = shortcode_atts(
		array(
			'TYPE'          => 'INTRO_BOX',
			'bg_img'        => '',
			'video_intro'   => '',
			'extra_class'   => ''
		),
		$atts
	);
	$atts['intro'] = $content;

	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);

	if ( !empty($atts['bg_img']) ){
		$atts['bg_img'] = wp_get_attachment_image_url($atts['bg_img'], 'large');
	}

	ob_start();
	wilcity_render_intro_box($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode('wilcity_vc_intro_box', 'wilcityVCIntroBox');