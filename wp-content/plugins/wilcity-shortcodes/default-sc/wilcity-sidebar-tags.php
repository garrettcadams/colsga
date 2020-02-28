<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
add_shortcode('wilcity_sidebar_tags', 'wilcitySidebarTags');

function wilcitySidebarTags($aArgs){
	global $post;

	if ( !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_listing_tag') ){
		return '';
	}
	$aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/tags', $post, $aAtts);
	}

	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'      => '',
			'icon'      => 'la la-sitemap',
			'postID'    => ''
		)
	);


	$aAtts['taxonomy'] = 'listing_tag';
	$aAtts['postID']   = $post->ID;
	ob_start();
	echo do_shortcode("[wilcity_sidebar_terms_box name='".$aAtts['name']."' atts='".json_encode($aAtts, JSON_UNESCAPED_SLASHES)."' /]");
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}