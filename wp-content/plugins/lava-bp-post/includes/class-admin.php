<?php

class Lava_Bp_Post_Admin extends Lava_Bp_Post_Func {

	const __OPTION_GROUP__ = 'lava_bp_post_group';

	private $admin_dir;

	private static $form_loaded = false;
	private static $is_wpml_actived;
	private static $item_refresh_message;

	public $options;

	public function __construct() {
		$this->setVariables();
		$this->registerHooks();
		$this->loadFIles();
		do_action( "lava_{$this->post_type}_admin_class_init" );
	}

	public function setVariables() {
		$this->admin_dir = trailingslashit( dirname( __FILE__ ) . '/admin' );
		$this->post_type = self::SLUG;
		$this->featured_term = self::getFeaturedTerm();
		$this->options = get_option( $this->getOptionFieldName() );
		self::$is_wpml_actived = function_exists( 'icl_object_id' );
	}

	public function registerHooks() {

		// Admin Initialize
		add_action( 'admin_init', Array( $this, 'register_options' ) );
		add_action( 'admin_menu', Array( $this, 'register_setting_page' ) );
		add_action( 'admin_footer', Array( $this, 'admin_form_scripts' ) );
		add_action( 'save_post', Array( $this, 'save_post' ) );
		add_action( 'add_meta_boxes', Array( $this, 'reigster_meta_box' ), 0 );

		add_filter( "lava_{$this->post_type}_login_url", Array( $this, 'login_url' ) );

		// Custom Back-end column
		add_filter( 'manage_edit-' . $this->post_type . '_columns', Array( $this, 'add_manage_column' ), 8 );
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', Array( $this, 'custom_manage_column_content' ), 10, 2 );

		// Custom Category Marker
		add_action( 'admin_enqueue_scripts', Array( $this, 'admin_enqueue_callback' ) );

		add_action( 'lava_' . $this->post_type . '_admin_setting_page_before', array( $this, 'settingPageBefore' ) );
		add_action( 'lava_' . $this->post_type . '_admin_setting_page_after', array( $this, 'settingPageAfter' ) );
	}

	public function loadFIles() {
		require_once( 'functions-admin.php' );
	}

	public function settingPageBefore() {
		wp_localize_script(
			sanitize_title( lava_bpp()->enqueue->handle_prefix . 'admin.js' ),
			'lava_dir_admin_param',
			Array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ajax_hook' => sprintf( '%s_', $this->post_type ),
			)
		);
		wp_enqueue_script( sanitize_title( lava_bpp()->enqueue->handle_prefix . 'admin.js' ) );
	}

	public function settingPageAfter() {
		wp_enqueue_media();
	}

	public function getNoImage() {
		$output = lava_bpp()->image_url . 'no-image.png';
		$noimage = $this->get_settings( 'blank_image', false );
		if( $noimage ) {
			$output = $noimage;
		}
		return $output;
	}

	public function reigster_meta_box() {
		add_meta_box(
			'lava_bp_post_metabox',
			__( "Additional Meta", 'Lavacode' ),
			Array( $this, 'metabox' ),
			self::SLUG,
			'advanced', 'high'
		);
	}

	public function metabox( $post ) {
		global $post;

		self::$form_loaded		= 1;
		$lava_item_fields	= apply_filters( "lava_{$this->post_type}_more_meta", Array() );
		ob_start();
			do_action( "lava_{$this->post_type}_admin_metabox_before" , $post );
			require_once dirname( __FILE__) . '/admin/admin-metabox.php';
			do_action( "lava_{$this->post_type}_admin_metabox_after" , $post );
		ob_end_flush();
	}

	public function admin_form_scripts() {
		if( ! self::$form_loaded )
			return;

		wp_localize_script(
			sanitize_title( lava_bpp()->enqueue->handle_prefix . 'admin-metabox.js' ),
			'lava_bpp_admin_meta_args',
			Array(
				'fail_find_address'	=> __( "You are not the author.", 'lvbp-bp-post' )
			)
		);

		wp_enqueue_script( sanitize_title( lava_bpp()->enqueue->handle_prefix . 'admin-metabox.js' ) );
	}

	public function save_post( $post_id ) {

		if( ! is_admin() ) {
			return false;
		}

		$has_lavafield = isset( $_POST[ 'lava_pt' ] );
		$lava_query = new lava_Array( $_POST );
		$lava_PT = new lava_Array( $lava_query->get( 'lava_pt', Array() ) );
		$lava_mapMETA = $lava_query->get( 'lava_map_param' );
		$lava_moreMETA = $lava_query->get( 'lava_additem_meta' );

		// More informations
		if( !empty( $lava_moreMETA ) ) : foreach( $lava_moreMETA as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		} endif;

		// More detail picture or image ids meta
		if( $has_lavafield ) {
			update_post_meta( $post_id, 'detail_images', $lava_query->get( 'lava_attach' ) );
		}

		// Featured item meta
		update_post_meta( $post_id, '_featured_item', $lava_PT->get( 'featured', 0 ) );

		// Upldate Json
		do_action( "lava_{$this->post_type}_json_update", $post_id, get_post( $post_id ), null );

	}

	public function register_options() {
		register_setting( self::__OPTION_GROUP__ , $this->getOptionFieldName() );
	}

	public function getOptionFieldName( $option_name=false ){    // option field name

		$strFieldName = 'lava_bpp_settings';

		if( $option_name )
			$strFieldName = sprintf( '%1$s[%2$s]', $strFieldName, $option_name );

		return $strFieldName;
	}

	public function getOptionsPagesLists( $default=0 ) {
		$pages_output = Array();
		if(
			! $pages = get_posts(
				Array(
					'post_type' => 'page',
					'posts_per_page' => -1,
					'suppress_filters' => false,
				)
			)
		) return false;

		$default = $this->wpml_post_id( $default, 'page' );

		foreach( $pages as $page ) {
			$pages_output[]	= "<option value=\"{$page->ID}\"";
			$pages_output[]	= selected( $default == $page->ID, true, false );
			$pages_output[]	= ">{$page->post_title}</option>";
		}

		return @implode( false, $pages_output );
	}

	public function register_setting_page() {
		add_submenu_page(
			'edit.php',
			__( "Lava Bp Post Setting", 'lvbp-bp-post' ),
			__( "Settings", 'lvbp-bp-post' ),
			'manage_options',
			'lava-' . self::SLUG . '-settings',
			Array( $this, 'admin_page_template' )
		);
	}

	public function admin_page_template() {
		global $lava_bpp_manager;
		do_action( 'lava_' . $this->post_type . '_admin_setting_page_before' );

		$arrTabs_args		= Array(
			''				=>	Array(
				'label'		=> __( "Home", 'lvbp-bp-post' )
				, 'group'	=> self::__OPTION_GROUP__
				, 'file'	=> $this->admin_dir . 'admin-index.php'
			)
		);

		$arrTabs = apply_filters( "lava_{$this->post_type}_admin_tab", $arrTabs_args );

		echo self::$item_refresh_message;
		echo "<div class=\"wrap\">";
			printf( "<h2>%s</h2>", __( "Lava Bp Post Setting", 'lvbp-bp-post' ) );
			echo "<form method=\"post\" action=\"options.php\">";
			echo "<h2 class=\"nav-tab-wrapper\">";
			$strCurrentPage	= isset( $_GET[ 'index' ] ) && $_GET[ 'index' ] != '' ? sanitize_text_field( $_GET[ 'index' ] ) : '';
			if( !empty( $arrTabs ) ) : foreach( $arrTabs as $key => $meta ) {
					printf(
						"<a href=\"%s\" class=\"nav-tab %s\">%s</a>"
						, esc_url(
								add_query_arg(
									Array(
										'page' => 'lava-' . self::SLUG . '-settings'
										, 'index' => $key
									)
									, admin_url( 'edit.php' )
								)
							)
						, ( $strCurrentPage == $key ? 'nav-tab-active' : '' )
						, $meta[ 'label' ]
					);

				}
				echo "</h2>";
				if( $strTabMeta = $arrTabs[ $strCurrentPage ] ) {
					settings_fields( $strTabMeta[ 'group' ] );
					if( file_exists( $strTabMeta[ 'file' ] ) )
						require_once $strTabMeta[ 'file' ];
				}
			endif;

			if( apply_filters( "lava_{$this->post_type}_admin_save_button", true ) )
				printf( "<button type=\"\" class=\"button button-primary\">%s</button>", __( "Save", 'lvbp-bp-post' ) );

			echo "</form>";
			echo "<form id=\"lava_common_item_refresh\" method=\"post\">";
			wp_nonce_field( "lava_{$this->post_type}_items", "lava_{$this->post_type}_refresh" );
			echo "<input type=\"hidden\" name=\"lang\">";
			echo "</form>";
		echo "</div>";
		do_action( 'lava_' . $this->post_type . '_admin_setting_page_after' );
	}

	public function admin_welcome_template() {
		if( file_exists( $this->admin_dir . 'admin-welcome.php' ) )
			require_once $this->admin_dir . 'admin-welcome.php';
	}

	public function get_settings( $option_key, $default=false ) {
		if( array_key_exists( $option_key, (Array) $this->options ) )
			if( $value = $this->options[ $option_key ] )
				$default = $value;
		return $default;
	}

	public function set_setting( $option_key, $option_value=false ) {
		$options = is_array( $this->options ) ? $this->options : Array();
		$options[ $option_key ] = $option_value;
		update_option( $this->getOptionFieldName(), $options );
		$this->options = $options;
	}

	public function noimage( $image_url ) {
		if( $noimage = $this->get_settings( 'blank_image' ) )
			return $noimage;
		return $image_url;
	}

	public function login_url( $login_url ) {
		if( $redirect = $this->get_settings( 'login_page' ) )
			return get_permalink( $redirect );
		return $login_url;
	}

	public function add_manage_column( $columns ) {
		return wp_parse_args(
			$columns,
			Array(
				'cb'				=> '<input type="checkbox">',
				'thumbnail'	=> __( "Thumbnail", 'lvbp-bp-post' ),
			)
		);
	}

	public function custom_manage_column_content( $cols_id, $post_id=0 ) {
		switch( $cols_id ) {
			case 'thumbnail':
				the_post_thumbnail();
				break;
		}
	}

	public function admin_enqueue_callback(){
		if ( function_exists( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
	}

	public function getTermOption( $tag=null, $key='', $default='' ) {
		if( !is_object( $tag ) )
			return $default;

		$strKeyName = sprintf( 'lava_%1$s_%2$s_%3$s', $tag->taxonomy, $tag->term_id, $key );
		return get_option( $strKeyName, $default );
	}

	public function setTermOption( $tag=null, $key='', $value='' ) {
		if( !is_object( $tag ) )
			return false;

		$strKeyName = sprintf( 'lava_%1$s_%2$s_%3$s', $tag->taxonomy, $tag->term_id, $key );
		return update_option( $strKeyName, $value );
	}

	public function lava_file_script_callback(){
		wp_localize_script(
			sanitize_title( lava_bpp()->enqueue->handle_prefix . 'admin-edit-term.js' ),
			'lv_edit_featured_taxonomy_variables',
			Array(
				'mediaBox_title'		=> __( "Select Category Featured Image", 'lvbp-bp-post' ),
				'mediaBox_select'	=> __( "Apply", 'lvbp-bp-post' ),
			)
		);
		wp_enqueue_script( sanitize_title( lava_bpp()->enqueue->handle_prefix . 'admin-edit-term.js' ) );
	}

}