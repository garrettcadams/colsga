<?php
/**
 * Theme Customizer - Sidebar
 *
 *
 */

namespace Awps\Api\Customizer;

use WP_Customize_Color_Control;
use Awps\Api\Customizer;

/**
 * Customizer class
 */
class Sidebar
{
	/**
	 * register default hooks and actions for WordPress
	 * @return
	 */
	public function register( $wp_customize )
	{
		$wp_customize->add_section( 'awps_sidebar_section' , array(
			'title' => __( 'Sidebar', 'jvbpd' ),
			'description' => __( 'Customize the Sidebar', 'jvbpd' ),
			'priority' => 161
		) );

		$wp_customize->add_setting( 'awps_sidebar_background_color' , array(
			'default' => '#ffffff',
			'transport' => 'postMessage', // or refresh if you want the entire page to reload
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'awps_sidebar_background_color', array(
			'label' => __( 'Background Color', 'jvbpd' ),
			'section' => 'awps_sidebar_section',
			'settings' => 'awps_sidebar_background_color',
			'sanitize_callback' => 'sanitize_hex_color',
		) ) );

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial( 'awps_sidebar_background_color', array(
				'selector' => '#awps-sidebar-control',
				'render_callback' => array( $this, 'output' ),
				'fallback_refresh' => true
			) );
		}
	}

	public function sanitize_callback() {

	}

	/**
	 * Generate inline CSS for customizer async reload
	 */
	public function output()
	{
		echo '<style type="text/css">';
			echo Customizer::css( '#sidebar', 'background-color', 'awps_sidebar_background_color' );
		echo '</style>';
	}
}