<?php
add_shortcode('wilcity_sidebar_categories', 'wilcitySidebarCategories');

function wilcitySidebarCategories($aArgs){
	$aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'      => '',
			'icon'      => 'la la-sitemap',
			'postID'    => '',
			'taxonomy'  => 'listing_cat'
		)
	);

	global $post;
	$aAtts['postID']   = $post->ID;

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/categories', $post, $aAtts);
	}

	ob_start();
	echo do_shortcode("[wilcity_sidebar_terms_box name='".$aAtts['name']."' atts='".json_encode($aAtts)."' /]");
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}