<?php

class Lava_Bp_Post_Shortcodes{

	public $shortcodes = Array();

	Const STR_SHORTCODE_PREFIX = 'lava_bp_%s_%s';
	Const ACCEPT = true;
	Const EXCEPT = false;

	public function __construct() {
		$this->post_type = lava_bpp()->core->getSlug();

		$this->shortcodes[ 'listing' ] = Array( $this, 'listings' );
		$this->shortcodes[ 'form' ] = Array( $this, 'form' );
		$this->shortcodes[ 'mypage' ] = Array( $this, 'dashboard' );

		add_action( 'init', array( $this, 'createShortcodes' ), 15 );
		$this->load_files();
	}

	public function getShortcodeName( $name='' ) {
		return sprintf( self::STR_SHORTCODE_PREFIX, $this->post_type, $name );

	}

	public function createShortcodes() {
		$arrShortcodes = apply_filters( 'lava_' . $this->post_type . '_bpp_shortcodes', $this->shortcodes, $this );
		if( empty( $arrShortcodes ) || !is_array( $arrShortcodes ) ) {
			return false;
		}
		foreach( $arrShortcodes as $strShortcode => $fnCallBack ) {
			add_shortcode( $this->getShortcodeName( $strShortcode ), $fnCallBack );
		}
	}

	public function load_files() {
		require_once( 'functions-ajaxListings.php' );
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
		$post_type = $this->post_type;
		$optAddressField = false;

		// Variables initialize
		$output_template = trailingslashit( lava_bpp()->template_path );

		add_action( 'wp_footer', Array( $this, '_listings_enqueues' ) );
		ob_start();
		require_once $output_template . 'template-listing.php';
		return ob_get_clean();
	}

	public function _listings_enqueues() {
		wp_enqueue_script( 'lava-bpp-lava-listing-js' );
	}

	public function form( $attr, $content='' ) {
		global $post, $wpdb;

		do_action( "lava_{$this->post_type}_form_shortcode_before" );

		if( $post instanceof WP_Post ) {
			$post->comment_close = true;
		}

		// If logged User ?
		if('member' ==  lava_bpp_get_option( 'add_capability' ) )
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

		$latlng						= Array();
		foreach(
			Array( 'lat', 'lng', 'street_lat', 'street_lng', 'street_pitch', 'street_heading', 'street_zoom', 'street_visible', 'country', 'locality', 'address' )
			as $index
		) $edit->$index = floatVal( get_post_meta( $edit->ID, 'lv_listing_' . $index, true ) );

		$edit->arrAttach = get_post_meta( $edit->ID, 'detail_images', true );

		$GLOBALS[ 'edit' ] = $edit;
		add_action( 'wp_footer', Array( $this, '_form_enqueues' ) );

		ob_start();

		$strFormFile = apply_filters(
			"lava_{$this->post_type}_form_loadFile",
			trailingslashit( lava_bpp()->template_path ) . 'template-addItem.php'
		);

		if( file_exists( $strFormFile ) )
			require_once $strFormFile;

		do_action( "lava_{$this->post_type}_form_shortcode_after" );
		return ob_get_clean();
	}

	public function _form_enqueues() {
		wp_enqueue_script( 'jquery-form' );
		wp_enqueue_script( lava_bpp()->enqueue->getHandleName( 'scripts.js' ) );
		wp_localize_script(
			lava_bpp()->enqueue->getHandleName( 'lava-submit-script.js' ),
			'lava_bpp_submit_args',
			Array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ajaxhook' => lava_bpp()->submit->ajaxhook,
				'post_id' => isset( $GLOBALS[ 'edit' ] ) ? $GLOBALS[ 'edit' ]->ID : 0,
				'images' => Array(
					'loading' => lava_bpp()->image_url . 'loading.gif',
				),
				'strings' => Array(
					'success' => esc_html__( "has been saved successfully.", 'lvbp-bp-post' ),
					'download' => esc_html__( "Download", 'lvbp-bp-post' ),
					'btn_remove' => esc_html__( "Remove", 'lvbp-bp-post' ),
					'limitDetailImages' => sprintf( esc_html( "Limited amount of images : %s ( You have uploaded : %s )", 'lvbp-bp-post' ), '{limit}', '{count}' ),
				),
			)
		);
		wp_enqueue_script( lava_bpp()->enqueue->getHandleName( 'lava-submit-script.js' ) );
	}

	public function dashboard( $attr, $content='' ) {

		$lavaDashBoardArgs = shortcode_atts(
			Array(
				'type' => 'all',
				'guest' => 'false',
				'author' => false,
				'count' => false,
				'title' => esc_html__( "My Posts", 'lvbp-bp-post' ),
			), $attr
		);

		if( 'true' !== $lavaDashBoardArgs[ 'guest' ] ) {
			if( self::ACCEPT !== ( $cReturn = self::is_available_shortcode() ) ) {
				return $cReturn;
			}
		}

		add_action( 'wp_footer', Array( $this, '_dashboard_enqueues' ) );

		$GLOBALS[ 'lava_bpp_stc_mmypage' ] = new lava_Array( $attr );
		$output_template = trailingslashit( lava_bpp()->template_path );

		ob_start();
		require( $output_template . 'template-dashboard.php' );
		return ob_get_clean();
	}

	public function _dashboard_enqueues() {
		wp_localize_script(
			lava_bpp()->enqueue->getHandleName( 'lava-dashboard.js' ),
			'lava_dir_dashboard_args',
			Array(
				'nonce' => wp_create_nonce( 'item_delete' ),
				'strings' => array(
					'strDeleteConfirm' => esc_html__( "Do you want to delete this item?", 'lvbp-bp-post' ),
				),
			)
		);
		wp_enqueue_script( lava_bpp()->enqueue->getHandleName( 'lava-dashboard.js' ) );
	}

	public function is_available_shortcode() {
		$lava_loginURL = apply_filters( "lava_{$this->post_type}_login_url", wp_login_url() );

		if( ! is_user_logged_in() ) {
			return sprintf(
				"<div class='notice' align='center'>
					<a href=\"%s\">%s</a>
				</div>"
				, $lava_loginURL
				,__( "Please login", 'lvbp-bp-post' )
			);
		}
		return self::ACCEPT;
	}

	public function is_can_modify ( $tID = 0 )
	{
		$post	= get_post( $tID );

		if( ! is_object( $post ) ){
			return sprintf( "<div class='notice'>%s</div>", __( "Invaild Post ID.", 'lvbp-bp-post' ) );
		}

		if( $post->post_author != get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
			return sprintf( "<div class='notice'>%s</div>", __( "You are not the author.", 'lvbp-bp-post' ) );
		}
		return self::ACCEPT;
	}
}