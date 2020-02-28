<?php
/**
 * @package JavoCore
 */
namespace Jvbpd\Base;

use Jvbpd\Base\BaseController;

/**
*
*/
class Enqueue extends BaseController
{
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 9 );
		add_action( 'wp_enqueue_scripts', array( $this, 'style_css_enqueue' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ), 9 );

		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'add_fonts' ) );
	}

	public function enqueue() {
		// enqueue all our scripts
		//wp_enqueue_script( 'media-upload' );
		//wp_enqueue_media();

		//wp_enqueue_script( 'jvcore-mainfest', $this->dist_url . 'js/manifest.js', array(), '1.0.0', true );
		//wp_enqueue_script( 'jvcore-vendor', $this->dist_url . 'js/vendor.js', array(), '1.0.0', true );
		wp_enqueue_script( 'jvcore-main', $this->dist_url . 'js/main.js', array(), '1.0', true );
		wp_enqueue_style( 'jvcore-bp', $this->dist_url . 'css/admin.css' );
	}

	public function style_css_enqueue() {
		wp_enqueue_style( 'jvcore-style', $this->dist_url . 'css/style.css', Array('main') );
		wp_add_inline_style( 'jvcore-style', jvbpd_tso()->get( 'custom_css') );
	}

	public function admin_enqueue() {
		wp_enqueue_style( 'jvcore-admin-style', $this->dist_url . 'css/admin.css' );
		//wp_enqueue_script( 'jvcore-main', $this->dist_url . 'js/main.js', array(), '1.0', true );
	}

	public function add_fonts() {
		wp_enqueue_style( 'jvcore-elementor-editor', $this->dist_url . 'css/admin.css' );
	}
}