<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\General;

function wilcityRenderListingTypeRelationships($aAtts){
	$aAtts = shortcode_atts(
		array(
			'title'       => '',
			'description' => '',
			'key'         => '',
			'style'       => 'grid',
			'maximum_posts_on_lg_screen'    => 'col-lg-6',
			'maximum_posts_on_md_screen'    => 'col-md-6',
			'maximum_posts_on_sm_screen'    => 'col-sm-6',
			'img_size'                      => 'wilcity_img_360x200',
			'is_mobile'   => '',
			'extra_class' => '',
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

	$listingContent = GetSettings::getPostMeta($post->ID, 'custom_'.$aAtts['key']);

	if ( empty($listingContent) ){
		return '';
	}

	$aParseContent = explode(',', $listingContent);
	$postType = get_post_type($aParseContent[0]);
	$aAtts['post_type'] = $postType;
	$aAtts['listing_ids'] = $aParseContent;
	$aAtts['orderby'] = 'post__in';
	$aAtts['toggle_viewmore'] = '';

	// 3 columns if it's under Single Listing Tab
	if ( isset($_GET['action']) && $_GET['action'] == 'wilcity_fetch_custom_content' ){
		$aAtts['maximum_posts_on_lg_screen'] = $aAtts['maximum_posts_on_md_screen'] = 'col-md-4';
	}

	ob_start();
	switch ($aAtts['style']){
		case 'list':
			wilcity_sc_render_grid($aAtts);
			break;
		default:
			wilcity_sc_render_grid($aAtts);
			break;
	}

	$content = ob_get_contents();
	ob_end_clean();

	$class = $aAtts['key'];
	if ( !empty($aAtts['extra_class']) ){
		$class .= ' ' . $aAtts['extra_class'];
	}
	
	$content = $content = '<div class="'.esc_attr($class).'">'.$content.'</div>';

	return $content;
}

add_shortcode('wilcity_render_listing_type_relationships', 'wilcityRenderListingTypeRelationships');