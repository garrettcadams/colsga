<?php

$output = $anim1 = '';

extract( 
	shortcode_atts( array(
			'type' => 'newest',
			'member_type' => 'all',
			'number' => 12,
			'perline' => '',
			'animation' => '',
			'rounded' => "",
			'class' => ''
	), $atts )
);

$params = array(
	'type' => $type,
	'scope' => $member_type,
	'per_page' => $number,

);

if($perline != '') {
	$class .= ' ' . $perline . '-thumbs';
}

if ($animation != '') {
	$anim1 = ' animate-when-almost-visible';
	$class .= ' jvbpd-thumbs-animated th-' . $animation;
}

if ($rounded == 'rounded') {
	$class .= ' rounded';
}

if ( function_exists('bp_is_active') ) {
	// begin bp members loop
	if ( bp_has_members( $params ) ){
			$output .= '<div class="wpb_wrapper">';
			$output .= '<div class="jvbpd-gallery'.$anim1.'">';
			$output .= '<div class="jvbpd-thumbs-images '.$class.'">';
				while( bp_members() ){
	
						bp_the_member();
						$output .= '<a href="'. bp_get_member_permalink() .'" title="'. bp_get_member_name() .'">';
								$output .= bp_get_member_avatar( array(	'type' => 'full', 'width' => '250', 'height' => '250' ));
								$output .= jvbpd_get_img_overlay();
						$output .= '</a>';	
	
				}
			$output .= '</div>';	
			$output .= '</div>';
			$output .= '</div>';
	}
}
else
{
	$output = __("This shortcode must have Buddypress installed to work.",'jvfrmtd');
} 
