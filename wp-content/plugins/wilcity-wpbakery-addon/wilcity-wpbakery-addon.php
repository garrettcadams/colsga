<?php
/*
 * Plugin Name: Wilcity WP Bakery Addon
 * Plugin URI: https://wilcity.com
 * Author: Wiloke
 * Author URI: https://wiloke.com
 * Version: 1.1.3
 */

if ( !defined('ABSPATH') ){
	die();
}

if ( !function_exists('vc_map') ){
	return '';
}

define('WILCITY_VC_SC', 'Wilcity');

function wilcityAddVCShortcode($aParams){
	$path = plugin_dir_path(__FILE__) . 'vc_templates/';
	$fileDir = $path.$aParams['base'].'.php';
	if ( is_file($fileDir) ){
		include $fileDir;
	}
}

function wilcityIntegrateWithVC(){
	$aVcShortcodes = include plugin_dir_path(__FILE__) . 'configs/vc-shortcodes.php';
	if ( !empty($aVcShortcodes) )
	{
		foreach ( $aVcShortcodes as $aVcMap )
		{
			$aVcMap['params'][] = array(
				'type'       => 'textfield',
				'heading'    => 'Extra Class',
				'param_name' => 'extra_class',
				'value'      => '',
				'std'        => ''
			);

			if ( !isset($aVcMap['is_remove_css_editor']) || !$aVcMap['is_remove_css_editor'] )
			{
				$aVcMap['params'][] = array(
					'type'          => 'css_editor',
					'heading'       => 'Css',
					'param_name'    => 'css',
					'group'         => 'Design Options'
				);
			}
			vc_map($aVcMap);
			wilcityAddVCShortcode($aVcMap);
		}

	}
}

function wilcityVCParseExtraClass($atts){
	if ( isset($atts['css']) ){
		$atts['extra_class'] = $atts['extra_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');
	}
	return $atts;
}

add_filter('wilcity/vc/parse_sc_atts', 'wilcityVCParseExtraClass');
add_action('vc_before_init', 'wilcityIntegrateWithVC');


function wilcityFilterTaxonomyAutoComplete($query, $tag, $param_name){
	global $wpdb;
	$taxonomy = substr($param_name, 0, -1);

	$taxonomyTbl = $wpdb->term_taxonomy;
	$termsTbl    = $wpdb->terms;

	$sql = "SELECT $termsTbl.term_id, $termsTbl.name FROM $termsTbl LEFT JOIN $taxonomyTbl ON ($termsTbl.term_id=$taxonomyTbl.term_id) WHERE $termsTbl.name LIKE '%".esc_sql(trim($query))."%' AND $taxonomyTbl.taxonomy=%s LIMIT 20";

	$aRawResults = $wpdb->get_results(
		$wpdb->prepare(
			$sql,
			$taxonomy
		)
	);

	if ( empty($aRawResults) ){
		return false;
	}

	$aResults = array();
	foreach ($aRawResults as $oTerm){
		$aResults[] = array(
			'label' => $oTerm->name,
			'value' => $oTerm->term_id
		);
	}
	return $aResults;
}

function wilcityFilterEvent($query, $tag, $param_name){
	global $wpdb;
	$query = '%' . $wpdb->_real_escape($query) . '%';

	$sql = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'event' AND post_title LIKE %s";

	$aRawResults = $wpdb->get_results(
		$wpdb->prepare(
			$sql,
			$query
		)
	);

	if ( empty($aRawResults) ){
		return false;
	}

	$aResults = array();
	foreach ($aRawResults as $oPost){
		$aResults[] = array(
			'label' => $oPost->post_title,
			'value' => $oPost->ID
		);
	}
	return $aResults;
}

function wilcityFilterListing($query, $tag, $param_name){
	global $wpdb;

	$aDirectoryTypes = \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, true);
	$aDirectoryTypes =  array_map(function ($type){
		global $wpdb;
		return $wpdb->_real_escape($type);
	}, $aDirectoryTypes);
	$types = '("' . implode('","', $aDirectoryTypes) . '")';

	$query = '%' . $wpdb->_real_escape($query) . '%';

	$sql = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type IN $types AND post_title LIKE %s";

	$aRawResults = $wpdb->get_results(
		$wpdb->prepare(
			$sql,
			$query
		)
	);

	if ( empty($aRawResults) ){
		return false;
	}

	$aResults = array();
	foreach ($aRawResults as $oPost){
		$aResults[] = array(
			'label' => $oPost->post_title,
			'value' => $oPost->ID
		);
	}
	return $aResults;
}


function wilcityRenderListingName($currentVal, $aParamSettings){
	$value = trim( $currentVal );
	if ( empty($value) ){
		return '';
	}

	$aParse = explode(',', $value);
	$aParse = array_map(function($val){
		return trim($val);
	}, $aParse);

	$aVals = array();
	foreach ($aParse as $postID){
		$aVals[] = get_the_title($postID);
	}

	return implode(',', $aVals);
}

function wilcityRenderTermName($currentVal, $aParamSettings){
	$value = trim( $currentVal );
	if ( empty($value) ){
		return '';
	}

	$aParse = explode(',', $value);
	$aParse = array_map(function($val){
		return trim($val);
	}, $aParse);
	$taxonomy = substr($aParamSettings['param_name'], 0, strlen($aParamSettings['param_name']) - 1);

	$aTerms = get_terms(
		array(
			'taxonomy' => $taxonomy,
			'include'  => $aParse,
			'orderby'  => 'include'
		)
	);

	if ( empty($aTerms) || is_wp_error($aTerms) ){
		return '';
	}

	$aVals = array();
	foreach ($aTerms as $oTerm){
		$aVals[] = $oTerm->name;
	}

	return implode(',', $aVals);
}

add_filter('vc_autocomplete_wilcity_vc_listings_tabs_listing_locations_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_listings_tabs_listing_cats_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);

add_filter('vc_autocomplete_wilcity_vc_listing_grip_layout_listing_locations_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_listing_grip_layout_listing_cats_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_listing_grip_layout_listing_tags_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_listing_grip_layout_listing_ids_callback', 'wilcityFilterListing', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_events_grid_listing_ids_callback', 'wilcityFilterEvent', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_events_slider_listing_ids_callback', 'wilcityFilterEvent', 10, 3);


add_filter('vc_form_fields_render_field_wilcity_vc_listing_grip_layout_listing_locations_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_wilcity_vc_listing_grip_layout_listing_cats_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_wilcity_vc_listing_grip_layout_listing_tags_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_wilcity_vc_listing_grip_layout_listing_ids_param_value', 'wilcityRenderListingName', 10, 2);
add_filter('vc_form_fields_render_field_wilcity_vc_listing_grip_layout_listing_ids_param_value', 'wilcityRenderListingName', 10, 2);

add_filter('vc_autocomplete_wilcity_vc_term_boxes_locations_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_term_boxes_listing_cats_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_term_boxes_tags_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);

add_filter('vc_form_fields_render_field_wilcity_vc_term_boxes_listing_locations_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_wilcity_vc_term_boxes_listing_tags_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_wilcity_vc_term_boxes_listing_cats_param_value', 'wilcityRenderTermName', 10, 2);


add_filter('vc_autocomplete_wilcity_vc_modern_term_boxes_listing_locations_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_modern_term_boxes_listing_cats_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_modern_term_boxes_listing_tags_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);

add_filter('vc_form_fields_render_field_wilcity_vc_modern_term_boxes_listing_locations_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_wilcity_vc_modern_term_boxes_listing_tags_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_wilcity_vc_modern_term_boxes_listing_cats_param_value', 'wilcityRenderTermName', 10, 2);


add_filter('vc_autocomplete_wilcity_vc_hero_listing_locations_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_hero_listing_cats_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_hero_listing_tags_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);

add_filter('vc_form_fields_render_field_hero_listing_locations_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_hero_listing_cats_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_hero_listing_tags_param_value', 'wilcityRenderTermName', 10, 2);

add_filter('vc_autocomplete_wilcity_vc_listings_slider_listing_locations_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_listings_slider_listing_cats_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_listings_slider_listing_tags_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);

add_filter('vc_form_fields_render_field_wilcity_vc_listings_slider_listing_locations_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_listings_slider_listing_cats_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_listings_slider_listing_tags_param_value', 'wilcityRenderTermName', 10, 2);


add_filter('vc_autocomplete_wilcity_vc_events_grid_listing_locations_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_events_grid_listing_cats_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_events_grid_listing_tags_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);

add_filter('vc_autocomplete_wilcity_vc_events_slider_listing_locations_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_events_slider_listing_cats_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);
add_filter('vc_autocomplete_wilcity_vc_events_slider_listing_tags_callback', 'wilcityFilterTaxonomyAutoComplete', 10, 3);

add_filter('vc_form_fields_render_field_events_grid_listing_locations_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_events_grid_listing_cats_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_events_grid_listing_tags_param_value', 'wilcityRenderTermName', 10, 2);

add_filter('vc_form_fields_render_field_events_slider_listing_locations_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_events_slider_listing_cats_param_value', 'wilcityRenderTermName', 10, 2);
add_filter('vc_form_fields_render_field_events_slider_listing_tags_param_value', 'wilcityRenderTermName', 10, 2);

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Wilcity_Vc_Hero extends WPBakeryShortCodesContainer {
	}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Wilcity_Vc_Search_Form extends WPBakeryShortCode {
	}
}

// Print Custom CSS To Taxonomy page
add_action('wp_head', function(){
	if ( $pageID = \WilokeListingTools\Framework\Helpers\GetSettings::isTaxonomyUsingCustomPage() ){
		$shortcodes_custom_css = get_post_meta( $pageID, '_wpb_shortcodes_custom_css', true );
		if ( ! empty( $shortcodes_custom_css ) ) {
			$shortcodes_custom_css = strip_tags( $shortcodes_custom_css );
			echo '<style type="text/css" data-type="vc_shortcodes-custom-css">';
			echo $shortcodes_custom_css;
			echo '</style>';
		}
	}
});
