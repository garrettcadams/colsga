<?php

wp_enqueue_script( 'jquery-caroufredsel' );

$output = '';

extract(
	shortcode_atts( array(
		'type' => 'newest',
        'member_type' => 'all',
		'number' => 10,
		'min_items' => 1,
		'max_items' => 6,
		'item_width' => 150,
		'image_size' => 'full',
        'autoplay' => '',
		'class' => '',
		'rounded' => "rounded",
		'online' => 'show'
	), $atts )
);

$params = array(
	'type' => $type,
    'scope' => $member_type,
	'per_page' => $number
);
if ($rounded == 'rounded') {
	$rounded = 'jvbpd-rounded';
}

$data_attr = '';
if ($autoplay != '') {
    $data_attr .= ' data-autoplay="' . $autoplay . '"';
}

if ( function_exists('bp_is_active') ) {

	if ( bp_has_members( $params ) ){
        $output = '<div class="wpb_wrapper">';
        $output .='<div class="jvbpd-carousel-container bp-groups-carousel '.$class.'">';
        $output .='<div class="jvbpd-carousel-items jvbpd-members-carousel" data-min-items="'.$min_items.'" data-max-items="'.$max_items.'" data-items-width="' . $item_width . '"' . $data_attr . '>';
        $output .= '<ul class="jvbpd-carousel">';
            while( bp_members() ) { bp_the_member();
                $output .= '<li>';
                $output .='<div class="loop-image">';
                    $output .='<div class="item-avatar '.$rounded.'">';
                        $output .= '<a href="'. bp_get_member_permalink() .'" title="'. bp_get_member_name() .'">';
                            $output .= bp_get_member_avatar( array(	'type' => $image_size, 'width' => $item_width, 'height' => $item_width ));
                        $output .= jvbpd_get_img_overlay();
                        $output .= '</a>';
                        if ( $online == 'show' ) {
                            $output .= jvbpd_get_online_status(bp_get_member_user_id());
                        }
                        $output .= '</div>'; //end item-avatar
                    $output .= '</div>';
                $output .= '</li>';
            }
        $output .= '</ul>';
        $output .= '</div>';
        $output .= '<div class="carousel-arrow">'
                    .'<a class="carousel-prev" href="#"><i class="fa fa-arrow-left"></i></a>'
                    .'<a class="carousel-next" href="#"><i class="fa fa-arrow-right"></i></a></div>';
        $output .='</div>';
        $output .='</div>';
	}

}
else
{
	$output = __("This shortcode must have Buddypress installed to work.",'jvfrmtd');
}
