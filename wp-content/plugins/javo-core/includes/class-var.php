<?php
if( !defined( 'ABSPATH' ) )
	die;

class Jvbpd_Core_Variable extends Jvbpd_Core {
	/**
	 *	Required Initialize Settings
	 */
	const SLUG			= 'lv_listing';
	const SUPPORT		= 'lv_ticket';
	const NAME			= 'listing';
	const CORE			= 'Jvbpd_Core';
	const FEATURED_CAT	= '_category';
	const MAINPLUG		= '';

	/**
	 *	Additional Initialize Settings
	 */
	const REVIEW = '';

	const LIMIT_CHART_ITEMS = 5;

	public $slug;
	protected $template_path = false;

	public function __construct( $file ) {
		$this->initialize( $file );
		$this->load_files();
		$this->register_hoook();
	}

	public function initialize( $file ) {
		$this->file				= $file;
		$this->folder			= get_parent_theme_file_path( $this->file );
		$this->dir				= ''; //trailingslashit( JVBPD_THEME_DIR . '/includes' );
		$this->assets_dir		= trailingslashit( $this->dir . 'assets' );
		$this->path				= dirname( $this->file );
		$this->template_path	= trailingslashit( $this->path ) . 'templates';

		$this->slug				= self::SLUG;
	}

	public function load_files() {}

	public function register_hoook() {
		add_action( 'init', array( $this, 'load_templateClass' ) );
		// Require Plugins
		add_action( 'jvbpd_tgmpa_plugins', Array( $this, 'bp_tgmpa_plugins' ) );
		add_action( 'jvbpd_helper_require_plugins', Array( $this, 'helper_require_plugins' ) );
		add_action( 'jvbpd_helper_require_plugins_pass', Array( $this, 'helper_require_plugins_bool' ) );

		add_action( 'wp_enqueue_scripts', Array( $this, 'register_resources' ) );
		add_action( 'init', Array( $this, 'custom_object' ), 100 );

		add_filter( 'jvbpd_theme_setting_pages', Array( $this, 'woo_page' ) );
		add_filter( 'jvbpd_theme_setting_pages', Array( $this, 'bp_page' ) );
		add_filter( 'jvbpd_theme_setting_pages', Array( $this, 'page_template_settings' ) );
		add_filter( 'jvbpd_theme_setting_pages', array( $this, 'lvbbp_page' ) );

		// add_filter( 'jvbpd_dashboard_slugs' , Array( $this, 'custom_register_slug' ) );

		add_filter( 'jvbpd_core_submit_page_link', array( $this, 'core_submit_page_link' ) );
		// add_action( 'wp', array( $this, 'save_chart_items' ) );

		add_filter( 'lava_' . self::SLUG . '_json_addition', Array( $this, 'json_append' ), 10, 3 );

		$this->save_chart_items();
	}

	public function load_templateClass() {
		$this->template = new LynkMainCore_Template;
	}

	public function getSlug() { return self::SLUG; }
	public function getFeatureCategory() { return self::SLUG . self::FEATURED_CAT; }

	public function getCoreName( $suffix=false ){
		$strSuffix = $suffix ? '_' . $suffix : false;
		return self::CORE . $strSuffix;
	}

	public function getHandleName( $strName='' ){ return sanitize_title( 'jv-' . $strName ); }

	public function bp_tgmpa_plugins( $plugins=Array() ) {
		return wp_parse_args(
			Array(

				// Javo Bp Core
				Array(
					'name' => 'Lynk Core',
					'slug' => 'jvbpd-core',
					'version' => '1.0.0.24',
					'required' => true,
					'force_activation' => false,
					'force_deactivation' => false,
					'external_url' => '',
					'source' => get_template_directory() . '/library/plugins/jvbpd-core.zip',
					'image_url' => JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-jvbpd-core.png',
				),

				// BuddyPress
				array(
					'name'						=> 'BuddyPress', // The plugin name
					'slug'						=> 'buddypress', // The plugin slug (typically the folder name)
					'required'					=> true, // If false, the plugin is only 'recommended' instead of required
					'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
					'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
					'image_url'					=> esc_url_raw( JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-buddypress.png' ),
				),

				// BBpress
				array(
					'name'						=> 'BBpress', // The plugin name
					'slug'						=> 'bbpress', // The plugin slug (typically the folder name)
					'required'					=> true, // If false, the plugin is only 'recommended' instead of required
					'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
					'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
					'image_url'					=> esc_url_raw( JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-bbpress.png' ),
				),


				// Lava Bp Post
				array(
					'name'						=> 'Lava Bp Post', // The plugin name
					'slug'						=> 'lava-bp-post', // The plugin slug (typically the folder name)
					'required'					=> false, // If false, the plugin is only 'recommended' instead of required
					'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
					'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
					'image_url'					=> JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-bp-post.png',
				),

				// Lava ajax search
				array(
					'name'						=> 'Lava Ajax Search', // The plugin name
					'slug'						=> 'lava-ajax-search', // The plugin slug (typically the folder name)
					'required'					=> false, // If false, the plugin is only 'recommended' instead of required
					'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
					'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
					'image_url'					=> JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-ajax-search.png',
				),

				// Visual Composer
				array(
					'name'						=> 'WPBakery Visual Composer', // The plugin name
					'slug'						=> 'js_composer', // The plugin slug (typically the folder name)
					'source'					=> get_template_directory() . '/library/plugins/js_composer.zip', // The plugin source
					'required'					=> true, // If false, the plugin is only 'recommended' instead of required
					'version'					=> '5.2', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
					'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
					'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
					'external_url'				=> '', // If set, overrides default API URL and points to an external URL
					'image_url'					=> JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-js_composer.png',
				),

				// Ultimate Addons
				array(
					'name'						=> 'Ultimate Addons for Visual Composer', // The plugin name
					'slug'						=> 'Ultimate_VC_Addons', // The plugin slug (typically the folder name)
					'source'					=> get_template_directory() . '/library/plugins/Ultimate_VC_Addons.zip', // The plugin source
					'required'					=> true, // If false, the plugin is only 'recommended' instead of required
					'version'					=> '3.16.13', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
					'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
					'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
					'external_url'				=> '', // If set, overrides default API URL and points to an external URL
					'image_url'					=> JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-Ultimate_VC_Addons.png',
				),
				// The Grid
				array(
					'name'						=> 'The Grid', // The plugin name
					'slug'						=> 'the-grid', // The plugin slug (typically the folder name)
					'source'					=> get_template_directory() . '/library/plugins/the-grid.zip', // The plugin source
					'required'					=> true, // If false, the plugin is only 'recommended' instead of required
					'version'					=> '2.4.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
					'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
					'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
					'external_url'				=> '', // If set, overrides default API URL and points to an external URL
					'image_url'					=> JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-javo-the-grid-core-logo.png',
				),
			), $plugins
		);
	}

	public function register_resources() {

		$jvbpd_load_styles = Array();
		$jvbpd_load_scripts = Array(
			'single.js' => '1.0.0',
			'map-template.js' => '1.0.0',
			'jquery.javo_search_shortcode.js'	=> '0.1.0',
		);

		if( !empty( $jvbpd_load_styles ) ) : foreach( $jvbpd_load_styles as $filename => $version ) {
			wp_register_style(
				$this->getHandleName( $filename ),
				$this->assets_dir . "css/{$filename}",
				Array(),
				$version
			);
		} endif;

		if( !empty( $jvbpd_load_scripts ) ) : foreach( $jvbpd_load_scripts as $filename => $version ) {
			wp_register_script(
				$this->getHandleName( $filename ),
				$this->assets_dir . "js/{$filename}",
				Array( 'jquery' ),
				$version,
				true
			);
		} endif;
	}

	public function main_slug_page( $pages ){
		return wp_parse_args(
			Array(
				'lv_listing'			=> Array(
					esc_html__( "Listing", 'jvfrmtd' ), false
					, 'priority'		=> 35
					, 'external'		=> $this->template_path . '/admin-theme-settings-item.php'
				)
			)
			, $pages
		);
	}

	public function map_page( $pages ) {
		return wp_parse_args(
			Array(
				'map'			=> Array(
					esc_html__( "Map", 'jvfrmtd' ), false
					, 'priority'		=> 32
					, 'external'		=> $this->template_path . '/admin-theme-settings-map.php'
				)
			)
			, $pages
		);
	}

	public function bp_page( $pages=Array() ) {

		if( function_exists( 'BuddyPress' ) ) {
			$pages[ 'boddypress' ] = Array(
				esc_html__( "BuddyPress", 'jvfrmtd' ), false,
				'priority' => 71,
				'external' => $this->template_path . '/admin-theme-settings-bp.php',

			);
		}
		return $pages;
	}

	public function page_template_settings( $pages=Array() ) {
			$pages[ 'lvdr' ] = Array(
				esc_html__( "Pages", 'jvfrmtd' ), false,
				'priority' => 31,
				'external' => $this->template_path . '/admin-theme-settings-lvdr.php',
			);
		return $pages;
	}

	public function lvbbp_page( $pages=Array() ) {

		if( function_exists( 'lava_bpp' ) ) {
			$pages[ 'lava_bpp' ] = Array(
				esc_html__( "Lava Bp Post", 'jvfrmtd' ), false,
				'priority' => 75,
				'external' => $this->template_path . '/admin-theme-settings-lvbpp.php',
			);
		}
		return $pages;
	}

	public function woo_page( $pages=Array() ) {

		if( function_exists( 'WC' ) ) {
			$pages[ 'woocommerce' ] = Array(
				esc_html__( "Woocommerce", 'jvfrmtd' ), false,
				'priority' => 75,
				'external' => $this->template_path . '/admin-theme-settings-woo.php',
			);
		}
		return $pages;
	}

	public function enqueue_php_css_array( $csses=Array() ){
		return wp_parse_args(
			Array(
				'includes-assets-extra' => Array(
					'dir' => $this->assets_dir . 'css',
					'file' => 'extra.css',
				)
			),
			$csses
		);
	}

	public function enqueue_php_less_array( $lesses=Array() ) {
		return wp_parse_args(
			Array(
				'includes-assets-extra' => Array(
					'dir' => $this->assets_dir . 'css',
					'file' => 'extra.less',
				)
			),
			$lesses
		);
	}

	public function helper_require_plugins( $plugins=Array() ) {
		return wp_parse_args(
			Array(
				'Jvbpd_Core' => esc_html__( "Lynk Core", 'jvfrmtd' ),
			), $plugins
		);
	}

	public function helper_require_plugins_bool( $boolPass=false ) {
		return $boolPass && class_exists( 'Jvbpd_Core' );
	}

	public function new_item_redirect( $URL, $post_id ){

		$is_update = isset( $_POST[ 'post_id' ] ) && intVal( $_POST[ 'post_id' ] ) > 0;

		if( $is_update )
			return $URL;
		return $URL;
	}

	public function custom_register_slug( $args=Array() ) {
		return wp_parse_args(
			Array(
				'JVBPD_ADDITEM_SLUG' => 'add-'.self::SLUG,
				'JVBPD_ADDEVENT_SLUG' => 'add-event',
				'JVBPD_MY_EVENTS_SLUG' => 'events',
				'JVBPD_FAVORITE' => 'favorite',
				'JVBPD_MYLISTS' => 'my-list',
				'JVBPD_ORDERS' => 'orders',
				'JVBPD_REVIEW_RECEIVED' => 'review-received',
				'JVBPD_REVIEW_SUBMITTED' => 'review-submitted',
				'JVBPD_ITEM_PUBLISHED' => 'item-published',
				'JVBPD_ITEM_PENDING' => 'item-pending',
				'JVBPD_ITEM_EXPIRED' => 'item-expired',
				'JVBPD_CONTACT' => 'contact',
				'JVBPD_REPORT' => 'report',
				'JVBPD_REPORT_EVENT' => 'report-event',
				'JVBPD_REPORT_SETTINGS' => 'report-settings',
				'JVBPD_LIBRARY' => 'library',
			), $args
		);
	}

	public function custom_object() {
		// Exclude Search
		$objPostType = get_post_type_object( self::SLUG );

		if( is_object( $objPostType ) ) {
			$objPostType->exclude_from_search = true;
		}
	}

	public function getUserListingCount( $user_id=0, $status='publish' ) {

		$strExpire = 'expire';
		$status = array_filter( (array) $status );
		$strQueryStatus = array_diff( $status, Array( $strExpire ) );

		$arrQuery = Array(
			'post_type' => self::SLUG,
			'author' => $user_id,
			'post_status' => $strQueryStatus,
			'posts_per_page' => -1
		);

		if( is_array( $status ) && in_array( $strExpire, $status ) ) {
			$arrQuery[ 'meta_query' ][] =  Array(
				'type' => 'NUMERIC',
				'key' => 'lv_expire_day',
				'value' => current_time( 'timestamp' ),
				'compare' => '<=',
			);
		}

		$objWPQ = new WP_Query( $arrQuery );
		return $objWPQ->found_posts;
	}

	public function getUserEventsCount( $user_id=0 ) {
		$intCount = 0;
		if( function_exists( 'tribe_get_events' ) ) {
			$lava_user_events = tribe_get_events(
				Array(
					'author' => $user_id,
					'posts_per_page' => -1,
				)
			);
			$lava_user_events = array_filter( (array) $lava_user_events );
			$intCount = sizeof( $lava_user_events );
		}
		return $intCount;
	}

	public function core_submit_page_link( $url ) {
		return jvbpd_getCurrentUserPage( 'add-' . jvbpdCore()->getSlug() );
	}

	public function custom_manager_option( $metas=Array() ) {
		$prepend = Array(
			'_header_type' => Array(
				'label'		=> esc_html__( "Header Type", 'jvfrmtd' ),
				'element'	=> 'select',
				'class'		=> 'all-options',
				'values'	=> Array(
					''  => esc_html__( "Default as theme settings", 'jvfrmtd' ),
					'featured' => esc_html__( "Featured image", 'jvfrmtd' ),
					'listing_category'	=> esc_html__( "Listing category featured image", 'jvfrmtd' ),
					'grid_style' => esc_html__( "Grid Style", 'jvfrmtd' ),
				),
			),
		);

		/*
		foreach( jvbpd_elements_tools()->getStaticACFieldMeta() as $meta_key => $label ) {
			$params = Array(
				'label' => $label,
				'element'	=> 'input',
				'type' => 'text',
			);

			if( in_array( $meta_key, Array( 'lvac_bedrooms', 'lvac_bathrooms', 'lvac_garages' ) ) ) {
				$params[ 'element' ] = 'select';
				$params[ 'values' ] = wp_parse_args( jvbpd_elements_tools()->get_select_range(), array( '' => $label ) );
			}

			if( in_array( $meta_key, Array( 'lvac_default_price', 'lvac_garages_size', 'lvac_area', 'lvac_land_area' ) ) ) {
				$params[ 'type' ] = 'number';
			}

			$prepend[ $meta_key ] = $params;
		} */

		return wp_parse_args( $metas, $prepend );
	}

	public function json_append( $args, $post_id, $objTerm ) {
		$args[ 'f' ] = get_post_meta( $post_id, '_featured_item', true );
		/*
		foreach( array_keys( jvbpd_elements_tools()->getStaticACFieldMeta() ) as $meta_key ) {
			$args[ $meta_key ] = get_post_meta( $post_id, $meta_key, true );
		} */
		return $args;
	}

	public function save_chart_items() {
		$user_id = get_current_user_id();
		if( isset( $_POST[ 'jvbpd_mypage_chart_items' ] ) ) {
			$arrItems = array_filter( (array) $_POST[ 'jvbpd_mypage_chart_items' ] );
			if( self::LIMIT_CHART_ITEMS < sizeof( $arrItems ) ) {
				$this->reportSettingMessage = sprintf( esc_html__( "Limited amount of chart item: %s ( You have selected : %s )", 'jvfrmtd' ), self::LIMIT_CHART_ITEMS, sizeof( $arrItems ) );
				add_filter( 'jvbpd_mypage_respot_setting_result', array( $this, 'resport_setting_err' ) );
			}else{
				update_user_meta( $user_id, '_mypage_chart_items', $_POST[ 'jvbpd_mypage_chart_items' ] );
			}
		}
		if( isset( $_POST[ 'bp_mypage_chart_events' ] ) ) {
			$arrItems = array_filter( (array) $_POST[ 'bp_mypage_chart_events' ] );
			if( self::LIMIT_CHART_ITEMS < sizeof( $arrItems ) ) {
				$this->reportSettingMessage = sprintf( esc_html__( "Limited amount of chart item: %s ( You have selected : %s )", 'jvfrmtd' ), self::LIMIT_CHART_ITEMS, sizeof( $arrItems ) );
				add_filter( 'jvbpd_mypage_respot_setting_result', array( $this, 'resport_setting_err' ) );
			}else{
				update_user_meta( $user_id, '_mypage_chart_events', $_POST[ 'bp_mypage_chart_events' ] );
			}
		}
	}

	public function resport_setting_err( $_return=false ) {
		return new WP_Error( 'report_setting_err', $this->reportSettingMessage );
	}
}