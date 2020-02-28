<?php
/**
 * WilokeInstallPlugins Class
 *
 * @category plugins
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
	exit;
}
class WilokeInstallPlugins
{
	public $aSelfConfig = array(
		'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => ''
	);

	public function __construct()
	{
		$this->run();
	}

	/**
	 * Install Plugins
	 */
	public function run()
	{
		global $wiloke;
		if ( isset($wiloke->aConfigs['install_plugins']) && current_user_can( 'activate_plugins' ) )
		{
			require_once WILOKE_INC_DIR . 'lib/tgm-plugin/class-tgm-plugin-activation.php';
			add_action('tgmpa_register', array($this, 'register_plugins'));
		}
	}

	public function register_plugins()
	{
		global $wiloke;
		tgmpa( $wiloke->aConfigs['install_plugins'], $this->aSelfConfig );
	}
}