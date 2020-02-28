<?php
use \WILCITY_SC\SCHelpers;

$atts = shortcode_atts(
	array(
		'TYPE'              => 'LISTINGS',
		'post_type'         => 'listing',
		'orderby'           => '',
		'posts_per_page'    => 6,
		'listing_cats'      => '',
		'listing_locations' => '',
		'listing_tags'      => '',
		'bg_color'          => '#ffffff',
		'style'             => 'grid'
	),
	$atts
);
if ( !trait_exists('WILCITY_APP\Controllers\JsonSkeleton') ){
	return '';
}

$aArgs = SCHelpers::parseArgs($atts);
$query = new WP_Query($aArgs);
if ( !$query->have_posts() ){
	wp_reset_postdata();
	return '';
}
$aResponse = array();
while ( $query->have_posts() ){
	$query->the_post();
	$aListing = apply_filters('wilcity/mobile/render_listings_on_mobile', $atts, $query->post);
	$aResponse[] = $aListing;
} wp_reset_postdata();

echo '%SC%' . json_encode(
		array(
			'oSettings' => \WILCITY_APP\Helpers\AppHelpers::removeUnnecessaryParamOnApp($atts),
			'TYPE'      => $atts['TYPE'],
			'oResults'  => $aResponse,
            'oViewMore' => \WILCITY_APP\Helpers\AppHelpers::getViewMoreArgs($atts),
		)
	) . '%SC%';
return '';
