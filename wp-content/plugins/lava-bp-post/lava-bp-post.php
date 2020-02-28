<?php
/**
 * Plugin Name: Lava Bp Post
 * Plugin URI: http://lava-code.com/bp-post/
 * Description: Lava Bp Post
 * Version: 1.0.8

 * Author: Lavacode
 * Author URI: http://lava-code.com/
 * Text Domain: lvbp-bp-post
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

if( ! class_exists( 'Lava_Bp_Post' ) ) :

	class Lava_Bp_Post {

		public static $instance;

		private $version = '1.0.8';
		public  $path = false;

		public function __construct( $file ) {
			$this->file = $file;
			$this->folder = basename( dirname( $this->file ) );
			$this->path = dirname( $this->file );
			$this->template_path = trailingslashit( $this->path ) . 'templates';
			$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
			$this->image_url = esc_url( trailingslashit( $this->assets_url . 'images/' ) );
		}

		public function register() {
			register_activation_hook( $this->file, Array( $this, 'plugin_active' ) );
			register_deactivation_hook( $this->file, Array( $this, 'unregister' ) );

			$this->load_files();
			$this->register_hooks();
			$this->enqueue = new Lava_Bp_Post_Enqueues;
		}

		public function getHookName( $suffix='' ) {
			$suffix = !empty( $suffix ) ? '_' . $suffix : $suffix;
			return sprintf( '%1$s%2$s', $this->getName(), $suffix );
		}

		public function plugin_active() {
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
			require_once( 'includes/class-shortcodes.php' );
			require_once( 'includes/class-template.php' );
			require_once( 'includes/class-submit.php' );

			// disable
			// require_once 'includes/class-addons.php';

			$this->core = new Lava_Bp_Post_Func;
			$GLOBALS[ 'lava_bpp_func' ] = $this->core;
		}

		public function register_hooks() {
			add_action( 'init', Array( $this, 'initialize' ) );
			load_plugin_textdomain( 'lvbp-bp-post', false, $this->folder . '/languages/' );
		}

		public function initialize() {
			add_rewrite_tag( '%edit%', '([^&]+)' );
			$this->submit = new Lava_Bp_Post_Submit;
			$this->admin = new Lava_Bp_Post_Admin;
			$this->template = new Lava_Bp_Post_template;
			$this->shortcode = new Lava_Bp_Post_Shortcodes;
			do_action( 'lava_bp_post_init' );
		}

		public static function get_instance( $file ) {
			if( ! self::$instance ) {
				self::$instance = new self( $file );
				self::$instance->register();
			}
			return self::$instance;
		}
	}

endif;

if( !function_exists( 'lava_bpp' ) ) :
	function lava_bpp() {
		$objInstance = Lava_Bp_Post::get_instance( __FILE__ );
		return $objInstance;
	}
	lava_bpp();
endif;