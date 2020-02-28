<?php

extract(
	shortcode_atts(
		array(), $atts
	)
);

if ( function_exists('bp_is_active') && bp_is_active('activity') ) {

	$output = '';


	$output .= '<div class="wpb_wrapper">';
		$output .= '<div class="jvbpd-bp-activity-page">';

		ob_start();
		bp_get_template_part( 'activity/index' );
		$output .= ob_get_clean();

		$output .= '</div>';
	$output .= '</div>';

}
else
{
	$output = __("This shortcode must have Buddypress installed and activity component activated.",'jvfrmtd');
}

