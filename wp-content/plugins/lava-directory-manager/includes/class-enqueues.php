<?php

if( !defined( 'ABSPATH' ) || ! class_exists( 'Lava_Directory_Manager' ) )
	die;

class Lava_Directory_Manager_Enqueues extends Lava_Directory_Manager
{
	private $lava_ssl = 'http://';

	public $handle_prefix = 'lava-directory-manager-';

	public function __construct() {
		if( is_ssl() )
			$this->lava_ssl							=  'https://';

		add_action( 'wp_enqueue_scripts'				, Array( $this, 'register_styles' ), 20 );
		add_action( 'wp_enqueue_scripts'				, Array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts'			, Array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts'			, Array( $this, 'admin_styles' ) );
	}

	public function register_styles() {
		global $lava_directory_manager;
		$lava_load_styles							=
			Array(
				'flexslider.css'					=> '2.5.0'
				, 'selectize.css'					=> '0.12.0'
				, "$lava_directory_manager->folder}.css" => '0.1.0'
			);

		if( !empty( $lava_load_styles ) )
			foreach( $lava_load_styles as $filename => $version )
			{
				wp_register_style(
					sanitize_title( $filename )
					, lava_get_directory_manager_assets_url() . "css/{$filename}"
					, false
					, $version
				);
				wp_enqueue_style( sanitize_title( $filename ) );
			}
	}

	public function register_scripts() {
		global $wpdb;

		$lava_google_api = '';

		if( $lava_google_api = false )
			$lava_google_api .= "&key={$lava_google_api}";

		if( $lava_google_lang = false )
			$lava_google_api .= "&language={$lava_google_lang}";

		$lava_load_scripts = Array(
			'scripts.js' => Array( '0.0.1', true ),
			'admin.js' => Array( '0.0.1', true )	,
			'admin-addons.js' => Array( '0.0.1', true ),
			'admin-edit-term.js' => Array( '0.0.1', true ),
			'admin-metabox.js' => Array( '0.0.1', true ),
			'jquery.lava.msg.js' => Array( '0.0.1', true ),
			'gmap3.js' => Array( '0.0.1', false ),
			'lava-submit-script.js' => Array( '0.0.1', false ),
			'lava-single.js' => Array( '0.0.2', true ),
			'lava-map.js' => Array( '0.0.2', true ),
			'lava-listing.js' => Array( '0.0.2', true ),
			'lava-dashboard.js' => Array( '0.0.2', true ),
			'jquery.flexslider-min.js'	=> Array( '2.5.0', true ),
			'google.map.infobubble.js' => Array( '1.0.0', true ),
			'jsoneditor.min.js' => Array( '7.0.4', true ),
		);

		if( !empty( $lava_load_scripts ) ) {
			foreach( $lava_load_scripts as $filename => $args ) {
				wp_register_script(
					$this->getHandleName( $filename ),
					lava_get_directory_manager_assets_url() . "js/{$filename}",
					Array( 'jquery' ), $args[0], $args[1]
				);
			}
		}

		$strAppend = '';

		if( $strAPIKEY = lava_directory()->admin->get_settings( 'google_map_api', '' ) ) {
			$strAppend = '&key=' . $strAPIKEY;
		}

		wp_enqueue_script( 'google-maps', sprintf(
			'%1$smaps.googleapis.com/maps/api/js?libraries=places,geometry%2$s',
			$this->lava_ssl, $strAppend
		), Array('jquery'), "0.0.1", false );
		wp_enqueue_script( $this->getHandleName( 'scripts.js' ) );
		wp_enqueue_script( 'lava-directory-manager-gmap3-js' );
		if(is_admin() && 'lv_listing_page_lava-lv_listing-settings' == get_current_screen()->id) {
			wp_enqueue_script( $this->getHandleName( 'jsoneditor.min.js' ) );
		}
	}

	public function getHandleName( $handle='' ){
		return 	sanitize_title( $this->handle_prefix . $handle );
	}

	public function admin_styles() {
		wp_enqueue_style( sanitize_title( self::$instance->folder . '-admin' ), lava_get_directory_manager_assets_url() . 'css/admin.css', false, '1.0.0' );
		wp_enqueue_style( 'selectize-style', lava_get_directory_manager_assets_url() . 'css/selectize.css', false, '1.0.0' );
	}
}
