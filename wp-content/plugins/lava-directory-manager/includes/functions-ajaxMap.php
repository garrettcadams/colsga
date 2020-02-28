<?php
// add_action( 'wp_ajax_lava_map_info_window_content'			, 'lava_map_info_window_content' );
// add_action( 'wp_ajax_nopriv_lava_map_info_window_content'	, 'lava_map_info_window_content' );

function lava_map_info_window_content() {
	header( 'Content-Type: application/json; charset=utf-8' );

	$lava_query					= new lava_Array( $_POST );
	$lava_result				= Array( "state" => "fail" );

	if( false !== ( $post_id = $lava_query->get( "post_id", false ) ) )
	{
		$post					= get_post( $post_id );

		//
		if( false == ( $lava_this_author		= get_userdata( $post->post_author ) ) )
		{
			$lava_this_author					= new stdClass();
			$lava_this_author->display_name		= '';
			$lava_this_author->avatar			= 0;
		}


		// Post Thumbnail
		if( '' !== ( $lava_this_thumb_id		= $lava_this_author->avatar ) )
			{
				$lava_this_thumb_url			= wp_get_attachment_image_src( $lava_this_thumb_id , 'lava-box-v' );

				if( isset( $lava_this_thumb_url ) ) {
					$lava_this_thumb			= $lava_this_thumb_url[0];
				}
			}


			// If not found this post a thaumbnail
			if( empty( $lava_this_thumb ) )
			{
				$lava_this_thumb		= ''; //$lava_tso->get( 'no_image', LAVA_IMG_DIR.'/no-image.png' );

			}
			$lava_this_thumb		= apply_filters( 'lava_map_list_thumbnail', $lava_this_thumb, $post );
			$lava_this_thumb	= "<div class=\"lava-thb\" style=\"background-image:url({$lava_this_thumb});\"></div>";

		// Other Informations
		$lava_result			= Array(
			'state'				=> 'success'
			, 'post_id'			=> $post->ID
			, 'post_title'		=> $post->post_title
			, 'permalink'		=> get_permalink( $post->ID )
			, 'thumbnail'		=> $lava_this_thumb
			, 'category'		=> current( wp_get_object_terms( $post->ID, 'listing_category', Array( 'fields' => 'names' ) ) )
			, 'location'		=> current( wp_get_object_terms( $post->ID, 'listing_category', Array( 'fields' => 'names' ) ) )
			, 'author_name'		=> $lava_this_author->display_name
		);
	}
	die( json_encode( $lava_result ) );
}



add_action( 'wp_ajax_nopriv_lava_' . self::SLUG . '_map_list'	, 'lava_map_listings_contents' );
add_action( 'wp_ajax_lava_' . self::SLUG . '_map_list'			, 'lava_map_listings_contents' );
function lava_map_listings_contents() {
	global
		$post
		, $lava_favorite
		, $lava_directory_manager;

	header( 'Content-Type: application/json; charset=utf-8' );

	$post_ids					= isset( $_REQUEST['post_ids'] ) ? (Array)$_REQUEST['post_ids'] : Array();
	$lava_result				= Array();

	foreach( $post_ids as $post_id )
	{

		if( null !== ( $post = get_post( $post_id ) ) )
		{

			// Get Ratings
			// $lava_rating					= new lava_RATING( $post->ID );

			$lava_author					= get_userdata( $post->post_author );
			$lava_author_name				= isset( $lava_author->display_name ) ? $lava_author->display_name : null;
			$lava_has_author				= isset( $post->post_author );

			$lv_listing_location = lava_directory_featured_terms( 'listing_location', $post->ID, false )!= '' ? lava_directory_featured_terms( 'listing_location', $post->ID, false ) : 'No City';
			$lv_listing_category = lava_directory_featured_terms( 'listing_category', $post->ID, false )!='' ? lava_directory_featured_terms( 'listing_category', $post->ID, false ) : 'No Type';

			$attachment_noimage				= apply_filters( 'lava_directory_listing_featured_no_image', $lava_directory_manager->image_url . 'no-image.png' );

			/* Post Thumbnail */ {
				$lava_this_thumb			= '';
				if( '' !== ( $lava_this_thumb_id = get_post_thumbnail_id( $post->ID ) ) ) {
					$lava_this_thumb_url	= wp_get_attachment_image_src( $lava_this_thumb_id , 'thumbnail' );
				}
				$lava_this_thumb			= isset( $lava_this_thumb_url[0] ) ? $lava_this_thumb_url[0] : $attachment_noimage ;

				$lava_this_thumb			= apply_filters( 'lava_map_list_thumbnail', $lava_this_thumb, $post );

				// If not found this post a thaumbnail
				if( empty( $lava_this_thumb ) ) {
					$lava_this_thumb		= null;
				}
				$lava_this_thumb_large		= "<div class=\"lava-thb\" style=\"background-image:url({$lava_this_thumb});\"></div>";
			}

			/* Near place search */{

				$lava_place_results		= Array();
				$lava_lat = $lava_lng	= null;

				if(
					( $lava_lat = get_post_meta( $post->ID, 'lv_listing_lat', true ) ) &&
					( $lava_lng = get_post_meta( $post->ID, 'lv_listing_lng', true ) ) &&
					isset( $_REQUEST['place'] )
				) {
					$lava_place_query			= Array(
						// 'key'					=> $lava_tso->get( 'google_api', 'AIzaSyDqixDRi7EBUxcbE0cLjYIw-NlB6RFKqyI' )
						'key'					=> 'AIzaSyDqixDRi7EBUxcbE0cLjYIw-NlB6RFKqyI'
						, 'sensor'				=> 'false'
						, 'radius'				=> 5000
						, 'location'			=> "{$lava_lat},{$lava_lng}"
						, 'types'				=> 'airport|bus_station|train_station'
					);

					foreach( Array( 'commute', 'location' ) as $type )
					{

						if( $type == 'location' )
							$lava_place_query['types'] = "bank";

						$lava_place_query_url		= "https://maps.googleapis.com/maps/api/place/nearbysearch/json?";
						foreach( $lava_place_query as $key => $value )
							$lava_place_query_url	.= "{$key}={$value}&";

						$lava_place_query_url		= substr( $lava_place_query_url, 0, -1 );
						$lava_place_response		= wp_remote_get( $lava_place_query_url, Array( 'header' => Array( 'Content-type' => 'application/json' ) ) );
						$lava_place_result			= wp_remote_retrieve_body( $lava_place_response );

						$lava_place_results[$type]	= json_decode( $lava_place_result );
					}
				}
			}

			// Other Informations
			$additional_item		= Array(
				'post_id'			=> $post->ID
				, 'post_title'		=> $post->post_title
				, 'post_content'	=> $post->post_content
				, 'post_date'		=>
					sprintf(
						__( "%s ago", 'Lavacode' )
						, human_time_diff(
							date( 'U', strtotime( $post->post_date ) )
							, current_time( 'timestamp' )
						)
					)
				, 'excerpt'			=> $post->post_excerpt
				, 'thumbnail_large'	=> $lava_this_thumb_large
				, 'thumbnail_url'	=> $lava_this_thumb
				, 'permalink'		=> get_permalink( $post->ID )
				, 'author_name'		=> $lava_author_name
				, 'f'				=> get_post_meta( $post->ID, '_featured_item', true )
				, 'place'			=> $lava_place_results
				, 'lat'				=> $lava_lat
				, 'lng'				=> $lava_lng
				, 'listing_location' => $lv_listing_location
				, 'listing_category' => $lv_listing_category
			);

			if( 'use' === get_post_meta( $post->ID, '_featured_item', true ) )
				$additional_item[ 'featured' ] = 'yes';

			$lava_result[]			= apply_filters( 'lava_multiple_listing_contents', $additional_item, $post->ID );
		} // End If
	} // End foreach
	die( json_encode( $lava_result ) );
}

add_action( 'wp_ajax_nopriv_lava_' . self::SLUG . '_get_json', 'lava_directory_map_get_json' );
add_action( 'wp_ajax_lava_' . self::SLUG . '_get_json', 'lava_directory_map_get_json' );
function lava_directory_map_get_json(){

	check_ajax_referer( 'lava_' . Lava_Directory_Manager_Func::SLUG . '_get_json', 'security' );

	$strCallBack = isset( $_GET[ 'callback' ] ) ? $_GET[ 'callback' ] : false;
	$strFilename = isset( $_GET[ 'fn' ] ) ? $_GET[ 'fn' ] : false;

	$upload_folder	= wp_upload_dir();

	$strFilename = str_replace( '..', '.', $strFilename );

	if( '' !== $strCallBack && '' !== $strFilename ) {
		$json_file		= "{$upload_folder['basedir']}/{$strFilename}.json";
		if( file_exists( $json_file ) ) {
			$content = file_get_contents( $json_file );
			$output = "{$strCallBack}({$content})";
			die( $output );
		}
	}
	die;
}
