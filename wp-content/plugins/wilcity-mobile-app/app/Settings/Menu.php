<?php
namespace WILCITY_APP\Settings;

use WilokeListingTools\Register\ListingToolsGeneralConfig;

class Menu {
	protected $slug = 'wilcity-mobile-menu';
	use ListingToolsGeneralConfig;

	public function __construct() {
		add_action('admin_menu', array($this, 'registerMenu'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
	}

	public function registerMenu(){
		add_submenu_page('wiloke-listing-tools', 'Mobile Menu', 'Mobile Menu', 'edit_theme_options', $this->slug, array($this, 'settings'));
	}

	public function settings(){
		include WILCITY_APP_PATH . 'views/menu/index.php';
	}

	public function enqueueScripts($hook){
		if ( strpos($hook, $this->slug) === false ){
			return false;
		}

		$this->requiredScripts();
		$this->generalScripts();
	}
}