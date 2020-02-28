<?php
/**
* Custom codes for Visual Composet
* 1. Grid Builder
*/


/** [ 1. Grid Builder ] **/
/** vc grid builder - listing category **/
add_filter( 'vc_grid_item_shortcodes', 'jvbpd_listing_category_add_grid_shortcode' );
function jvbpd_listing_category_add_grid_shortcode( $shortcodes ) {
   $shortcodes['jvbpd_list_category'] = array(
     'name' => __( 'List category', 'jvfrmtd' ),
     'base' => 'jvbpd_list_category',
     'category' => __( 'Content', 'jvfrmtd' ),
     'description' => __( 'Show List Category. Only for Javo Listings (lv_listing).', 'jvfrmtd' ),
     'post_type' => Vc_Grid_Item_Editor::postType(),
  );

   return $shortcodes;
}

add_filter( 'vc_gitem_template_attribute_listing_category', 'jvbpd_gitem_attr_listing_category', 10, 2 );
function jvbpd_gitem_attr_listing_category( $value, $data ){
	global $post;
	$strOutput = false;
	if( get_post_type( $post->ID ) == jvbpd_core()->slug ) {
		$arrTerms = wp_get_object_terms( $post->ID, 'listing_category', array( 'fields' => 'names' ));
		$strOutput = join( ', ', $arrTerms );
	}
	return $strOutput;
}


/** vc grid builder - listing location **/
add_filter( 'vc_grid_item_shortcodes', 'jvbpd_listing_location_add_grid_shortcode' );
function jvbpd_listing_location_add_grid_shortcode( $shortcodes ) {
   $shortcodes['jvbpd_list_location'] = array(
     'name' => __( 'List location', 'jvfrmtd' ),
     'base' => 'jvbpd_list_location',
     'location' => __( 'Content', 'jvfrmtd' ),
     'description' => __( 'Show List location. Only for Javo Listings (lv_listing).', 'jvfrmtd' ),
     'post_type' => Vc_Grid_Item_Editor::postType(),
  );

   return $shortcodes;
}

add_filter( 'vc_gitem_template_attribute_listing_location', 'jvbpd_gitem_attr_listing_location', 10, 2 );
function jvbpd_gitem_attr_listing_location( $value, $data ){
	global $post;
	$strOutput = false;
	if( get_post_type( $post->ID ) == jvbpd_core()->slug ) {
		$arrTerms = wp_get_object_terms( $post->ID, 'listing_location', array( 'fields' => 'names' ));
		$strOutput = join( ', ', $arrTerms );
	}
	return $strOutput;
}