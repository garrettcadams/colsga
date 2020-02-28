<?php

class Lava_Directory_Manager_Submit {

	Const AJAXHOOK_FORMAT = 'lava_%s_manager_';

	Const STR_CATE_FEATURES = 'listing_amenities';

	Const STATE_OK = 'OK';
	Const STATE_FAILED = 'FAIL';
	public $post_type = false;
	public $ajaxhook = false;

	public function __construct() {
		$this->setVariables();
		$this->registerHooks();
		$this->registerAjaxHooks();
	}

	public function setVariables() {
		$this->post_type = lava_directory()->core->getSlug();
		$this->name = lava_directory()->core->getName();
		$this->ajaxhook = sprintf( self::AJAXHOOK_FORMAT, $this->post_type );
	}

	public function registerHooks() {
		add_action( 'wp_head', Array( $this, 'debug' ) );
		add_action( 'init', array( $this, 'initialize' ), 15 );
	}

	public function registerAjaxHooks() {
		foreach(
			array(
				'submit_item' => array( $this, 'submit' ),
				'upload_file' => array( $this, 'upload_attachment' ),
				'get_attachment_info' => array( $this, 'attachment_info' ),
				'get_amenities_fields' => array( $this, 'getAmenitiesFields' ),
			) as $hookSuffix => $arrFunc
		) {
			add_action( 'wp_ajax_' . $this->ajaxhook . $hookSuffix, $arrFunc );
			add_action( 'wp_ajax_nopriv_' . $this->ajaxhook . $hookSuffix, $arrFunc );
		}
	}

	public function initialize() {
		if(
			isset( $_POST[ 'action' ] ) && isset( $_POST[ 'security' ] ) &&
			$_POST[ 'action' ] == 'lava_directory_manager_mypage_delete' && wp_verify_nonce( $_POST[ 'security' ], 'item_delete' )
		) $this->remove();
	}

	public function edit_button( $post_content ) {
		global $lava_directory_manager_func;

		if( ! is_singular( $this->post_type ) )
			return $post_content;

		if( get_current_user_ID() !== get_the_author_meta( 'ID' ) )
			return $post_content;

		if( ! $edit_page_id = intVal( lava_directory_manager_get_option( "page_add_{$this->post_type}" ) ) )
			return $post_content;

		if( ! $edit_page_link = get_permalink( $edit_page_id ) )
			return $post_content;

		$output_content		= Array();
		$output_content[]	= sprintf(
			"<div><a href=\"%s\">%s</a></div>"
			, esc_url( add_query_arg( Array( 'edit' => get_the_ID() ), $edit_page_link ) )
			, __( "Edit", 'Lavacode' )
		);

		$output_content[]	= $post_content;
		return @implode( false, $output_content);
	}


	public function upload_attachment() {
		$response = Array( 'STATE' => self::STATE_FAILED );
		if( isset( $_FILES[ 'source' ] ) ) {
			$fileFeaturedImage = wp_handle_upload( $_FILES[ 'source' ], Array( 'test_form' => 0 ) );
			$detailID = wp_insert_attachment(
				Array(
					'post_title' => sanitize_title( basename( $_FILES[ 'source' ][ 'name' ] ) ),
					'post_mime_type' => $fileFeaturedImage[ 'type' ],
					'guid' => $fileFeaturedImage[ 'url' ]
				),
				$fileFeaturedImage[ 'file' ]
			);
			$strFeaturedImageMeta = wp_generate_attachment_metadata( $detailID, $fileFeaturedImage[ 'file' ] );
			wp_update_attachment_metadata( $detailID, $strFeaturedImageMeta );
			$response[ 'STATE' ] = self::STATE_OK;
			$response[ 'ID' ] = $detailID;
		}
		wp_send_json($response);
	}

	public function getAttachmentInfo( $post_id=0 ) {
		$output = Array();
		$post = get_post( $post_id );
		if(!$post instanceof \WP_Post) {
			return $output;
		}
		$mimeType = $post->post_mime_type;

		if( empty( $mimeType ) ) {
			return array( 'output' => esc_html__( "Inavild File", 'Lavacode' ) );
		}

		$mime = @explode( '/', $mimeType );

		$output[ 'ID' ] = $post->ID;
		$output[ 'mime' ] = $mimeType;
		$output[ 'type' ] = $mime[0];
		$output[ 'ext' ] = $mime[1];
		$output[ 'filename' ] = basename( get_attached_file( $post->ID ) );
		$output[ 'url' ] = wp_get_attachment_url( $post->ID );

		if( $output[ 'type' ] == 'image' ) {
			$output[ 'output' ] = wp_get_attachment_image( $post->ID  );
		}else{
			$output[ 'output' ] = sprintf(
				'<a href="%1$s">%2$s ( %3$s )</a>',
				$output[ 'url' ],
				$output[ 'filename' ],
				esc_html__( "Download", 'Lavacode' )
			);
		}

		return $output;
	}

	public function attachment_info() {
		$attachID = isset( $_POST[ 'id' ] ) ? intVal( $_POST[ 'id' ] ) : 0;
		wp_send_json( $this->getAttachmentInfo( $attachID ) );
	}

	public function getAmenitiesFields() {
		$output = Array();
		$arrTerms = isset( $_POST[ 'terms' ] ) ? (Array)$_POST[ 'terms' ]  : array();
		$intObjectId = isset( $_POST[ 'object_id' ] ) ? intVal( $_POST[ 'object_id' ] ) : 0;
		$arrFeatureIDs = Array();

		if( !empty( $arrTerms ) ) {
			foreach( array_filter( $arrTerms ) as $term_id ) {
				$arrFeatures =  lava_directory()->admin->getTermOption( get_term( $term_id, 'listing_category' ), 'features', array() );
				if( !empty( $arrFeatures ) && is_array( $arrFeatures ) ) {
					foreach( $arrFeatures as $features_id ) {
						$arrFeatureIDs[ $features_id ] = true;
					}
				}
			}
			if( !empty( $arrFeatureIDs ) ) {
				foreach( array_keys( $arrFeatureIDs ) as $intTermID ) {

					$objTerm = get_term( $intTermID );
					if( ! $objTerm instanceof WP_Term ) {
						continue;
					}
					$arrValue = wp_get_object_terms( $intObjectId, self::STR_CATE_FEATURES, Array( 'fields' => 'ids' ) );
					$output[] = Array(
						'checked' => in_array( $intTermID, $arrValue ),
						'term_id' => $objTerm->term_id,
						'name' => $objTerm->name,
					);
				}
			}
		}else{
			$objTerms = get_terms( array( 'taxonomy' => 'listing_amenities', 'hide_empty' => false )  );
			foreach( $objTerms as $objTerm ) {
				$output[] = Array(
					'checked' => false,
					'term_id' => $objTerm->term_id,
					'name' => $objTerm->name,
				);
			}
		}
		wp_send_json( Array(
			'title' => '<span class="field-required-star">*</span> ' . esc_html__( "Amenities", 'Lavacode' ),
			'output' => $output,
			'empty' => '', // '<span>' . esc_html__( "There is no amenities/features in this category", 'Lavacode' ) . '</span>',
		) );
	}

	public function submit() {

		$response = Array( 'state' => 'fail' );
		// check_ajax_referer( "lava_directory_manager_submit_{$this->post_type}", 'security' );

		$is_update = false;
		$is_publish = lava_directory_manager_get_option( "new_{$this->post_type}_status" ) !== 'pending';
		$is_publish = (boolean) apply_filters( "lava_{$this->post_type}_new_status", $is_publish );

		$lava_dashboardID	= lava_directory()->admin->get_settings( 'page_my_page' );

		try{

			$post_args		= Array();
			$lava_query		= new lava_Array( $_POST );
			$userID			= get_current_user_id();

			if( ! is_user_logged_in() ) {

				if( ! $user_email = $lava_query->get( 'user_email', false ) ) {
					throw new Exception( __( "Invaild User Email.", 'Lavacode' ) );
				}

				if( ! $user_pass = $lava_query->get( 'user_pass', false ) ) {
					throw new Exception( __( "Invaild User Password.", 'Lavacode' ) );
				}

				$user_login = str_replace( Array( '@', '.' ), '-', $user_email );
				// $user_pass = wp_generate_password();

				wp_clear_auth_cookie();

				$userID = wp_insert_user( compact( 'user_email', 'user_login', 'user_pass' ) );

				if( is_wp_error( $userID ) ) {
					throw new Exception( $userID->get_error_message() );
				}

				$user = new WP_User($userID);

				// Wordpress 4.9.0 compatibility
				wp_new_user_notification( $userID );
				wp_set_current_user( $userID );
				wp_set_auth_cookie( $userID );
				do_action( 'wp_login', $user->user_login, $user );
			}

			if( intVal( $lava_query->get( 'post_id', 0 ) )  > 0 ) {
				$is_update	= true;
			}

			$lava_taxonomies = $lava_query->get( 'lava_additem_terms', Array() );
			$lava_metafields = $lava_query->get( 'lava_additem_meta', Array() );
			$lava_locations = $lava_query->get( 'lava_location', Array() );

			$intDetailImageLimit = $this->getLimitDetailImages();

			if( isset( $_POST[ 'lava_additem_meta' ] ) && isset( $_POST[ 'lava_additem_meta' ][ 'detail_images' ] ) ) {
				if( 0 < $intDetailImageLimit && $intDetailImageLimit < sizeof( $_POST[ 'lava_additem_meta' ][ 'detail_images' ] ) ) {
					throw new Exception(
						sprintf(
							esc_html__( "Limited amount of images : %s ( You have uploaded : %s )", 'Lavacode' ),
							$intDetailImageLimit, sizeof( $_POST[ 'lava_additem_meta' ][ 'detail_images' ] )
						)
					);
					return false;
				}
			}

			$post_type		= $this->post_type;
			$post_title		= $lava_query->get( 'txt_title' );
			$post_content	= $lava_query->get( 'txt_content' );

			$post_args		= compact( 'post_type', 'post_title', 'post_content', 'post_status' );

			if( $is_update ) {
				$post_args['ID']			= $lava_query->get( 'post_id', 0 );
				$post_id					= wp_update_post( $post_args );
			}else{
				$post_args['post_status']	= $is_publish ? 'publish' : 'pending';
				// $post_args['post_status']	= 'pending';
				$post_id					= wp_insert_post( $post_args );
			}

			if( intVal( $post_id ) > 0 ) {
				$GLOBALS[ 'lava_directory_form_current_id' ] = $post_id;


				$arrLocation				= Array();

				if( isset( $_FILES[ 'lava_featured_file' ] ) && $_FILES[ 'lava_featured_file' ]['size'] > 0 ) {

					$fileFeaturedImage			= wp_handle_upload( $_FILES[ 'lava_featured_file' ], Array( 'test_form' => 0 ) );
					$featuredID					= wp_insert_attachment(
						Array(
							'post_title'		=> sanitize_title( basename( $_FILES[ 'lava_featured_file' ][ 'name' ] ) )
							, 'post_mime_type'	=> $fileFeaturedImage[ 'type' ]
							, 'guid'			=> $fileFeaturedImage[ 'url' ]
						)
						, $fileFeaturedImage[ 'file']
					);
					$strFeaturedImageMeta = wp_generate_attachment_metadata( $featuredID, $fileFeaturedImage[ 'file' ] );
					wp_update_attachment_metadata( $featuredID, $strFeaturedImageMeta );
					set_post_thumbnail( $post_id, $featuredID );
				}

				if( isset( $_POST[ 'featured_id' ] ) ) {
					if( '' != $_POST[ 'featured_id' ] ) {
						set_post_thumbnail( $post_id, $_POST[ 'featured_id' ] );
					}else{
						delete_post_thumbnail( $post_id );
					}
				}


				if( !empty( $lava_metafields ) && is_Array( $lava_metafields ) ) : foreach( $lava_metafields as $name => $values ) {
					update_post_meta( $post_id, $name, $values );
				} endif;


				if( !empty( $lava_taxonomies ) && is_Array( $lava_taxonomies ) ) : foreach( $lava_taxonomies as $taxonomy => $values ) {

					if( $taxonomy != $this->name . '_keyword' )
						$values = Array_map( 'intVal', $values );

					if(
						$taxonomy == $this->name . '_category' &&
						intVal( lava_directory()->admin->get_settings( 'limit_category', 0 ) ) > 0
					) $values = Array_splice(
						$values,
						0,
						intVal( lava_directory()->admin->get_settings( 'limit_category', 0 ) )
					);

					if(
						$taxonomy == $this->name . '_location' &&
						intVal( lava_directory()->admin->get_settings( 'limit_location', 0 ) ) > 0
					) $values = Array_splice(
						$values,
						0,
						intVal( lava_directory()->admin->get_settings( 'limit_location', 0 ) )
					);

					wp_set_object_terms( $post_id, $values, $taxonomy );
				} endif;

				if( !empty( $lava_locations ) && is_Array( $lava_locations ) ) : foreach( $lava_locations as $name => $values ) {

					update_post_meta( $post_id, "lv_listing_{$name}", $values );

					if( $name == 'locality' || $name == 'country' )
						$arrLocation[] = $values;

				} endif;

				update_post_meta( $post_id, '_location', $arrLocation );
				do_action( "lava_{$this->post_type}_json_update", $post_id, get_post( $post_id ), $is_update );

				$response[ 'state' ] = 'OK';

				/*
				if( get_post_status( $post_id ) === 'publish' ) {
					$strRedirect	= get_permalink( $post_id );
				}else{
					$strRedirect = intVal( $lava_dashboardID ) > 0 ? get_permalink( $lava_dashboardID ) : home_url();
				} */

				$strRedirect = get_permalink( $post_id );
				$response[ 'link']	= esc_url_raw( apply_filters( "lava_{$this->post_type}_new_item_redirect", $strRedirect, $post_id ) );

			}else{
				throw new Exception( __( "Please try again, failure to submit", 'Lavacode' ) );
			}
		} catch( Exception $e ) {
			wp_send_json( Array( 'err' => $e->getMessage() ) );
		}
		wp_send_json($response);
	}

	public function remove() {
		$is_deleted = false;
		$post_id = isset( $_POST[ 'post_id' ] ) ? intVal( $_POST[ 'post_id' ] ) : 0;
		$post = get_post( $post_id );

		if( $post instanceof WP_Post ) {
			if( $post->post_author == get_current_user_id() || current_user_can( 'manage_options' )  ) {
				$_result = wp_delete_post( $post->ID, true );
				if( ! is_wp_error( $_result ) ) {
					$is_deleted = true;
				}
			}
		}

		if( $is_deleted ) {
			$GLOBALS[ 'lava_dashboard_message' ] = esc_html__( "It has been deleted.", 'Lavacode' );
		}else{
			$GLOBALS[ 'lava_dashboard_message' ] = esc_html__( "You are not the author.", 'Lavacode' );
		}
	}

	public function getLimitDetailImages() {
		$intLimitOption = lava_directory()->admin->get_settings( 'limit_detail_images', 0 );
		return intVal( apply_filters( 'lava_' . $this->post_type . '_limit_detail_images', $intLimitOption ) );
	}

	public function debug() {}

}