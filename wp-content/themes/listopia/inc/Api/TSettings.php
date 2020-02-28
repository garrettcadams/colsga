<?php
/*
 *
 * jvbpd_get_theme_settings
 *
 */
class jvbpd_get_theme_settings {

	const OPTIONS_KEY = 'jvbpd_themes_settings';

	private $options;
	public $map;
	public $header;
	public $pages = Array();
	public static $instance = null;

	public function __construct() {

		$this->setVeriables();
		$this->getThemeInformation();
	}

	public function setVeriables() {
		if( !$this->options ) {
			$this->get_options();
		}
		if( class_exists( 'jvbpd_array' ) ) {
			$this->map = new jvbpd_array( (Array) $this->get( 'map', array() ) );
			$this->header = new jvbpd_array( (Array) $this->get( 'hd', array() ) );
		}
	}

	public function getOptionKey() { return self::OPTIONS_KEY; }

	public function get_options() {
		$strOptions		= $this->get_option_orgin();
		$this->options	=  maybe_unserialize( $strOptions );
	}

	public function get_option_orgin() {
		return get_option( self::OPTIONS_KEY );
	}

	public static function getAll() {
		return self::$instance->options;
	}

	public function get( $key=false, $default=NULL ) {
		if( empty( $key ) || ! is_array( $this->options ) ) {
			return $default;
		}

		if( array_key_exists( $key, $this->options ) ) {
			if( is_numeric( $this->options[ $key ] ) ) {
				return $this->options[ $key ];
			}else{
				if( !empty( $this->options[ $key ] ) ) {
					$default = $this->options[ $key ];
				}
			}
		}
		return $default;
	}

	public function getPages() {
		if( !empty( $this->pages ) ) {
			return $this->pages;
		}

		$query_args = Array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		);

		$query = new WP_Query;
		$this->pages = $query->query( $query_args );
		return $this->pages;
	}

	public function getNoImage() {
		$strImageFolder = defined( 'JVBPD_IMG_DIR' ) ? JVBPD_IMG_DIR : get_template_directory_uri() . '/assets/images';
		$strNoImageURL = $strImageFolder . '/no-image.png';
		$strNoImage = $this->get( 'no_image', false );
		if( !empty( $strNoImage ) ) {
			$strNoImageURL = $strNoImage;
		}
		return $strNoImageURL;
	}

	public function getThemeInformation() {
		$this->theme = wp_get_theme();
		$this->themeName = $this->theme->get( 'Name' );
		$this->templateName = $this->theme->get( 'Template' );
		if( $this->templateName ) {
			$this->themeName = $this->templateName;
		}
	}

	public static function getInstance(){
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

if( !function_exists( 'jvbpd_tso' ) ):
	function jvbpd_tso() {
		$objInstance = jvbpd_get_theme_settings::getInstance();
		$GLOBALS[ 'jvbpd_tso' ] = $objInstance;
		return $objInstance;
	}
	jvbpd_tso();
endif;