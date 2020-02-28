<?php
$atts = shortcode_atts(
	array(
		'TYPE'              => 'GOOGLE_ADMOB',
		'banner_size_type'  => 'default',
		'banner_size'=> ''
	),
	$atts
);
if ( !trait_exists('WILCITY_APP\Controllers\JsonSkeleton') ){
	return '';
}

$aAdmobConfiguration = \WILCITY_APP\Helpers\AppHelpers::getAdMobConfiguration();

if ( empty($aAdmobConfiguration) ){
	return '';
}

if ( $atts['banner_size_type'] == 'custom'){
	$aAdmobConfiguration['oBanner']['banner_size'] = $atts['banner_size'];
}

unset($atts['banner_size_type']);

echo '%SC%' . json_encode(array(
		'oResults'  => $aAdmobConfiguration,
		'TYPE'      => $atts['TYPE']
	)) . '%SC%';
return '';