<?php
/**
 * Theme Customizer - Footer
 *
 *
 */

namespace Awps\Api\Customizer;

use WP_Customize_Control;
use WP_Customize_Color_Control;

use Awps\Api\Customizer;

/**
 * Customizer class
 */
class Footer
{
	/**
	 * register default hooks and actions for WordPress
	 * @return
	 */
	public function register( $wp_customize )
	{
		$wp_customize->add_section( 'awps_footer_section' , array(
			'title' => __( 'Footer', 'jvbpd' ),
			'description' => __( 'Customize the Footer', 'jvbpd' ),
			'priority' => 162
		) );

		$wp_customize->add_setting( 'awps_footer_background_color' , array(
			'default' => '#ffffff',
			'transport' => 'postMessage', // or refresh if you want the entire page to reload
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_setting( 'awps_footer_copy_text' , array(
			'default' => 'Proudly powered by AWPS',
			'transport' => 'postMessage', // or refresh if you want the entire page to reload
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'awps_footer_background_color', array(
			'label' => __( 'Background Color', 'jvbpd' ),
			'section' => 'awps_footer_section',
			'settings' => 'awps_footer_background_color',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'awps_footer_copy_text', array(
			'label' => __( 'Copyright Text', 'jvbpd' ),
			'section' => 'awps_footer_section',
			'settings' => 'awps_footer_copy_text',
		) ) );

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial( 'awps_footer_background_color', array(
				'selector' => '#awps-footer-control',
				'render_callback' => array( $this, 'outputCss' ),
				'fallback_refresh' => true
			) );

			$wp_customize->selective_refresh->add_partial( 'awps_footer_copy_text', array(
				'selector' => '#awps-footer-copy-control',
				'render_callback' => array( $this, 'outputText' ),
				'fallback_refresh' => true
			) );
		}
	}

	/**
	 * Generate inline CSS for customizer async reload
	 */
	public function outputCss()
	{
		echo '<style type="text/css">';
			echo Customizer::css( '.site-footer', 'background-color', 'awps_footer_background_color' );
		echo '</style>';
	}

	/**
	 * Generate inline text for customizer async reload
	 */
	public function outputText()
	{
		echo Customizer::text( 'awps_footer_copy_text' );
	}
}