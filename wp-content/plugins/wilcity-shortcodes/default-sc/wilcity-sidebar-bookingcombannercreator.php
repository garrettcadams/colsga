<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Time as WilcityTimeHelper;
use WilokeListingTools\Frontend\BusinessHours;

add_shortcode('wilcity_sidebar_bookingcombannercreator', 'wilcitySidebarBookingComSidebarCreator');
function wilcitySidebarBookingComSidebarCreator($aArgs){
	global $post;
	$aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'  => '',
			'icon'  => 'la la-hotel',
			'desc'  => ''
		)
	);
	if ( !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_bookingcombannercreator') ){
		return '';
	}

	$bookingID = \WilokeListingTools\Models\BookingCom::getCreatorIDByParentID($post->ID);

	if ( empty($bookingID) ){
		return '';
	}

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/bookingcombannercreator', $post, $aAtts);
	}
	$content = '';
	$content .= '<div class="content-box_module__333d9 sidebar-item-bookingcom">';
	$content .= wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon'], true);
	$content .= '<div class="content-box_body__3tSRB">';
		$content .= do_shortcode('[bdotcom_bm bannerid="'.esc_attr($bookingID).'"]');
	$content .= '</div>';
	$content .= '</div>';

	return $content;
}