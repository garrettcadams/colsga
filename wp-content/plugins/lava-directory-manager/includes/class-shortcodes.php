<?php

class Lava_Directory_Manager_Shortcodes{

	const ACCEPT = true;
	const EXCEPT = false;

	public function __construct() {
		$this->post_type = lava_directory()->core->getSlug();

		add_shortcode( 'lava_directory_listing', Array( $this, 'listings' ) );
		add_shortcode( 'lava_directory_form', Array( $this, 'form' ) );
		add_shortcode( 'lava_directory_mypage', Array( $this, 'dashboard' ) );

		require_once "functions-ajaxListings.php";
	}

	public function _array( &$arrTax = Array(), $strPosition ) {

		if( ! is_Array( $arrTax ) || empty( $arrTax ) )
			return $arrTax;

		switch( $strPosition ) {
			case 'current'	: current( $arrTax ); break;
			case 'next'		: next( $arrTax ); break;
			case 'prev'		: prev( $arrTax ); break;
			case 'first'	: reset( $arrTax ); break;
			case 'last'		:
			default			: end( $arrTax ); break;
		}
		return key( $arrTax );
	}

	public function listings( $attr, $content='' ) {
		global $lava_directory_manager;

		$post_type			= $this->post_type;

		// Variables initialize
		$output_template	= trailingslashit( $lava_directory_manager->template_path );
		$optAddressField	= false;

		add_action( 'wp_footer', Array( $this, '_listings_enqueues' ) );
		ob_start();
		require_once $output_template . 'template-listing.php';
		return ob_get_clean();
	}

	public function _listings_enqueues() {
		wp_enqueue_script( 'lava-directory-manager-lava-listing-js' );
	}

	public function form( $attr, $content='' ) {
		global
			$post,
			$wpdb,
			$lava_directory_manager;

		do_action( "lava_{$this->post_type}_form_shortcode_before" );

		if( is_object( $post ) )
			$post->comment_close		= true;

		// If logged User ?
		if('member' ==  lava_directory_manager_get_option( 'add_capability' ) )
			if( self::ACCEPT !== ( $cReturn = self::is_available_shortcode() ) ) return $cReturn;

		// If current user has modify permission ?
		if( $is_edit = intVal( get_query_var( 'edit' ) ) )
			if( self::ACCEPT !== ( $cReturn = self::is_can_modify( $is_edit ) ) ) return $cReturn;

		// Get Request variables
		$lava_query			= new lava_Array( $_POST );

		if( ! $wpdb->get_var( "select ID from {$wpdb->posts} where ID={$is_edit}" ) ) {
			// Initialze for edit variable.
			$edit					= new stdClass();
			$edit->ID				=
			$edit->post_title		=
			$edit->post_content		=
			$edit->post_author		= false;
		}else{
			$edit = get_post( $is_edit );
		}

		$latlng = Array();
		foreach(
			Array( 'lat', 'lng', 'street_lat', 'street_lng', 'street_pitch', 'street_heading', 'street_zoom', 'street_visible' )
			as $index
		) $edit->$index = floatVal( get_post_meta( $edit->ID, 'lv_listing_' . $index, true ) );

		foreach(
			Array( 'country', 'locality', 'political', 'political2', 'address', 'zipcode' )
			as $index
		) $edit->$index = get_post_meta( $edit->ID, 'lv_listing_' . $index, true );

		$edit->arrAttach = get_post_meta( $edit->ID, 'detail_images', true );

		$GLOBALS[ 'edit' ] = $edit;
		add_action( 'wp_footer', Array( $this, '_form_enqueues' ) );

		ob_start();

		$strFormFile = apply_filters(
			"lava_{$this->post_type}_form_loadFile"
			, trailingslashit( $lava_directory_manager->template_path ) . 'template-addItem.php'
		);

		if( file_exists( $strFormFile ) )
			require_once $strFormFile;

		do_action( "lava_{$this->post_type}_form_shortcode_after" );
		return ob_get_clean();
	}

	public function _form_enqueues() {
		wp_enqueue_script( 'jquery-form' );
		wp_localize_script(
			lava_directory()->enqueue->getHandleName( 'lava-submit-script.js' ),
			'lava_directory_manager_submit_args',
			Array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ajaxhook' => lava_directory()->submit->ajaxhook,
				'post_id' => isset( $GLOBALS[ 'edit' ] ) ? $GLOBALS[ 'edit' ]->ID : 0,
				'images' => Array(
					'loading' => lava_directory()->image_url . 'loading.gif',
				),
				'maps' => Array(
					'restrictions_country' => lava_directory()->admin->get_settings( 'restrictions_country', 'all' ),
				),
				'required' => Array(
					'amenities' => in_array( 'listing_amenities', lava_directory()->admin->get_settings( 'required_fields', Array() ) ),
					'featured_image' => in_array( 'featured_image', lava_directory()->admin->get_settings( 'required_fields', Array() ) ),
					'detail_images' => in_array( 'detail_images', lava_directory()->admin->get_settings( 'required_fields', Array() ) ),
					'map' => in_array( 'map', lava_directory()->admin->get_settings( 'required_fields', Array() ) ),
				),
				'strings' => Array(
					'success' => esc_html__( "has been saved successfully.", 'Lavacode' ),
					'download' => esc_html__( "Download", 'Lavacode' ),
					'btn_remove' => esc_html__( "Remove", 'Lavacode' ),
					'not_found' => esc_html__( "Required field is empty : ", 'Lavacode' ),
					'not_found_featured' => esc_html__( "Please add a featured image.", 'Lavacode' ),
					'not_found_detail_images' => esc_html__( "Please add a Detail images.", 'Lavacode' ),
					'not_found_amenity' => esc_html__( "Please choose at least one option.", 'Lavacode' ),
					'not_found_map' => esc_html__( "Map information is required.", 'Lavacode' ),
					'limitDetailImages' => sprintf( esc_html( "Limited amount of images : %s ( You have uploaded : %s )", 'Lavacode' ), '{limit}', '{count}' ),
				),
			)
		);
		wp_enqueue_script( lava_directory()->enqueue->getHandleName( 'lava-submit-script.js' ) );
	}

	public function dashboard( $attr, $content='' ) {

		$lavaDashBoardArgs = shortcode_atts(
			Array(
				'type' => 'all',
			), $attr
		);

		if( self::ACCEPT !== ( $cReturn = self::is_available_shortcode() ) ) return $cReturn;

		add_action( 'wp_footer', Array( $this, '_dashboard_enqueues' ) );

		$GLOBALS[ 'lava_directory_stc_mmypage' ] = new lava_Array( $attr );
		$output_template = trailingslashit( lava_directory()->template_path );

		ob_start();
		require( $output_template . 'template-dashboard.php' );
		return ob_get_clean();
	}

	public function _dashboard_enqueues() {
		wp_localize_script(
			lava_directory()->enqueue->getHandleName( 'lava-dashboard.js' ),
			'lava_dir_dashboard_args',
			Array(
				'nonce' => wp_create_nonce( 'item_delete' ),
				'strings' => array(
					'strDeleteConfirm' => esc_html__( "Do you want to delete this item?", 'Lavacode' ),
				),
			)
		);
		wp_enqueue_script( lava_directory()->enqueue->getHandleName( 'lava-dashboard.js' ) );
	}

	public function is_available_shortcode() {
		$post_type = lava_directory()->core->getSlug();
		$lava_loginURL = apply_filters( "lava_{$post_type}_login_url", wp_login_url() );

		if( ! is_user_logged_in() ) {
			return sprintf(
				"<div class='notice' align='center'>
					<a href=\"%s\">%s</a>
				</div>"
				, $lava_loginURL
				,__( "Please login", 'Lavacode' )
			);
		}
		return self::ACCEPT;
	}

	public function is_can_modify ( $tID = 0 )
	{
		$post	= get_post( $tID );

		if( ! is_object( $post ) ){
			return sprintf( "<div class='notice'>%s</div>", __( "Invaild Post ID.", 'Lavacode' ) );
		}

		if( $post->post_author != get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
			return sprintf( "<div class='notice'>%s</div>", __( "You are not the author.", 'Lavacode' ) );
		}
		return self::ACCEPT;
	}
}