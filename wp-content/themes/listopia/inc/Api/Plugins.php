<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Javo
 * @version    2.6.1 for parent theme Javobp for publication on ThemeForest
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */



add_action( 'tgmpa_register', 'jvbpd_register_required_plugins' );
function jvbpd_register_required_plugins() {

	$config = array(
		'id'           => 'jvbpd',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	// $coreName = sprintf( '%s Core', jvbpd_tso()->themeName );
	$coreName = 'Javo Core';

	tgmpa(
		apply_filters( 'jvbpd_tgmpa_plugins', Array(
			Array(
				'name' => $coreName,
				'slug' => sanitize_title( $coreName ),
				'version' => '1.0.2.7',
				'required' => true,
				'force_activation' => false,
				'force_deactivation' => false,
				'external_url' => '',
				'source' => get_template_directory() . '/inc/Plugins/javo-core.zip',
				'image_url' => JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-javo-core.png',
			),
			// Array(
			// 	'name' => 'Revolution Slider',
			// 	'slug' => 'revslider',
			// 	'version' => '6.1.2',
			// 	'required' => true,
			// 	'force_activation' => false,
			// 	'force_deactivation' => false,
			// 	'external_url' => '',
			// 	'source' => get_template_directory() . '/inc/Plugins/revslider.zip',
			// 	'image_url' => JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-revslider.png',
			// ),
			Array(
				'name' => 'The Grid',
				'slug' => 'the-grid',
				'version' => '2.7.3',
				'required' => true,
				'force_activation' => false,
				'force_deactivation' => false,
				'external_url' => '',
				'source' => get_template_directory() . '/inc/Plugins/the-grid.zip',
				'image_url' => JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-javo-the-grid-core-logo.png',
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


			// Lava ajax search
			array(
				'name'						=> 'Lava Directory Manager', // The plugin name
				'slug'						=> 'lava-directory-manager', // The plugin slug (typically the folder name)
				'required'					=> true, // If false, the plugin is only 'recommended' instead of required
				'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'image_url'					=> JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-lava-directory-manager.png',
			),


			// Lava ajax search
			array(
				'name'						=> 'Post Views Counter', // The plugin name
				'slug'						=> 'post-views-counter', // The plugin slug (typically the folder name)
				'required'					=> false, // If false, the plugin is only 'recommended' instead of required
				'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'image_url'					=> 'https://ps.w.org/post-views-counter/assets/icon-256x256.png',
			),

			// Elementor
			array(
				'name'						=> 'Elementor', // The plugin name
				'slug'						=> 'elementor', // The plugin slug (typically the folder name)
				'required'					=> true, // If false, the plugin is only 'recommended' instead of required
				'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'image_url'					=> JVBPD_IMG_DIR . '/icon/jv-default-setting-plugin-elementor.png',
			),
			array(
				'name'						=> 'Contact Form 7', // The plugin name
				'slug'						=> 'contact-form-7', // The plugin slug (typically the folder name)
				'required'					=> true, // If false, the plugin is only 'recommended' instead of required
				'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'image_url'					=> 'https://ps.w.org/contact-form-7/assets/icon-256x256.png',
			),
			array(
				'name'						=> 'WP Super Cache', // The plugin name
				'slug'						=> 'wp-super-cache', // The plugin slug (typically the folder name)
				'required'					=> true, // If false, the plugin is only 'recommended' instead of required
				'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'image_url'					=> 'https://ps.w.org/wp-super-cache/assets/icon-256x256.png',
			),
			array(
				'name'						=> 'MailChimp for WordPress', // The plugin name
				'slug'						=> 'mailchimp-for-wp', // The plugin slug (typically the folder name)
				'required'					=> true, // If false, the plugin is only 'recommended' instead of required
				'force_activation'			=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation'		=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'image_url'					=> 'https://ps.w.org/mailchimp-for-wp/assets/icon-256x256.png',
			),

		) ),
		apply_filters( 'jvbpd_tgmpa_config', $config )
	);
}
