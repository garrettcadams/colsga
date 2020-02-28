<?php

if( !defined( 'ABSPATH' ) || ! class_exists( 'Lava_Bp_Post' ) ) {
	die;
}

class Lava_Bp_Post_Enqueues extends Lava_Bp_Post {

	private $lava_ssl = 'http://';
	public $handle_prefix = 'lava-bpp-';

	public function __construct() {
		if( is_ssl() ) {
			$this->lava_ssl = 'https://';
		}
		add_action( 'wp_enqueue_scripts', Array( $this, 'register_styles' ), 20 );
		add_action( 'wp_enqueue_scripts', Array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts', Array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts', Array( $this, 'admin_styles' ) );
	}

	public function register_styles() {
		$lava_load_styles = Array(
			'flexslider.css'=> '2.5.0',
			'selectize.css'=> '0.12.0',
			lava_bpp()->folder . '.css' => lava_bpp()->getVersion(),
		);

		if( !empty( $lava_load_styles ) ) {
			foreach( $lava_load_styles as $filename => $version ) {
				wp_register_style(
					sanitize_title( $filename ),
					lava_bpp()->assets_url . "css/{$filename}",
					false, $version
				);
				wp_enqueue_style( sanitize_title( $filename ) );
			}
		}
	}

	public function register_scripts() {
		global $wpdb;

		$lava_google_api = '';

		if( $lava_google_api = false ) {
			$lava_google_api .= "&key={$lava_google_api}";
		}

		if( $lava_google_lang = false ) {
			$lava_google_api .= "&language={$lava_google_lang}";
		}

		$lava_load_scripts = Array(
			'scripts.js' => Array( '0.0.1', true ),
			'admin.js' => Array( '0.0.1', true )	,
			'admin-addons.js' => Array( '0.0.1', true ),
			'admin-edit-term.js' => Array( '0.0.1', true ),
			'admin-metabox.js' => Array( '0.0.1', true ),
		/*	'less.min.js' => Array( '2.4.1', false ), */
			'jquery.lava.msg.js' => Array( '0.0.1', true ),
			'lava-submit-script.js' => Array( '0.0.1', false ),
			'lava-single.js' => Array( '0.0.2', true ),
			'lava-dashboard.js' => Array( '0.0.2', true ),
			'jquery.flexslider-min.js'	=> Array( '2.5.0', true ),
		);

		if( !empty( $lava_load_scripts ) ) {
			foreach( $lava_load_scripts as $filename => $args ) {
				wp_register_script(
					$this->getHandleName( $filename ),
					lava_bpp()->assets_url . "js/{$filename}",
					Array( 'jquery' ), $args[0], $args[1]
				);
			}
		}

		$strAppend = '';
		if( $strAPIKEY = lava_bpp()->admin->get_settings( 'google_map_api', '' ) ) {
			$strAppend = '&key=' . $strAPIKEY;
		}

	}

	public function getHandleName( $handle='' ){
		return 	sanitize_title( $this->handle_prefix . $handle );
	}

	public function admin_styles() {
		wp_enqueue_style(
			sanitize_title( lava_bpp()->folder . '-admin' ),
			lava_bpp()->assets_url . 'css/admin.css', false, lava_bpp()->getVersion()
		);
	}
}

