<?php
/**
 * @package JavoCore
 */
/**
 * Plugin Name: Javo Core
 * Description: This plugin is requested for Javo wordpress theme. it loads shortcodes and some custom code.
 * Version: 1.0.2.7
 * Author: Javo Themes
 * Author URI: http://javothemes.com/
 * Text Domain: jvfrmtd
 * Domain Path: /languages/
 * License: GPLv2 or later */



if( ! defined( 'ABSPATH' ) )
die;

if(!function_exists('jvbpd_core_active_notice')) {
	add_action('admin_init', 'jvbpd_core_active_notice');
	register_activation_hook(__FILE__, 'jvbpd_core_active_notice');
	function jvbpd_core_active_notice() {
		if(version_compare(phpVersion(), '7.0.0', '<=')){
			$strNotice = sprintf(
				'<p><strong>%1$s : </strong> %2$s<br></p><p>%3$s<br>%4$s : <strong>%5$s</strong></p><p><a href="%6$s">%7$s</a></p>',
				esc_html("Notice", 'jvfrmtd'),
				esc_html("You are using an old version of PHP which is not supported by our theme.
				Please upgrade your PHP version to the newest version ( Recommended 7.3x and minimum 7.0x ).
				If you are using a hosting server, please contact your hosting support team to upgrade it.", 'jvfrmtd'),
				esc_html("After upgrad PHP version, core will be activated and work propely.", 'jvfrmtd'),
				esc_html("The current PHP version", 'jvfrmtd'),
				phpversion(),
				admin_url("plugins.php"),
				esc_html("Back to plugins page", 'jvfrmtd')
			);
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die($strNotice);
		}
	}
}


 // Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}


/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists( 'Jvbpd\\Init' ) ) :
	Jvbpd\Init::registerServices();
endif;


if( ! class_exists( 'Jvbpd_Core' ) ) :

	class Jvbpd_Core {

/* Const : */
		/**
		 * Debug mode on/off
		 * @const boolean
		 * @since 1.0.0
		 */
		const DEBUG = false;


		/**
		 * Debug mode on/off
		 * @const boolean
		 * @since 1.0.0
		 */

		const VER = '1.0.2.0';

		/**
		 * Core Post Type
		 * @const boolean
		 * @since 1.0.0
		 */
		const CORE_POST_TYPE = 'lv_listing';

		/**
		 * Support Post Type
		 * @const boolean
		 * @since 1.0.0
		 */
		const SUPPORT_POST_TYPE = 'lv_ticket';

/* Private : */
		/**
		 * Core Theme Template Name
		 * @var string
		 * @since 1.0.0
		 */
		private $theme_name = '';

		/**
		 * Core Theme Template Name
		 * @var Array
		 * @since 1.0.0
		 * @array_key Theme name
		 * @array_value Core name
		 */
		private $theme_names = Array(
			'listopia' => 'jvbpd',
			'playo' => 'jvbpd',
			'javo_directory' => 'jvbpd',
			'javo_theme' => 'jvbpd',
			'javo' => 'jvbpd',
			'spot' => 'jvbpd',
			'lynk' => 'jvbpd',
		);

		/**
		 * Get Theme Information Object
		 * @var object
		 * @since 1.0.0
		 */
		private $theme = null;

/* Public : */

		/**
		 * Instance object
		 * @var object
		 * @since 1.0.0
		 */
		public static $instance;

		public $prefix = false;

		/**
		 * Template Instance object
		 * @var object
		 * @since 1.0.0
		 */
		public $template_instance = null;

		/**
		 * Template Instance object
		 * @var object
		 * @since 1.0.0
		 */
		public $var_instance = null;

/* Protected : */
		/**
		 * Get Import Directory
		 * @var string
		 * @since 1.0.0
		 */
		public $import_path	= false;

		/**
		 * Get Export Directory
		 * @var string
		 * @since 1.0.0
		 */
		protected $export_path	= true;

		public function __construct( $file ) {

			$this->file = $file;
			$this->folder = basename( dirname( $this->file ) );
			$this->path = dirname( $this->file );

			/* original path */
			$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
			$this->dirt_path = trailingslashit( $this->path ) . 'dir';
			$this->addons_url = esc_url( trailingslashit( plugins_url( '/dir/addons/', $this->file ) ) );
			$this->import_path = trailingslashit( $this->path ) . 'import';
			//$this->export_path = trailingslashit( $this->path ) . 'export';
			$this->include_path = trailingslashit( $this->path ) . 'includes';
			$this->module_path = trailingslashit( $this->path ) . 'modules';
			$this->shortcode_path = trailingslashit( $this->path ) . 'shortcodes';
			$this->template_path = trailingslashit( $this->path ) . 'templates';
			$this->widget_path = trailingslashit( $this->path ) . 'widgets';

			$this->elementor_path = trailingslashit( $this->path ) . 'elementor';

			if( $this->theme_check( $this->theme_names ) ) {
				$this->load_files();
				$this->register_hooks();
			}
			do_action( 'jvbpd_core_init' );
		}

		public function theme_check( $theme_names=Array() ) {
			$this->theme = wp_get_theme();
			$this->template = $this->theme->get( 'Name' );
			if( $this->theme->get( 'Template' ) ) {
				$this->parent = wp_get_theme(  $this->theme->get( 'Template' ) );
				$this->template = $this->parent->get( 'Name' );
			}
			$this->template = str_replace( ' ', '_', strtolower( $this->template ) );
			$strPrefix = sanitize_key( $this->template );
			$isAllowTheme = false;
			if( array_key_exists( $strPrefix, $theme_names ) ) {
				$this->prefix = $theme_names[ $strPrefix ];
				$isAllowTheme = true;
			}
			return $isAllowTheme;
		}

		public function load_files() {
			$arrFIles		= Array();
			$arrFIles[]		= $this->shortcode_path . '/core-shortcodes.php';
			$arrFIles[]		= $this->import_path . '/javo-import.php';
			$arrFIles[]		= $this->export_path . '/javo-export.php';
			$arrFIles[]		= $this->dirt_path . '/class/class-bp-ext.php';
			$arrFIles[]		= $this->include_path . '/class-admin.php';
			$arrFIles[]		= $this->include_path . '/class-events.php';
			$arrFIles[]		= $this->include_path . '/class-process.php';
			$arrFIles[]		= $this->include_path . '/class-template.php';
			$arrFIles[]		= $this->include_path . '/class-shortcode.php';
			$arrFIles[]		= $this->include_path . '/class-var.php';
			$arrFIles[]		= $this->include_path . '/class-admin-helper.php';
			$arrFIles[]		= $this->include_path . '/function-ajax.php';
			$arrFIles[]		= $this->include_path . '/functions.php';
			$arrFIles[]		= $this->include_path . '/class-portfolio.php';
			$arrFIles[]		= $this->include_path . '/class-elementor.php';
			$arrFIles[]		= $this->include_path . '/the-grid-core.php';

			if( defined( 'ELEMENTOR_VERSION' ) ) {
				$arrFIles[] = $this->elementor_path . '/class-core.php';
			}

			if( !empty( $arrFIles ) ) foreach( $arrFIles as $filename ) {
				if( file_exists( $filename ) ) {
					require_once( $filename );
				}
			}
		}

		public function register_hooks() {
			add_action( 'init', Array( $this, 'load_core' ), 99 );
			load_plugin_textdomain('jvfrmtd', false, $this->folder . '/languages/');

			add_filter( 'jvbpd_doc_url', array( $this, 'doc_url' ) );
			add_filter( 'jvbpd_support_url', array( $this, 'support_url' ) );

			if( class_exists( 'Jvbpd_Core_Admin' ) ) {
				$this->admin = new Jvbpd_Core_Admin;
			}

			$this->template_instance = new Jvbpd_Core_Template;
			$this->shortcode_instance = new Jvbpd_Core_Shortcode;
			$this->custom_bp_isntance = new Jvbpd_bp_dir_ext;
		}

		public function doc_url() { return esc_url_raw( 'docs.wpjavo.com/theme/' ); }
		public function support_url() { return esc_url_raw( 'javothemes.com/support/' ); }

		public function load_core() {
			if( function_exists( 'jvbpd_register_shortcodes' ) ) {
				jvbpd_register_shortcodes( $this->theme_names[ $this->template ] . '_' );
			}

			if( class_exists( 'Jvbpd_Import' ) ) {
				$this->import = new Jvbpd_Import;
				$GLOBALS[ 'jvbpd_import' ]	= $this->import;
			}

			if( class_exists( 'jvbpd_Export' ) ) {
				$this->export = new jvbpd_Export;
				$GLOBALS[ 'jvbpd_Export' ]	= $this->export;
			}
			$this->var_instance = new Jvbpd_Core_Variable( $this->file );
		}

		public function getSlug() { return self::CORE_POST_TYPE; }
		public function getVersion(){ return self::VER; }

		public function getTemplateName() {
			return $this->template;
		}

		public static function get_instance( $file=null ) {
			if( null === self::$instance )
				self::$instance = new self( $file );

			return self::$instance;
		}
	}
endif;
if( ! function_exists( 'jvbpdCore' ) ) {
	function jvbpdCore() {
		return Jvbpd_Core::get_instance( __FILE__ );
	}
	jvbpdCore();
}
