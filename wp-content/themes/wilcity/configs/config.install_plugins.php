<?php
/**
 * @param Array(key=>val)
 * key: key of aConfigs
 * val: a part of file name: config.val.php
 */
return array(
	array(
		'name'               => esc_html__('Redux Framework', 'wilcity'),
		'slug'               => 'redux-framework', // The plugin slug (typically the folder name).
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('Cmb2', 'wilcity'),
		'slug'               => 'cmb2', // The plugin slug (typically the folder name).
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Wiloke Listing Tools',
		'slug'               => 'wiloke-listing-tools', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/wiloke-listing-tools.zip', // The plugin source.
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Wilcity Widgets',
		'slug'               => 'wilcity-widgets', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/wilcity-widgets.zip', // The plugin source.
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Wilcity Shortcodes',
		'slug'               => 'wilcity-shortcodes', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/wilcity-shortcodes.zip', // The plugin source.
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Wilcity WPBakery Addon',
		'slug'               => 'wilcity-wpbakery-addon', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/wilcity-wpbakery-addon.zip', //
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Wilcity Paid Claim',
		'slug'               => 'wilcity-paid-claim', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/wilcity-paid-claim.zip', //
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Wilcity Elementor Addon',
		'slug'               => 'wilcity-elementor-addon', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/wilcity-elementor-addon.zip', //
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Wilcity Mobile App',
		'slug'               => 'wilcity-mobile-app', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/wilcity-mobile-app.zip', //
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name' => 'Booking.com Banner Creator',
		'slug' => 'bookingcom-banner-creator',
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Wilcity Import',
		'slug'               => 'wilcity-import', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/wilcity-import.zip', //
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Contact Form 7',
		'slug'               => 'contact-form-7', // The plugin slug (typically the folder name).
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Email Templates',
		'slug'               => 'email-templates', // The plugin slug (typically the folder name).
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('King Composer', 'wilcity'),
		'slug'               => 'kingcomposer', // The plugin slug (typically the folder name).
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('Visual Composer', 'wilcity'),
		'slug'               => 'jscomposer', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/js_composer.zip', //
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Elementor',
		'slug'               => 'elementor', // The plugin slug (typically the folder name).
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'WooCommerce',
		'slug'               => 'woocommerce', // The plugin slug (typically the folder name).
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => 'Dokan Multivendor Marketplace',
		'slug'               => 'dokan-lite', // The plugin slug (typically the folder name).
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	)
);