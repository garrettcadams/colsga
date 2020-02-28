<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\General;

function wilcityRenderDateTimeField($aAtts){
	$aAtts = shortcode_atts(
		array(
			'key'         => '',
			'is_mobile'   => '',
			'post_id'     => '',
			'description' => '',
			'extra_class' => '',
			'title'       => ''
		),
		$aAtts
	);

	if ( !empty($aAtts['post_id']) ){
		$post = get_post($aAtts['post_id']);
	}else{
		$post = \WILCITY_SC\SCHelpers::getPost();
	}

	if ( empty($aAtts['key']) || !class_exists('WilokeListingTools\Framework\Helpers\GetSettings') || empty($post)){
		return '';
	}
	if ( !GetSettings::isPlanAvailableInListing($post->ID, $aAtts['key']) ){
		return '';
	}
	$content = GetSettings::getPostMeta($post->ID, 'custom_'.$aAtts['key']);
	if( empty($content) ) {
		return '';
	}
	$timestamp = strtotime($content);

	$timeFormat = GetSettings::getPostMeta($post->ID, 'timeFormat');

	$class = $aAtts['key'];
	if ( !empty($aAtts['extra_class']) ){
		$class .= ' ' . $aAtts['extra_class'];
	}

	return '<span class="'.esc_attr($class).'">' . date_i18n(get_option('date_format'), $timestamp) . ' ' . date_i18n(\WilokeListingTools\Framework\Helpers\Time::getTimeFormat($timeFormat), $timestamp) . '</span>';
}

add_shortcode('wilcity_render_date_time_field', 'wilcityRenderDateTimeField');