<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\General;

function wilcityRenderImageField($aAtts){
	$aAtts = shortcode_atts(
		array(
			'post_id'     => '',
			'key'         => '',
			'is_mobile'   => '',
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

	$imgID = GetSettings::getPostMeta($post->ID, 'custom_'.$aAtts['key'].'_id');
	if ( empty($imgID) ){
		$url = GetSettings::getPostMeta($post->ID, 'custom_'.$aAtts['key']);
		$title = $post->post_title;
		if ( empty($url) ){
			return '';
		}
	}else{
		$title = get_post_field('post_title', $imgID);
		$url = wp_get_attachment_image_url($imgID, 'large');
	}

	$class = $aAtts['key'];
	if ( !empty($aAtts['extra_class']) ){
		$class .= ' ' . $aAtts['extra_class'];
	}

	return '<img class="'.esc_attr($class).'" src="'.esc_url($url).'" alt="'.esc_attr($title).'">';
}

add_shortcode('wilcity_render_image_field', 'wilcityRenderImageField');