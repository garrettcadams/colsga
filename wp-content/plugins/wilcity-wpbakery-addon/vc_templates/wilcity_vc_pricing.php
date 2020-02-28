<?php
function wilcityVCPricing($atts){
	$atts = shortcode_atts(
		array(
			'include'       => '',
			'items_per_row' => 'col-md-4',
			'extra_class'   => '',
			'listing_type'  => 'flexible',
			'toggle_nofollow'  => 'disable',
			'css'           => ''
		),
		$atts
	);
	$atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);

	ob_start();
	wilcityPricing($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode('wilcity_vc_pricing', 'wilcityVCPricing');