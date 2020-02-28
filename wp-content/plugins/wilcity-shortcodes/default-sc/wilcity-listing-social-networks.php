<?php
function wilcityListingSocialNetworks($aAtts){
	$aSocialNetworks = \WilokeListingTools\Framework\Helpers\GetSettings::getSocialNetworks($aAtts['post_id']);
	if ( empty($aSocialNetworks) ){
		return '';
	}

	$socialNetworks = '';
	foreach ($aSocialNetworks as $socialKey => $val){
		if ( empty($val) ){
			continue;
		}

		if ( $socialKey == 'bloglovin' ){
			$socialKey = 'heart';
		}

		$socialNetworks .= '<a href="'.esc_url($val).'" target="_blank" class="social-icon_item__3SLnb"><i class="fa fa-'.esc_attr($socialKey).'"></i></a>';
	}

	if ( empty($socialNetworks) ){
		return '';
	}

	return '<div class="social-icon_module__HOrwr social-icon_style-2__17BFy">'.$socialNetworks.'</div>';
}

add_shortcode('wilcity_listing_social_networks', 'wilcityListingSocialNetworks');