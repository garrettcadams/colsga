<?php
use WILCITY_SC\SCHelpers;
$atts = shortcode_atts(
	array(
		'post_type'         => 'event',
		'orderby'           => 'post_date',
		'order'             => 'DESC',
		'img_size'          => 'wilcity_img_360x200',
		'posts_per_page'    => 6,
		'style'             => 'grid',
		'bg_color'          => '#ffffff'
	),
	$atts
);

$aArgs = SCHelpers::parseArgs($atts);

$aArgs['isAppEventQuery'] = true;

$query = new WP_Query($aArgs);

if ( !$query->have_posts() ){
	wp_reset_postdata();
	return '';
}

$aResponse = array();
while ( $query->have_posts() ){
	$query->the_post();
	$aListing = apply_filters('wilcity/mobile/render_event_on_mobile', $atts, $query->post);

	unset($aListing['aMetaData']);
	unset($aListing['aSections']);
	unset($aListing['coverImg']);
	unset($aListing['logo']);
	unset($aListing['oCustomSettings']);
	unset($aListing['oGallery']);
	unset($aListing['oIcon']);
	unset($aListing['oReview']);
	unset($aListing['oSocialNetworks']);
	unset($aListing['oTerm']);
	unset($aListing['oVideos']);
	unset($aListing['timezone']);

	$aResponse[] = $aListing;
} wp_reset_postdata();

echo '%SC%' . json_encode(
		array(
			'oSettings' => $atts,
			'TYPE'      => 'EVENTS',
			'oResults'  => $aResponse
		)
	) . '%SC%';
return '';