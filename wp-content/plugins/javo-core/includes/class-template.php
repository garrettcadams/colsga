<?php
class Jvbpd_Core_Template extends Jvbpd_Core {

	Const PREVIEW_KEY_FORMAT = 'single_%1$s_%2$s_preview_type';
	Const CORE_PREFIX_FORMAT = 'jvbpd_%s_';

	Public $prefix = 'jvbpd_';
	Public $core_prefix = '';

	public $templates = array();

	Public $is_archive = false;

	Private $map_slug = '';

	Private $filter_position = false;

	Private $single_type = 'type-grid';
	Private $support_type = 'type-c';

	Public $is_review_plugin_active = false;
	Public $is_cross_domain = false;
	Public $strGetJsonHook = false;
	Public $json_file = '';

	public function __construct() {
		add_filter( 'template_include', array( $this, 'map_template' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_script' ), 9999, 1 );

		add_action( 'init', Array( $this, 'get_lava_jsonfile' ), 99 );

		$this->setVariables();
		$this->loadMapTemplate();
		$this->registerHooks();
		$this->woo_func();

		$this->createCustomThemeTemplate();
	}

	public function map_template( $template='' ) {
		$post = get_queried_object();
		if( $post instanceof WP_Post ) {
			$template = 'lava_lv_listing_map' == get_post_meta( $post->ID, '_wp_page_template', true ) ? $this->initMapTemplate() : $template;
		}
		return $template;
	}

	public function register_scripts() {
		$scripts = Array(
			'lightgallery-all' => Array(
				'enqueue' => true,
				'src' => '../../dist/js/lightgallery-all.min.js',
			),
			'noUISlider' => Array(
				'src' => '../../dist/js/noUISlider.min.js',
			),
			'masonry' => Array(
				'handle' => 'masonry.pkgd',
				'enqueue' => true,
				'src' => '../../dist/js/masonry.pkgd.min.js',
			),
			'imagesloaded' => Array(
				'enqueue' => true,
				'src' => '../../dist/js/imagesloaded.pkgd.min.js',
			),
			'modernizr' => Array(
				'enqueue' => true,
				'src' => '../../dist/js/modernizr.min.js',
			),
			'classie' => Array(
				'enqueue' => true,
				'src' => '../../dist/js/classie.min.js',
			),
			'jvbpd-listing-single' => Array(
				'src' => 'single.js',
			),
			'frontend' => Array(
				'src' => '../../dist/js/all.js',
			),
		);

		wp_enqueue_script( 'jquery-effects-core' );
		if( !empty( $scripts ) ) {
			foreach( $scripts as $script => $scriptMeta ) {

				$deps = Array( 'jquery' );
				if( isset( $scriptMeta[ 'deps' ] ) ) {
					$deps = wp_parse_args( $deps, $scriptMeta[ 'deps' ] );
				}

				$ver = jvbpdCore()->getVersion();
				if( isset( $scriptMeta[ 'ver' ] ) ) {
					$ver = $scriptMeta[ 'ver' ];
				}

				wp_register_script(
					( isset( $scriptMeta[ 'handle' ] ) ? $scriptMeta[ 'handle' ] : jvbpdCore()->var_instance->getHandleName( $script ) ),
					jvbpdCore()->assets_url . 'js/' . $scriptMeta[ 'src' ],
					$deps, $ver, true
				);
				if( isset( $scriptMeta[ 'enqueue' ] ) && $scriptMeta[ 'enqueue' ] ) {
					wp_enqueue_script( ( isset( $scriptMeta[ 'handle' ] ) ? $scriptMeta[ 'handle' ] : jvbpdCore()->var_instance->getHandleName( $script ) ) );
				}
			}
		}
	}

	public function register_styles() {
		global $wp_scripts;
		$styles = Array(
			/*
			'nouislider.min.css' => Array(
				'enqueue' => true,
				'src' => 'nouislider.min.css',
			), */
		);

		wp_enqueue_script( 'jquery-effects-core' );

		wp_enqueue_style(
			"jquery-ui-css",
			"//ajax.googleapis.com/ajax/libs/jqueryui/{$wp_scripts->registered['jquery-ui-core']->ver}/themes/ui-lightness/jquery-ui.min.css"
		);

		if( !empty( $styles ) ) {
			foreach( $styles as $style => $styleMeta ) {
				$deps = Array();
				if( isset( $styleMeta[ 'deps' ] ) ) {
					$deps = wp_parse_args( $deps, $styleMeta[ 'deps' ] );
				}

				$ver = jvbpdCore()->getVersion();
				if( isset( $styleMeta[ 'ver' ] ) ) {
					$ver = $styleMeta[ 'ver' ];
				}

				wp_register_style(
					( isset( $styleMeta[ 'handle' ] ) ? $styleMeta[ 'handle' ] : jvbpdCore()->var_instance->getHandleName( $style ) ),
					jvbpdCore()->assets_url . 'css/' . $styleMeta[ 'src' ],
					$deps, $ver, 'all'
				);
				if( isset( $styleMeta[ 'enqueue' ] ) && $styleMeta[ 'enqueue' ] ) {
					wp_enqueue_style( ( isset( $styleMeta[ 'handle' ] ) ? $styleMeta[ 'handle' ] : jvbpdCore()->var_instance->getHandleName( $style ) ) );
				}
			}
		}

	}

	public function enqueue_frontend_script() {
		wp_enqueue_script( jvbpdCore()->var_instance->getHandleName( 'frontend' ) );
	}

	public function get_lava_jsonfile() {
		if( !function_exists( 'lava_directory' ) )
			return;

		if( method_exists( lava_directory()->core, 'getJsonFileName' ) )
			$this->json_file = lava_directory()->core->getJsonFileName();

		if( method_exists( lava_directory()->core, 'is_crossdomain' ) ) {
			$this->is_cross_domain = lava_directory()->core->is_crossdomain();
			$this->strGetJsonHook = sprintf( 'lava_%s_get_json', self::CORE_POST_TYPE );
		}
	}

	public function initMapTemplate() {
		return parent::$instance->template_path . '/template-map.php';
	}

	public function loadMapTemplate() {
		// add_action( 'jvbpd_' . parent::CORE_POST_TYPE . '_map_body', Array( $this, 'load_map_type_switcher' ) );
		add_action( 'jvbpd_' . parent::CORE_POST_TYPE . '_map_body', Array( $this, 'load_map_inline_filter' ) );
		add_action( 'jvbpd_' . parent::CORE_POST_TYPE . '_map_body', Array( $this, 'custom_load_map' ) );
		add_action( 'lava_' . parent::CORE_POST_TYPE . '_map_container_after', Array( $this, 'custom_map_scripts' ) );
		add_action( 'lava_' . parent::CORE_POST_TYPE . '_map_container_after', Array( $this, 'radius_parameter' ) );
	}



	public function setVariables() {
		$this->map_slug = 'multipleBox';
		$this->slug = self::CORE_POST_TYPE;
		self::$instance = &$this;
		$this->core_prefix = sprintf( self::CORE_PREFIX_FORMAT, parent::CORE_POST_TYPE );
	}

	public function registerHooks() {
		// Get Json File Name
		add_action( 'init', Array( $this, 'active_hooks' ), 11 );
		add_action( 'wp_head', Array( $this, 'active_hooks' ) );
		add_action( 'wp_enqueue_scripts', Array( $this, 'single_hooks' ) );
		add_action( 'save_post', Array( $this, 'setSingleType' ), 10, 2 );

		// Single
		add_filter( 'template_include', Array( $this, 'single_template' ), 15 );
		add_filter( 'wp_nav_menu_items', Array( $this, 'addHeaderSearchMenu' ), 10, 2 );

		// Login
		add_action( 'jvbpd_login2_modal_login_after', array( $this, 'addSocialLoginButton' ) );

		add_action( 'wp_footer', array( $this, 'loadBriefModal' ) );
		add_filter( 'jvbpd_' . parent::CORE_POST_TYPE . '_map_params', array( $this, 'archive_params' ) );
		add_filter( 'jvbpd_elementor_params', array( $this, 'more_taxonomies_params' ) );

		// Content
		add_filter('Javo/Content/Sidebar_Active', Array($this, 'sidebar_deactivate'));

	}

	public function setCoreSingleType() {
		$arrTypes = Array(
			'type-a',
			'type-b',
			'type-half',
			'type-grid',
			'type-left-tab',
			'type-top-tab'
		);

		$strType = jvbpd_tso()->get( 'lv_listing_single_type', 'type-grid' );
		$dynamic_options = array();
		$dynamic_options = apply_filters( 'jvbpd_' . parent::CORE_POST_TYPE . '_single_type', $strType, jvbpd_tso() );
		if( isset( $dynamic_options['newType'] ) && in_array( $dynamic_options['newType'], $arrTypes ) ) {
			$this->single_type = $dynamic_options['newType'];
		}
		$this->singlePreviewAction( parent::CORE_POST_TYPE, 'single_type' );
	}

	public function singlePreviewAction( $post_type='', $variable_name='' ) {

		$is_allowed = false;
		$arrAllowVariables = Array( 'single_type', 'resume_type' );
		$nonce_field = 'single_' . $post_type . '_action';
		$strCacheKey = sprintf( self::PREVIEW_KEY_FORMAT, $post_type, get_the_ID() );
		$strTypeName = get_transient( $strCacheKey );

		if( isset( $_POST[ $nonce_field ] ) && wp_verify_nonce( $_POST[ $nonce_field ], 'jvbpd_single_type_preview' ) ){
			$is_allowed = true;
		}

		if( 'pending' == get_post_status() && in_array( get_post_type(), array( parent::CORE_POST_TYPE ) ) ) {
			$is_allowed = true;
		}

		if( ! $is_allowed ) {
			return false;
		}

		if( isset( $_POST[ 'jvbpd_preview_single_type' ] ) && $_POST[ 'jvbpd_preview_single_type' ] != '' ) {
			$strTypeName = $_POST[ 'jvbpd_preview_single_type' ];
			set_transient( $strCacheKey, $strTypeName );
		}

		if( false === $strTypeName ) {
			return false;
		}

		if( in_array( $variable_name, $arrAllowVariables ) ) {
			$this->$variable_name = $strTypeName;
		}
	}

	public function active_hooks() {
		/* Common */

			// Mobile Menu
			add_filter( 'jvbpd_core_post_type', Array( $this, 'getSlug' ) );
			//add_action( 'jvbpd_header_container_after', array( $this, 'header_map_filter' ) );
			add_action( 'jvbpd_top_nav_menu_center', array( $this, 'header_map_filter' ) );

		/* Single */ {

			add_filter( 'jvbpd_post_title_header', Array( $this, 'hidden_sintle_title' ), 10, 2 );
			add_filter( 'jvbpd_single_post_types_array', Array( $this, 'custom_single_transparent' ) );

			// Custom CSS
			add_filter( 'jvbpd_custom_css_rows', Array( $this, 'custom_single_template_css_row' ), 20 );

			// Navigation
			add_filter( 'jvbpd_detail_item_nav', Array( $this, 'custom_single_header_nav' ) );

			// Footer
			add_action( 'lava_' . parent::CORE_POST_TYPE . '_single_container_after', Array( $this, 'custom_single_dot_nav' ) );
		}

		/* Single Support */ {

			// Header
			/*
			add_filter( 'body_class' , Array( $this, 'custom_single_body_class' ) );
			add_filter( 'jvbpd_post_title_header', Array( $this, 'hidden_sintle_title' ), 10, 2 );
			add_action( 'lava_' . parent::SUPPORT_POST_TYPE . '_single_container_before', Array( $this, 'custom_single_support_header' ) );

			add_action( 'jvbpd_' . parent::SUPPORT_POST_TYPE . '_single_body', Array( $this, 'custom_single_support_body' ) );
			add_action( 'lava_' . parent::SUPPORT_POST_TYPE . '_manager_single_enqueues', Array( $this, 'custom_single_enqueues' ) );

			// Navigation
			add_filter( 'jvbpd_detail_support_nav', Array( $this, 'custom_single_support_header_nav' ) );

			// Footer
			add_action( 'lava_' . parent::SUPPORT_POST_TYPE . '_single_container_after', Array( $this, 'custom_single_dot_nav' ) );
			*/
		}

		/* Map */ {

			// Map Data
			add_action( 'lava_' . parent::CORE_POST_TYPE . '_setup_mapdata', Array( $this, 'custom_map_parameter' ), 10, 3 );

			add_action( 'jvbpd_core/ajax/brief/get_html', Array( $this, 'map_brief_contents' ), 10, 3 );

			// Map Template Hooks
			add_action( 'lava_' . parent::CORE_POST_TYPE . '_map_wp_head', Array( $this, 'custom_map_hooks' ) );

			// Custom CSS
			add_filter( 'jvbpd_custom_css_rows', Array( $this, 'custom_map_template_css_row' ), 30 );

			// Header
			add_action( 'lava_' . parent::CORE_POST_TYPE . '_map_box_enqueue_scripts', Array( $this, 'custom_map_enqueues' ), 99 );
			add_action( 'lava_' . parent::CORE_POST_TYPE . '_map_container_before', Array( $this, 'custom_before_setup' ) );

			// Brief Window
			add_filter( 'jvbpd_brief_info_summary_items', array( $this, 'brief_summary_item' ), 10, 2 );



			// Body Class
			add_filter( 'lava_' . parent::CORE_POST_TYPE . '_map_classes', Array( $this, 'custom_map_classes' ) );

			// Load Templates
			add_filter( 'lava_' . parent::CORE_POST_TYPE . '_map_htmls' , Array( $this, 'custom_map_htmls' ), 10, 2 );

			add_filter( 'jvbpd_template_map_module_options', Array( $this, 'map_no_lazyload' ) );
			add_filter( 'jvbpd_template_list_module_options', Array( $this, 'map_no_lazyload' ) );

			add_action( 'jvbpd_map_output_class', Array( $this, 'mapOutput_class' ) );
			add_action( 'jvbpd_map_list_output_class', Array( $this, 'listOutput_class' ) );
		}

		/* Archive */
		{
			/*
			add_filter( 'jvbpd_template_list_module', Array( $this, 'archive_map_list_module' ), 10, 2 );
			add_filter( 'jvbpd_template_list_module_loop', Array( $this, 'archive_map_list_module_loop' ), 10, 3 );
			add_filter( 'jvbpd_template_map_module', Array( $this, 'archive_map_module' ), 10, 2 );
			add_filter( 'jvbpd_template_map_module_loop', Array( $this, 'archive_map_module_loop' ), 10, 3 ); */

			add_filter( 'lava_' . parent::CORE_POST_TYPE . '_get_template' , Array( $this, 'custom_archive_page' ), 10, 3 );
			add_filter( 'jvbpd_map_class', Array( $this, 'custom_map_class' ), 30, 2 );
			add_filter( 'jvbpd_' . parent::CORE_POST_TYPE . '_map_list_content_column_class', Array( $this, 'custom_list_content_column' ), 10, 3 );
			add_action( 'jvbpd_' . parent::CORE_POST_TYPE . '_map_list_container_before', Array( $this, 'map_list_container_before' ), 15 );
			add_action( 'jvbpd_' . parent::CORE_POST_TYPE . '_map_list_container_after', Array( $this, '_map_list_container_after' ) );
		}

		/* Widget */{
			add_filter( 'jvbpd_recent_posts_widget_excerpt', Array( $this, 'core_recentPostsWidget' ), 10, 4 );
			add_filter( 'jvbpd_recent_posts_widget_describe_type_options', Array( $this, 'core_recentPostsWidgetOption' ), 10, 1 );
		}
	}

	public function load_map_type_switcher(){
		$this->load_template( 'part-map-type-switcher' );
	}

	public function load_map_inline_filter(){
		$this->load_template( 'part-map-inline-filter' );
	}

	public function custom_load_map() {
		$strFileName = parent::$instance->template_path .'/template-map-container.php';
		if( ! file_exists( $strFileName ) ) {
			esc_html_e( "Not found template type", 'jvfrmtd' );
			return;
		}
		require_once( $strFileName );
	}

	public function custom_map_scripts() {
		$strFileName		= Array();
		$strFileName[]		= parent::$instance->template_path;
		$strFileName[]		= 'scripts-map-multipleBox.php';
		$strFileName		= @implode( '/', $strFileName );

		if( !file_exists( $strFileName ) ){
			wp_die( new WP_Error( 'not-found-map-script', __( "Not found script : ", 'jvfrmtd' ) . $strFileName ) );
		}

		require_once( $strFileName );
	}

	public function radius_parameter() {
		$radius = isset($_GET['radius']) ? $_GET['radius'] : 0;
		printf('<input type="hidden" name="radius_param" value="%s">', $radius);
	}

	public function single_hooks() {
		$this->setCoreSingleType();

		add_filter( 'body_class' , Array( $this, 'custom_single_body_class' ) );
		// add_action( 'lava_' . parent::CORE_POST_TYPE . '_single_container_before', Array( $this, 'custom_single_header' ) );
		add_action( 'jvbpd_' . parent::CORE_POST_TYPE . '_single_body', Array( $this, 'custom_single_body' ) );
		add_action( 'lava_' . parent::CORE_POST_TYPE . '_manager_single_enqueues', Array( $this, 'custom_single_enqueues' ) );
		if( is_single() ) {
			add_action('wp_head',  Array( $this, 'jv_fb_thumbnail'));
		}

		add_filter( 'jvbpd_' . parent::CORE_POST_TYPE . '_single_tab_menus', Array( $this, 'single_tab_menus' ) );
		add_action( 'wp_footer', array( $this, 'single_control_buttons' ) );
	}

	public function jv_fb_thumbnail(){
		global $post, $jvbpd_tso;
		if(get_the_post_thumbnail($post->ID, 'thumbnail')) {
			$image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
		} else {
			$image = $jvbpd_tso->get('no_image', JVBPD_IMG_DIR.'/no-image.png');
		}
		echo '<meta property="og:image" content="'.$image.'"/>';
	}

	public function load_template( $template_name, $extension='.php' , $params=Array(), $_once=true ) {

		if( !empty( $params ) ) {
			extract( $params );
		}

		$strFileName = jvbpdCore()->template_path . '/' . $template_name . $extension;
		$strFileName = apply_filters( 'jvbpd_core_load_template', $strFileName, $template_name );

		if( file_exists( $strFileName ) ) {
			if( $_once ) {
				require_once $strFileName;
			}else{
				require $strFileName;
			}
			return true;
		}
		return false;
	}

	public function custom_single_body_class( $body_classes=Array() ) {

		$arrSinglePostType = Array( parent::CORE_POST_TYPE, parent::SUPPORT_POST_TYPE );
		$body_classes[] = $this->single_type;
		$body_classes[] = 'extend-meta-block';

		if( in_array( get_post_type(), $arrSinglePostType ) )
			$body_classes[]	= 'no-sticky';

		return $body_classes;
	}

	public function header_map_filter() {
		$this->load_template( 'part-header-map-filter' );
	}

	public function hidden_sintle_title( $post_title='', $post=null ) {
		if( is_null( $post ) || get_post_type( $post ) === parent::CORE_POST_TYPE )
			$post_title = null;

		return $post_title;
	}

	public function custom_single_transparent( $post_types=Array() ){
		$post_types[] = parent::CORE_POST_TYPE;
		return $post_types;
	}

	public function custom_single_enqueues(){
		wp_enqueue_script( 'jquery-sticky' );
		wp_enqueue_script( 'zeroclipboard' );
		wp_enqueue_script( 'swiper' );
		add_action( 'wp_footer', array( $this, 'single_footer_enqueue' ) );
	}

	public function single_footer_enqueue() {
		//wp_enqueue_script( jvbpdCore()->var_instance->getHandleName('lightgallery-all') );
		wp_localize_script(
			jvbpdCore()->var_instance->getHandleName( 'jvbpd-listing-single' ),
			'jvbpd_custom_post_param',
			apply_filters( 'jvbpd_core/single/listing/params', Array(
				'widget_sticky' => jvbpd_tso()->get( jvbpdCore()->var_instance->slug . '_single_sticky_widget' ),
				'map_type' => jvbpd_tso()->get( jvbpdCore()->var_instance->slug . '_map_width_type' ),
				'single_type' => $this->single_type,
				'map_style' => stripslashes( htmlspecialchars_decode( jvbpd_tso()->get( 'map_style_json' ) ) ),
			), get_post() )
		);
		wp_enqueue_script( jvbpdCore()->var_instance->getHandleName( 'jvbpd-listing-single' ) );
	}

	public function custom_single_header() {
		$this->load_template( 'part-single-header-' . $this->single_type );
		$this->load_template( 'addon-header-spyscroll-nav' );
	}

	public function custom_single_body() {
		$this->load_template( 'template-single-' . $this->single_type );
	}

	public function custom_single_support_header() {
		$this->load_template( 'part-single-header-' . $this->support_type );
	}

	public function custom_single_support_body() {
		$this->load_template( 'template-single-' . $this->support_type );
	}

	public function single_tab_menus( $menus=Array() ) {

		if( function_exists( 'lava_directory_booking' ) && get_post_meta( get_the_ID(), '_booking', true ) ) {
			$menus[ 'booking' ] = Array(
				'label' => esc_html__( "Booking", 'jvfrmtd' ),
				'icon' => "jvd-icon-calendar"
			);
		}
		$menus[ 'others' ] = Array(
			'label' => esc_html__( "Others", 'jvfrmtd' ),
			'icon' => "jvd-icon-website_1"
		);
		return $menus;
	}

	public function setSingleType( $post_id=0, $post ) {

		if( ! in_array( $post->post_type, Array( parent::CORE_POST_TYPE ) ) || $post->post_status != 'publish' ) {
			return false;
		}

		if( ! function_exists( 'lync_single' ) ) {
			return false;
		}

		$strKeyName = jvbpd_single()->admin->fieldName;
		$strCacheKey = sprintf( self::PREVIEW_KEY_FORMAT, $post->post_type, $post_id );
		$strTypeName = get_transient( $strCacheKey );

		if( 0 < intVal( $post_id ) && false !== $strTypeName ) {
			$arrOldValues = get_post_meta( $post_id, $strKeyName, true );
			if( is_array( $arrOldValues ) ) {
				$arrOldValues[ 'singleType' ] = $strTypeName;
			}else{
				$arrOldValues = Array( 'singleType' => $strTypeName );
			}
			update_post_meta( $post_id, $strKeyName, $arrOldValues );
			delete_transient( $strCacheKey );
		}
	}

	public function single_template( $template ) {
		$post = get_queried_object();

		if( $post instanceof WP_Post ) {
			if( $post->post_type == 'lv_listing' ) {
				$fileName = jvbpdCore()->template_path . '/single-lv_listing.php';
				if( file_exists( $fileName ) ) {
					$template = $fileName;
				}
			}
		}
		return $template;
	}

	public function custom_single_template_css_row( $rows=Array() ){
		$strPrefix = 'html body.single.single-' . jvbpdCore()->var_instance->slug . ' ';

		$rows[] = $strPrefix . 'header#header-one-line nav.javo-main-navbar';
		$rows[] = '{ top:auto; position:relative; left:auto; right:auto; }';

		return $rows;
	}

	public function custom_single_support_template_css_row( $rows=Array() ){
		$strPrefix = 'html body.single.single-' . parent::SUPPORT_POST_TYPE . ' ';

		$rows[] = $strPrefix . 'header#header-one-line nav.javo-main-navbar';
		$rows[] = '{ top:auto; position:relative; left:auto; right:auto; }';

		return $rows;
	}

	public function custom_single_header_nav( $args=Array() ) {

		$arrAllowPostTypes = apply_filters( 'jvbpd_single_post_types_array', Array( 'lv_listing' ) );
		$append_args = Array();
		/*$append_args[ 'javo-item-condition-section'	 ]	= Array(
			'label' => esc_html__( "Detail", 'jvfrmtd' )
			, 'class' => 'glyphicon glyphicon-tasks'
			, 'type' => $arrAllowPostTypes
		); */
		$append_args[ 'javo-item-describe-section' ]	= Array(
			'label' => esc_html__( "Description", 'jvfrmtd' )
			, 'class' => 'glyphicon glyphicon-align-left'
			, 'type' => $arrAllowPostTypes
		);
		$append_args[ 'javo-item-amenities-section'	 ]	= Array(
			'label' => esc_html__( "Amenities", 'jvfrmtd' )
			, 'class' => 'glyphicon glyphicon-ok-circle'
			, 'type' => $arrAllowPostTypes
		);
		if( class_exists( 'Lava_Directory_Review' ) ) :
			$append_args[ 'javo-item-review-section' ]	= Array(
				'label' => esc_html__( "Review", 'jvfrmtd' )
				, 'class' => 'glyphicon glyphicon-comment'
				, 'type' => $arrAllowPostTypes
			);
		endif;

		return wp_parse_args( $append_args, $args );
	}

	public function custom_single_support_header_nav( $args=Array() ) {

		$arrAllowPostTypes = Array( parent::SUPPORT_POST_TYPE );
		$append_args = Array();
		$append_args[ 'jvbpd-support-last-answer'	 ]	= Array(
			'label' => esc_html__( "Last Answer", 'jvfrmtd' )
			, 'class' => 'glyphicon glyphicon-tasks'
			, 'type' => $arrAllowPostTypes
		);
		$append_args[ 'jvbpd-support-update-ticket' ] = Array(
				'label' => esc_html__( "Update Ticket", 'jvfrmtd' )
				, 'class' => 'glyphicon glyphicon-comment'
				, 'type' => $arrAllowPostTypes
		);
		$append_args[ 'jvbpd-support-policy' ]	= Array(
			'label' => esc_html__( "Support Policy", 'jvfrmtd' )
			, 'class' => 'glyphicon glyphicon-ok-circle'
			, 'type' => $arrAllowPostTypes
		);
		$append_args[ 'jvbpd-support-hour' ] = Array(
			'label' => esc_html__( "Support Hours", 'jvfrmtd' )
			, 'class' => 'glyphicon glyphicon-align-left'
			, 'type' => $arrAllowPostTypes
		);
		$append_args[ 'jvbpd-support-watch' ] = Array(
			'label' => esc_html__( "Save This Ticket", 'jvfrmtd' )
			, 'class' => 'glyphicon glyphicon-map-marker'
			, 'type' => $arrAllowPostTypes
		);

		return wp_parse_args( $append_args, $args );
	}

	public function custom_single_dot_nav() {
		$this->load_template( 'part-single-dot-nav' );
	}

	public function single_control_buttons() {
		$post = get_post();
		if( ! in_array( get_post_type( $post ), Array( parent::CORE_POST_TYPE ) ) || get_post_status( $post ) != 'pending' ) {
			return false;
		}
		$this->load_template( 'part-single-layer-preview', '.php', array( 'post' => $post ) );
	}

	public function woo_func() {
		add_action( 'jvbpd_sidebar_id', Array( $this, 'woo_sidebar' ), 10, 2 );
		add_filter( 'jvbpd_sidebar_position', array( $this, 'woo_sidebar_position' ), 15, 2 );
	}

	public function createCustomThemeTemplate() {
		$this->templates = Array(
			'template-add-core-form' => esc_html__( 'Submit listing form template', 'jvfrmtd' ),
		);
		if( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
			add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'regieter_core_template' ) );
		}else{
			add_filter( 'theme_page_templates', array( $this, 'reigster_core_template_old' ) );
		}
		add_filter( 'wp_insert_post_data',  array( $this, 'regieter_core_template' ) );
		add_filter( 'template_include',  array( $this, 'load_core_template' ) );
		add_action( 'jvbpd_core/template/submit/form_after', Array($this, 'add_package_field'));
	}

	public function reigster_core_template_old( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	public function regieter_core_template( $atts ) {
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}

		wp_cache_delete( $cache_key , 'themes');
		$templates = array_merge( $templates, $this->templates );
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );
		return $atts;
	}

	public function load_core_template( $template ) {
		global $page, $pages;
		$post = get_queried_object();

		// Count, offset -1 error fix
		if($post instanceof \WP_Post) {
			$page = is_numeric($page) ? max(1, $page) : 1;
			$pages = $pages ? $pages : Array($post->post_content);
		}
		$post = get_queried_object();

		if( ! $post instanceof WP_Post ) {
			return $template;
		}

		$templateName = get_post_meta( $post->ID, '_wp_page_template', true );

		if ( !isset( $this->templates[ $templateName ] ) ) {
			return $template;
		}

		if( $templateName == 'template-add-core-form' ) {
			$file = jvbpdCore()->template_path . '/template-add-core-form.php';
		}

		if(function_exists('lv_directory_payment')) {
			$payment = lv_directory_payment();
			if( $payment->admin->is_active() ) {
				if( !get_query_var( 'package' ) && 'yes' != get_query_var('update') ) {
					add_filter('jvbpd_core/template/submit/display_content', '__return_false');
					add_action('jvbpd_core/template/submit/custom_content', Array($this, 'get_package_selector'));
				}
			}
		}

		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}
		return $template;
	}

	public function get_package_selector() {
		$payment = lv_directory_payment();
		echo '<div class="p-10">';
		require_once($payment->template_path . '/template-package-selector.php');
		echo '</div>';
	}

	public function add_package_field() {
		if(!function_exists('lv_directory_payment')) {
			return;
		}

		$payment = lv_directory_payment();

		if(!$payment->admin->is_active()) {
			return;
		}

		printf( '<input type="hidden" name="%s" value="%s">', 'package', get_query_var( 'package' ) );
	}

	public function custom_map_parameter( $obj, $get=false, $post=false ) {
		$_instance = get_queried_object();
		$term_id = 0;

		if( $_instance instanceof WP_Term ) {
			$term_id = intVal( $_instance->term_id );
		}

		if( !$obj instanceof WP_Post ) {
			return;
		}

		$obj->requests = Array();

		if( ! method_exists( $get, 'get' ) || ! method_exists( $post, 'get' ) ) {
			return;
		}

		$taxonomies = apply_filters( 'jvbpd_map_requests', array( 'category', 'geolocation' ), $obj );
		if( is_array( $taxonomies ) ) {
			foreach( $taxonomies as $taxonomy ) {
				$obj->requests[ $taxonomy ] = $get->get( $taxonomy, $post->get( $taxonomy, $term_id ) );
			}
		}
		add_filter( 'body_class', array( $this, 'sidebar_disable_class' ), 999 );
		add_filter( 'jvbpd_post_title_header', '__return_false' );
		add_filter( 'jvbpd_left_sidebar_display', '__return_false' );
		add_filter( 'jvbpd_member_sidebar_display', '__return_false' );
		add_action( 'wp_footer', array( $this, 'map_template_append_right_sidebar' ) );
	}

	public function sidebar_disable_class( $classes=array() ) {
		$classes[] = 'sidebar-disabled';
		return array_diff( $classes, array( 'member-sidebar-active' ) );
	}

	public function map_template_append_right_sidebar() {
		get_template_part('library/header/right', 'sidebar');
	}

	public function map_brief_contents( $html=Array(), $post ) {
		/*
		ob_start();
		$this->load_template( 'template-brief-contents', '.php', array( 'jvbpd_post' => $post ) );
		$html = Array( ob_get_clean() ); */
		//return $html;
		$template_id = Jvbpd_Listing_Elementor::get_settings( 'custom_preview', '' );
		$template = Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id );
		$instance = new Jvbpd_Replace_Content( $post->ID, $template );
		return Array( $instance->render() );
	}

	public function custom_map_hooks() {

		$options = (Array) get_post_meta( get_the_ID(), 'jvbpd_map_page_opt', true );
		$strOptionName = 'mobile_type';

		if(
			is_array( $options ) &&
			!empty( $options[ $strOptionName ] )  &&
			$options[ $strOptionName ] == 'ajax-top'
		){
			add_action( 'jvbpd_header_brand_right_after', Array( $this, 'addition_inner_switcher' ), 15 );
		}

	}

	public function custom_map_template_css_row( $rows ){
		$strPrefix		= 'html body.page-template.page-template-lava_' . jvbpdCore()->var_instance->slug . '_map' . ' ';
		$strPrimary		= jvbpd_tso()->get( 'total_button_color', false );
		$strPrimary_text = jvbpd_tso()->get( 'primary_font_color', false );
		$strPrimary_border = 'none';
		if(jvbpd_tso()->get('total_button_border_use', false) == 'use' && jvbpd_tso()->get( 'total_button_border_color')!='' ){
			$strPrimary_border = '1px solid '.jvbpd_tso()->get( 'total_button_border_color', false );
		}
		$strPrimaryRGB	= apply_filters( 'jvbpd_rgb', substr( $strPrimary, 1) );

		$rows[] = "body{ background-color:#f00; }";

		if( $strPrimary ){
			$rows[] = $strPrefix . ".javo-shortcode .module .meta-category:not(.no-background),";
			$rows[] = $strPrefix . ".javo-shortcode .module .media-left .meta-category:not(.no-background)";
			// $rows[] = ".jvbpd-header-map-filter-wrap";
			/** ----------------------------  */
			$rows[] = "{ background-color:{$strPrimary}; color:{$strPrimary_text}; border:{$strPrimary_border}; }";

			$rows[] = $strPrefix . ".javo-shortcode .module.javo-module12 .thumb-wrap:hover .javo-thb:after";
			/** ----------------------------  */
			$rows[] = "{ background-color:rgba({$strPrimaryRGB['r']}, {$strPrimaryRGB['g']}, {$strPrimaryRGB['b']}, .92); }";
		}

		return apply_filters( 'jvbpd_' . parent::CORE_POST_TYPE . '_custom_map_css_rows', $rows, $strPrefix );
	}

	public function custom_map_enqueues() {
		global $post;

		$is_empty_post = false;

		wp_enqueue_script( jvbpdCore()->var_instance->getHandleName( 'noUISlider' ) );
		wp_enqueue_script( 'selectize' );
		wp_enqueue_script( 'jquery-sticky' );
		wp_enqueue_script( 'jQuery-chosen-autocomplete' );

		if( !is_object( $post ) ){
			$is_empty_post = true;
			$post = new stdClass();
			$post->lava_type = $this->slug;
			$post->ID = 0;
		}

		$objOptions = (Array)get_post_meta( $post->ID, 'jvbpd_map_page_opt', true );


		wp_localize_script(
			// jvbpdCore()->var_instance->getHandleName( 'map-template' ),
			jvbpdCore()->var_instance->getHandleName( 'frontend' ),
			'jvbpd_core_map_param',
			apply_filters(
				'jvbpd_' . parent::CORE_POST_TYPE . '_map_params',
				Array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'cross_domain' => $this->is_cross_domain,
					'json_hook' => $this->strGetJsonHook,
					'json_file' => $this->json_file,
					'json_security' => wp_create_nonce( $this->strGetJsonHook ),
					'template_id' => $post->ID,
					'selctize_terms' => Array( 'listing_category', 'listing_location' ),
					'strLocationAccessFail' => esc_html__( "Your position access failed.", 'jvfrmtd' ),
					// 'amenities_filter' => $objOptions->get( 'amenities_filter' ),
					'amenities_filter' => isset( $objOptions['amenities_filter'] ) ? $objOptions['amenities_filter'] : '',
					'allow_wheel' => jvbpd_tso()->get( 'map_allow_mousewheel', 'a' ),
					// 'map_marker' => $objOptions->get( 'map_marker', jvbpd_tso()->get( 'map_marker' ) ),
					'map_marker' => isset( $objOptions['map_marker'] ) ? $objOptions['map_marker'] : '',
					'strings' => Array(
						'multiple_cluster' => esc_html__( "This place contains multiple places. please select one.", 'jvfrmtd' )
					),
					'map_init_position_lat' => function_exists('jvbpd_elements_tools')? jvbpd_elements_tools()->getPageSetting( 'map_init_position_lat' ) : false,
					'map_init_position_lng' => function_exists('jvbpd_elements_tools')? jvbpd_elements_tools()->getPageSetting( 'map_init_position_lng' ) : false,
					'distance_unit' => function_exists('jvbpd_elements_tools')? jvbpd_elements_tools()->getPageSetting( 'map_distance_unit' ) : false,
				),
				jvbpd_tso(), $objOptions
			)
		);

		if( $is_empty_post )
			$post=  null;

		// wp_enqueue_script( jvbpdCore()->var_instance->getHandleName( 'map-template' ) );
	}

	public function custom_before_setup( $post ) {
		$is_core_map_actived = class_exists( 'Jvbpd_Core_Map' );
		if( get_post_meta( get_post()->ID, '_map_filter_position', true ) == 'map-layout-search-top' && $is_core_map_actived )
			add_action( 'jvbpd_' . parent::CORE_POST_TYPE . '_map_switcher_before', Array( $this, 'custom_map_listing_filter' ) );
		else
			add_action( 'jvbpd_' . parent::CORE_POST_TYPE . '_map_lists_before', Array( $this, 'custom_map_listing_filter' ) );
	}

	public function brief_summary_item( $items=array(), $post_id=0 ) {

		if( ! function_exists( 'javo_moreTax' ) ) {
			return;
		}
		$arrTaxonomies = javo_moreTax()->admin->getMoreTaxonomies();
		$arrTaxonomiesNames = Array();

		if( !empty( $arrTaxonomies ) && is_array( $arrTaxonomies ) ) {
			foreach( $arrTaxonomies as $arrTax ) {
				if( !empty( $arrTax[ 'name' ] ) ) {
					$arrTaxonomiesNames[] = $arrTax[ 'name' ];
				}
			}
		}

		foreach( $arrTaxonomiesNames as $taxName ) {
			$arrTerms = wp_get_object_terms( $post_id, $taxName, array( 'fields' => 'names' ) );
			$items[] = Array(
				'label' => get_taxonomy( $taxName )->label,
				'value' => join( ', ', $arrTerms )
			);
		}

		return $items;
	}

	public function addition_inner_switcher(){
		$this->load_template( 'part-map-filter-inner-switcher', '.php', Array( 'post' => $GLOBALS[ 'post' ] ), false );
	}

	public function custom_map_classes( $classes=Array() ){

		$classes[] = 'no-sticky';
		$classes[] = 'no-smoth-scroll';
		// $classes[] = 'mobile-ajax-top';
		$classes[] = 'mobile-active';

		if( function_exists( 'jvbpd_elements_tools' ) ) {
			$classes[] = sprintf( 'map-type-%s', jvbpd_elements_tools()->getMapType() );
		}

		$options = (Array) get_post_meta( get_the_ID(), 'jvbpd_map_page_opt', true );
		$strOptionName = 'mobile_type';

		if( is_array( $options ) && !empty( $options[ $strOptionName ] ) ) {
			$classes[] = 'mobile-' . $options[ $strOptionName ];
		}

		return $classes;
	}

	public function listing_mobile_filter( $post ) {
		$this->load_template( 'html-map-mobile-listing-menu', '.php', Array( 'post' => $post )  );
	}

	public function custom_map_htmls( $args, $plug_dir ) {
		$tmpDir = parent::$instance->template_path . '/';
		return Array(
			'javo-map-loading-template' => $tmpDir . 'html-map-loading.php',
			'javo-map-box-panel-content' => $tmpDir . 'html-map-grid-template.php',
			'javo-map-box-infobx-content' => $tmpDir . 'html-map-popup-contents.php',
			'javo-list-box-content' => $tmpDir . 'html-list-box-contents.php',
			'javo-map-inner-control-template' => $tmpDir . 'html-map-inner-controls.php',
			'jvbpd-map-inner-panel' => $tmpDir . 'html-map-inner-panel.php',
			'jvbpd-map-distance-bar' => $tmpDir . 'html-map-distance-bar.php',
		);
	}

	public function map_no_lazyload( $args=Array() ){ return wp_parse_args( Array( 'no_lazy' => true ), $args ); }

	public function mapOutput_class( $classes=Array() ){

		$classes[] = 'module-hover-zoom-in';
		return $classes;

	}

	public function listOutput_class( $classes=Array() ){

		$classes[] = 'module-hover-zoom-in';
		return $classes;
	}

	public function custom_archive_page( $template, $query=false, $obj=false ) {

		$term = get_queried_object();

		if( ! $term instanceof WP_Term ) {
			return $template;
		}

		$taxonomy = $term->taxonomy;

		if( ! in_array( $taxonomy, Array( 'listing_keyword' ) ) ) {
			if( in_array( parent::CORE_POST_TYPE, get_taxonomy( $taxonomy )->object_type ) ) {
				$this->is_archive = true;
				$obj->get_map_template();
				$template = $this->initMapTemplate();
			}
		}
		return $template;
	}

	public function custom_map_class( $classes ) {
		$classes[] = 'mobile-active';
		if( $this->is_archive && false ) {
			$classes = wp_parse_args( Array( 'hide-listing-filter' ), $classes );
		}
		return $classes;
	}

	public function custom_list_content_column( $class_name ) {
		if( $this->is_archive ) {
			$class_name	= 'col-sm-12';
		}
		return $class_name;
	}

	public function archive_map_list_module( $module_name, $post_id ) {
		if( ! $post_id ) {
			$module_name = 'module12';
		}
		return $module_name;
	}

	public function archive_map_list_module_loop( $template, $class_name, $post_id ) {
		if( ! $post_id )
			$template = "<div class=\"col-md-4\">%s</div>";
		return$template;
	}

	public function archive_map_module( $module_name, $post_id ) {
		if( ! $post_id )
			$module_name		= 'module12';
		return $module_name;
	}

	public function archive_map_module_loop( $template, $class_name, $post_id ) {
		if( ! $post_id )
			$template = "<div class=\"col-md-6\">%s</div>";
		return$template;
	}

	public function map_list_container_before( $post ) {
		if( $this->is_archive ){
			$this->load_template( 'part-archive-container-header' );
		}
	}

	public function _map_list_container_after( $post ) {
		if( $this->is_archive ){
			$this->load_template( 'part-archive-container-footer' );
		}
	}

	public function custom_map_listing_filter( ) {
		global $post;

		$strFileName = parent::$instance->template_path . '/html-map-mainFilter.php';
		if( !file_exists( $strFileName ) ){
			wp_die( new WP_Error( 'not-found-map-filter', __( "Not found filter : ", 'jvfrmtd' ) . $strFileName ) );
		}

		require_once $strFileName;
	}

	public function core_recentPostsWidget( $excerpt='', $length=0, $post=false, $args=null ){

		$isMoreMeta = is_array( $args ) &&
			!empty( $args[ 'describe_type' ] ) &&
			$args[ 'describe_type' ] == 'rating_category';

		if(
			$isMoreMeta &&
			class_exists( 'Jvbpd_Module' ) &&
			is_object( $post ) &&
			$post->post_type == jvbpdCore()->var_instance->slug
		) {

			$objModule = new Jvbpd_Module( $post );
			$excerpt = join( false, Array(
				'<div class="javo-shortcode">',
					'<div class="module">',
						sprintf( jvbpdCore()->var_instance->shortcode->contents_with_raty_star( '', $objModule ) ),
						'<div class="meta-moreinfo">',
							sprintf(
								'<span class="meta-category">%s</span>',
								$objModule->c( 'listing_category', esc_html__( "No Category", 'jvfrmtd'	 ) )
							),
							' / ',
							sprintf(
								'<span class="meta-location">%s</span>',
								$objModule->c( 'listing_location', esc_html__( "No Location", 'jvfrmtd'	 ) )
							),
						'</div>',
					'</div>',
				'</div>',
			) );
		}
		return $excerpt;
	}

	public function core_recentPostsWidgetOption( $options=Array() ){
		if( class_exists( 'Lava_Directory_Review' ) )
			$options[ 'rating_category' ] = esc_html__( "Rating & Category ( only 'listing' )", 'jvfrmtd' );

		return $options;
	}

	public function woo_sidebar( $sidebar_id='', $post ) {
		if( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$sidebar_id = 'woo-sidebar';
		}
		return $sidebar_id;

	}

	public function woo_sidebar_position( $position='', $post_id=0 ) {
		if( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			if( is_archive() ) {
				$position = jvbpd_tso()->get( 'woo_archive_sidebar', 'right' );
			}elseif( is_singular( 'product' ) ) {
				$position = jvbpd_tso()->get( 'woo_single_sidebar', 'right' );
			}
		}
		return $position;
	}

	public function renderHeaderSearchMenu( $args=null ) {
		$item_buffer = $menu_classes = Array();
		$menu_classes[] = 'main-menu-item';
		$menu_classes[] = 'menu-item-depth-0';
		$menu_classes[] = 'jvbpd-menu';
		$menu_classes[] = 'jvbpd-search1-opener';
		$menu_classes[] = 'menu-item';
		$menu_classes[] = 'menu-item-type-custom';
		$menu_classes[] = 'menu-item-object-custom';
		$menu_classes[] = 'hidden-sm';
		$menu_classes[] = 'hidden-md';
		$menu_classes[] = 'hidden-lg';
		foreach(
			Array(
				'search-opener' => Array(
					'class' => 'jvbpd-btn-search1-opener',
					'icon' => 'fa fa-search',
				),
				'map-mobile-switch' => Array(
					'class' => 'jvbpd-btn-map-mobile-switcher jvbpd-map-mobile-switch',
					'icon' => 'fa fa-map-marker',
				),
		) as $menuID => $menuMeta ) {
			$item_buffer[] = sprintf(
				'<a class="%1$s"><i class="%2$s"></i></a>',
				$menuMeta[ 'class' ],
				$menuMeta[ 'icon' ]
			);
		}
		return sprintf( '<li class="%1$s">%2$s</li>', join( ' ', $menu_classes ), join( ' ', $item_buffer ) );
	}

	public function renderMobileLogin( $args=null ) {
		$item_buffer = $menu_classes = Array();
		$menu_classes[] = 'text-center';
		$menu_classes[] = 'main-menu-item';
		$menu_classes[] = 'menu-item-depth-0';
		$menu_classes[] = 'menu-item';
		$menu_classes[] = 'menu-item-type-custom';
		$menu_classes[] = 'menu-item-object-custom';
		$menu_classes[] = 'hidden-sm';
		$menu_classes[] = 'hidden-md';
		$menu_classes[] = 'hidden-lg';

		$menu_item = Array();

		if( is_user_logged_in() ) {
			$menu_item[] = Array(
				'href' => wp_logout_url(),
				'class' => 'jvbpd-btn-login inline-block',
				'icon' => '',
				'label' => esc_html__( "Logout", 'jvfrmtd' ),
			);
			$menu_item[] = Array(
				'href' => wp_logout_url(),
				'class' => 'jvbpd-btn-mypage inline-block',
				'icon' => '',
				'label' => esc_html__( "My Page", 'jvfrmtd' ),
			);
		}else{
			$menu_item[] = Array(
				'href' => wp_login_url(),
				'class' => 'jvbpd-btn-login inline-block',
				'icon' => '',
				'label' => esc_html__( "Login", 'jvfrmtd' ),
			);
		}

		foreach( $menu_item as $menuID => $menuMeta ) {
			$item_buffer[] = sprintf(
				'<a class="%2$s" href="%1$s"><i class="%3$s"></i>%4$s</a>',
				$menuMeta[ 'href' ],
				$menuMeta[ 'class' ],
				$menuMeta[ 'icon' ],
				$menuMeta[ 'label' ]
			);
		}
		return sprintf( '<li class="%1$s">%2$s</li>', join( ' ', $menu_classes ), join( ' ', $item_buffer ) );
	}

	public function addHeaderSearchMenu( $items, $args=null ) {
		if( $args->theme_location == 'top_nav_menu_right' ) {
			$items .= $this->renderHeaderSearchMenu( $args );
		}
		if( $args->theme_location == 'top_nav_menu_left' ) {
			$items .= $this->renderMobileLogin( $args );
		}
		return $items;
	}

	public function addSocialLoginButton() {
		if( class_exists( 'LVCSL_Class' ) ) {
			echo do_shortcode( '[lvcsl-login]' );
		}
	}

	public function loadBriefModal() {
		wp_localize_script(
			jvbpdCore()->var_instance->getHandleName( 'frontend' ),
			'jvbpd_elementor_args',
			apply_filters('jvbpd_elementor_params', Array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'settings' => Array(
					'lazyload' => jvbpd_tso()->get( 'lazyload' ),
				),
				'strings' => Array(
					'login' => Array(
						'security' => wp_create_nonce( 'user_login' ),
						'errUserName' => esc_html__( 'usernames with spaces should not be allowed.', 'jvfrmtd' ),
						'errDuplicateUser' => esc_html__( 'The email or username has been taken', 'jvfrmtd' ),
						'errNotAgree' => esc_html__( 'You need to read and agree the terms and conditions to register.', 'jvfrmtd' ),
						'strJoinComplete' => esc_html__( 'Register Complete', 'jvfrmtd' ),
						'errLoginServer' => esc_html__( 'There is a problem with the login server', 'jvfrmtd' ),
						'strSuccessLogin' => esc_html__( 'Logged in successfully', 'jvfrmtd' ),
						'strProcessing' => esc_html__( 'Processing', 'jvfrmtd' ),
					),
					'join' => Array(
						'strSuccessJoin' => esc_html__( 'Registration successful', 'jvfrmtd' ),
						'errPasword' => esc_html__( 'Password and confirm password do not match', 'jvfrmtd' ),
					),
					'map_list_reset_filter' => Array(
						'category' => esc_html__( 'Category', 'jvfrmtd' ),
						'location' => esc_html__( 'Location', 'jvfrmtd' ),
						'address' => esc_html__( 'Address', 'jvfrmtd' ),
						'amenities' => esc_html__( 'Amenities', 'jvfrmtd' ),
					),
				),
			))
		);
		$this->load_template( 'template-modal-brief-modal' );
	}

	public function archive_params( Array $params=Array() ) {
		if( get_queried_object() instanceof WP_Term ) {
			$params[ 'archive_page' ] = Array(
				'taxonomy' => get_queried_object()->taxonomy,
				'term_id' => get_queried_object()->term_id,
			);
		}
		return $params;
	}

	public function sidebar_deactivate($is_sidebar_active) {
		return false;
	}

	public function more_taxonomies_params($params=Array()) {
		$output = Array();
		if(function_exists('javo_moreTax')) {
			$taxonomies = javo_moreTax()->admin->getMoreTaxonomies();
			if(false !== $taxonomies){
				foreach( $taxonomies as $taxonomy ){
					if(empty($taxonomy['name'])){
						continue;
					}
					$output[$taxonomy['name']] = (object) Array(
						'label' => $taxonomy['label'],
						'field' => Array(
							Array(
								'type' => 'selectize',
								'selector' => sprintf('.ui-select [name="list_filter[%s]"]', $taxonomy['name']),
							),
							Array(
								'type' => 'checkbox',
								'selector' => sprintf('input[type="checkbox"][data-tax="%s"]', $taxonomy['name']),
							),
						),
					);
				}
			}
		}
		$params['more_taxonomy'] = $output;
		return $params;
	}

}