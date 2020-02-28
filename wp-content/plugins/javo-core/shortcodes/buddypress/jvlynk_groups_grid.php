<?php
 
$output = $anim1 = '';

extract( 
	shortcode_atts( array(
			'type' => 'newest',
			'number' => 12,
			'perline' => '',
			'animation' => '',
			'rounded' => "",
			'class' => ''
	), $atts )
);

$params = array(
	'type' => $type,
	'per_page' => $number,

);

if($perline != '') {
	$class .= ' '.$perline.'-thumbs';
}

if ($animation != '') {
	$anim1 = ' animate-when-almost-visible';
	$class .= ' jvbpd-thumbs-animated th-'.$animation;
}

if ($rounded == 'rounded') {
	$class .= ' rounded';
}

if ( function_exists('bp_is_active') && bp_is_active('groups') ) {
	// begin bp groups loop
	if ( bp_has_groups( $params ) ){
			$output .= '<div class="wpb_wrapper">';
			$output .= '<div class="jvbpd-gallery'.$anim1.'">';
			$output .= '<div class="jvbpd-thumbs-images '.$class.'">';
				while( bp_groups() ){

						bp_the_group();
						$output .= '<a href="'. bp_get_group_permalink() .'" title="'. esc_attr( bp_get_group_name()) .'">';
								$output .= bp_get_group_avatar( array(	'type' => 'full', 'width' => '250', 'height' => '250' ));
								$output .= jvbpd_get_img_overlay();
						$output .= '</a>';	

				}
			$output .= '</div>';	
			$output .= '</div>';
			$output .= '</div>';
	}
	else
	{
		$output = __("No groups were found at the moment. Please come back later.",'jvfrmtd');
	} 
}
else
{
	$output = __("This shortcode must have Buddypress installed to work.",'jvfrmtd');
} 			
