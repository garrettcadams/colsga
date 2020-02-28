<?php
/**
 * Plugin Name: Lava Directory Manager
 * Plugin URI: http://lava-code.com/directory/
 * Description: Lava Directory Manager Plugin
 * Version: 1.1.24

 * Author: Lavacode
 * Author URI: http://lava-code.com/
 * Text Domain: Lavacode
 * Domain Path: /languages/
 */
/*
    Copyright Automattic and many other contributors.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if( ! defined( 'ABSPATH' ) )
	die();

if( ! class_exists( 'Lava_Directory_Manager' ) ) :

	class Lava_Directory_Manager {

		public $path = false;
		public static $instance;
		private $version = '1.1.24';

		public function __construct( $file ) {
			$this->file = $file;
			$this->folder = basename( dirname( $this->file ) );
			$this->path = dirname( $this->file );
			$this->template_path = trailingslashit( $this->path ) . 'templates';
			$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
			$this->image_url = esc_url( trailingslashit( $this->assets_url . 'images/' ) );

			register_activation_hook( $this->file, Array( $this, 'register' ) );
			register_deactivation_hook( $this->file, Array( $this, 'unregister' ) );

			$this->load_files();
			$this->register_hooks();
			$this->enqueue = new Lava_Directory_Manager_Enqueues;
		}

		public function getHookName( $suffix='' ) {
			$suffix = !empty( $suffix ) ? '_' . $suffix : $suffix;
			return sprintf( '%1$s%2$s', $this->getName(), $suffix );
		}

		public function register() {
			flush_rewrite_rules();
			do_action( $this->getHookName( 'Register' ) );
		}

		public function unregister(){ do_action( $this->getHookName( 'Unregister' ) ); }
		public function getVersion() { return $this->version; }
		public function getName(){ return get_class( $this ); }
		public function getPluginDir() { return trailingslashit( dirname( dirname( __FILE__ ) ) ); }

		public function load_files() {
			require_once( 'includes/class-lava-array.php' );
			require_once( 'includes/class-core.php' );
			require_once( 'includes/class-admin.php' );
			require_once( 'includes/class-field.php' );
			require_once( 'includes/class-enqueues.php' );
			require_once( 'includes/class-widgets.php' );
			require_once( 'includes/class-shortcodes.php' );
			require_once( 'includes/class-template.php' );
			require_once( 'includes/class-submit.php' );

			// disable
			require_once 'includes/class-addons.php';

			$this->core = new Lava_Directory_Manager_Func;
			$GLOBALS[ 'lava_directory_manager_func' ] = $this->core;
		}

		public function register_hooks() {
			add_action( 'init', Array( $this, 'initialize' ) );
			add_action( 'widgets_init', Array( $this, 'register_sidebar' ) );
			add_action( 'widgets_init', Array( $this, 'register_widgets' ) );
			load_plugin_textdomain( 'Lavacode', false, $this->folder . '/languages/' );
		}

		public function initialize() {
			add_rewrite_tag( '%edit%', '([^&]+)' );
			$this->submit = new Lava_Directory_Manager_Submit;
			$this->addons = new Lava_Directory_Manager_Addons;
			$this->admin = new Lava_Directory_Manager_Admin;
			$this->template = new Lava_Directory_Manager_template;
			$this->shortcode = new Lava_Directory_Manager_Shortcodes;

			$GLOBALS[ 'lava_directory_manager_admin' ] = $this->admin;
			do_action( 'lava_directory_manager_init' );
		}

		public function register_sidebar() {
			$post_type = lava_directory()->core->slug;
			register_sidebar(
				Array(
					'name' => sprintf( __( "Lava Single Sidebar (%s)", 'Lavacode' ), $post_type ),
					'id'	 => "lava-{$post_type}-single-sidebar",
				)
			);
		}

		public function register_widgets() {
			$this->widget = new Lava_Directory_Manager_widgets;
		}

		public static function get_instance( $file ) {
			if( ! self::$instance )
				self::$instance = new self( $file );
			return self::$instance;
		}

	}
endif;

if( !function_exists( 'lava_directory' ) ) :
	function lava_directory() {
		$objInstance = Lava_Directory_Manager::get_instance( __FILE__ );
		$GLOBALS[ 'lava_directory_manager' ] = $objInstance;
		return $objInstance;
	}
	lava_directory();
endif;