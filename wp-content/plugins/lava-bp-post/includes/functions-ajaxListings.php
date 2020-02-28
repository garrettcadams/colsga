<?php
add_action( 'wp_ajax_lava_bpp_listing' , 'lava_ajax_bpp_items' );
add_action( 'wp_ajax_nopriv_lava_bpp_listing', 'lava_ajax_bpp_items' );

function lava_ajax_bpp_items() {

	global $lava_bpp_manager;

	header( "Content-Type:application/json; charset=utf-8" );

	$response = $output_items	= Array();

	$lava_query					= new lava_Array( $_GET );
	$this_paged					= $lava_query->get( 'paged', 1 );
	$post_type					= constant( 'Lava_Bp_Post_Func::SLUG' );

	$lava_listing_posts_args	=
		Array(
			'post_type'			=> $post_type
			, 'post_status'		=> 'publish'
			, 'posts_per_page'	=> 10
			, 'paged'			=> $this_paged
			, 'tax_query'		=> Array()
		);

	if( $this_taxonomy = $lava_query->get( 'lava_filter', false ) )
	{
		if( is_Array( $this_taxonomy ) ) : foreach( $this_taxonomy as $taxonomy => $term_id ) {

			if( empty( $term_id ) )
				continue;

			if( is_Array( $term_id ) ){
				$term_	= get_terms( $taxonomy, Array( 'hide_empty' => 0, 'fields' => 'ids' ) );
				if( !Array_diff( $term_, $term_id ) )
					continue;
			}

			$lava_listing_posts_args[ 'tax_query' ][] =
				Array(
					'taxonomy'	=> $taxonomy
					, 'field'	=> 'term_id'
					, 'terms'	=> $term_id
				);

		} endif;
	}

	if( $this_keyword = $lava_query->get( 'keyword', false ) )
		$lava_listing_posts_args['s']	= $this_keyword;

	if( $this_location = $lava_query->get( 'location', false ) )
		$lava_listing_posts_args['meta_query'][]	= Array(
			'key'		=> '_location'
			, 'value'	=> $this_location
			, 'compare'	=> 'LIKE'
		);

	$lava_listing_posts					= new WP_Query( $lava_listing_posts_args );
	$arrGetMoreInfo						= apply_filters( "lava_{$post_type}_more_meta", Array() );
	$arrGetTaxonomies					= apply_filters( "lava_{$post_type}_taxonomies", Array() );

	if( $lava_listing_posts->have_posts() )
	{
		while( $lava_listing_posts->have_posts() )
		{
			$lava_listing_posts->the_post();

			$arrMoreInfo = $arrTaxonomies = Array();

			if( !empty( $arrGetMoreInfo ) ) : foreach( $arrGetMoreInfo as $key => $meta ) {
				$arrMoreInfo[ $key ]	= get_post_meta( get_the_ID(), $key, true );
			} endif;

			if( !empty( $arrGetTaxonomies ) ) : foreach( $arrGetTaxonomies as $key => $meta ) {
				$arrTaxonomies[ $key ]	= lava_bpp_featured_terms( $key, get_the_ID(), false );
			} endif;

			$attachment_id				= apply_filters( 'lava_bpp_listing_featured_image_id', get_post_thumbnail_id(), get_the_ID() );
			$attachment_noimage			= apply_filters( 'lava_bpp_listing_featured_no_image', $lava_bpp_manager->image_url . 'no-image.png' );

			$thumbnail_meta				= wp_get_attachment_image_src( $attachment_id, Array( 20, 20 ) );
			$this_thumbnail				= isset( $thumbnail_meta[0] ) ? $thumbnail_meta[0] : $attachment_noimage ;

			$output_items[]				=
				Array(
					'post_title'		=> get_the_title()
					, 'post_date'		=> get_the_date()
					, 'permalink'		=> get_permalink()
					, 'author_name'		=> get_the_author_meta( 'display_name' )
					, 'thumbnail'		=> $this_thumbnail
					, 'meta'			=> $arrMoreInfo
					, 'term'			=> $arrTaxonomies
				);
		}
	}
	wp_reset_query();

	$big						= 999999999;

	$response[ 'data']			= $output_items;
	$response[ 'pagination']	= paginate_links(
		Array(
			'base'				=> str_replace( $big, '|%#%', esc_url( get_pagenum_link( $big ) ) )
			, 'format'			=> '%#%'
			, 'current'			=> $this_paged
			, 'total'			=> $lava_listing_posts->max_num_pages
		)
	);
	die( json_encode( $response ) );
}