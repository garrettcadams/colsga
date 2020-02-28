<?php

namespace WilokeListingTools\Register;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Inc;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\General;

class RegisterMobileMenu {
	protected $slug = 'wilcity-mobile-menu';
	use ListingToolsGeneralConfig;
	private static $mainMenuKey = 'mobile_main_menu';
	private static $secondaryMenuKey = 'mobile_secondary_menu';
	private $sKeyWord = '';
	private $aStacks = array(
		'home'   => 'homeStack',
		'event'  => 'eventStack',
		'account'  => 'accountStack',
		'menu'   => 'menuStack',
		'rest'   => 'listingStack',
		'page'   => 'pageStack',
		'posts' => 'blogStack'
	);
	private static $aMainMenuSettings = array();
	private static $aAvailableMainMenuSettings = array();

	protected static $aSecondaryMenu = array(
		array(
			'key'       => 'home',
			'name'      => 'Home',
			'iconName'  => 'home',
			'screen'    => 'homeStack'
		)
	);

	protected static $aDefaultMainMenu = array(
		array(
			'key'       => 'home',
			'name'      => 'Home',
			'iconName'  => 'home',
			'screen'    => 'homeStack',
			'status'    => 'enable'
		),
		array(
			'key'       => 'listing',
			'name'      => 'Listing',
			'iconName'  => 'map-pin',
			'screen'    => 'listingStack',
			'status'    => 'enable'
		),
		array(
			'key'       => 'event',
			'name'      => 'Event',
			'iconName'  => 'calendar',
			'screen'    => 'eventStack',
			'status'    => 'enable'
		),
		array(
			'key'       => 'account',
			'name'      => 'Profile',
			'iconName'  => 'user',
			'screen'    => 'accountStack',
			'status'    => 'enable'
		),
		array(
			'key'       => 'menu',
			'name'      => 'Secondary Menu',
			'iconName'  => 'three-line',
			'screen'    => 'menuStack',
			'status'    => 'enable'
		)
	);

	public function __construct() {
		add_action('admin_menu', array($this, 'registerMenu'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wilcity_get_page_id', array($this, 'getPageID'));
		add_action('wp_ajax_wilcity_get_directory_key', array($this, 'getDirectoryKey'));
		add_action('wp_ajax_wilcity_get_listing_directory_key', array($this, 'getListingDirectoryKey'));
		add_action('wp_ajax_wilcity_save_main_mobile_menu_settings', array($this, 'saveMobileMenuSettings'));
		add_action('wp_ajax_wilcity_save_secondary_menu_settings', array($this, 'saveSecondaryMenuSettings'));
	}

	private function saveSecondary($aData){
		if ( !current_user_can('edit_theme_options') || empty($aData) ){
			return false;
		}

		$aValues = array();
		foreach ($aData as $aSegment){
			$aFieldSettings = array();
			foreach ($aSegment['aFields'] as $aField){
				$aFieldSettings[$aField['name']] = !is_array($aField['value']) ? sanitize_text_field($aField['value']) : $aField['value'];
			}
			$aFieldSettings['screen'] = $aSegment['oGeneral']['screen'];
			$aValues[] = $aFieldSettings;
		}
		SetSettings::setOptions(self::$secondaryMenuKey, $aValues);
		return true;
	}

	public function saveSecondaryMenuSettings(){
		$status = $this->saveSecondary($_POST['data']);
		if ( $status ){
			wp_send_json_success(array(
				'msg' => 'Congratulations! The Main Apps Menu has been setup successfully!'
			));
		}else{
			wp_send_json_error(array(
				'msg' => 'Oos! You do not have permission to access this page or the data is emptied now.'
			));
		}
	}

	private function saveMainMenu($aData){
		if ( !current_user_can('edit_theme_options') || empty($aData) ){
			return false;
		}
		$aValues = array();
		foreach ($aData as $aSegment){
			$aFieldSettings = array();
			foreach ($aSegment['aFields'] as $aField){
				$aFieldSettings[$aField['name']] = !is_array($aField['value']) ? sanitize_text_field($aField['value']) : $aField['value'];
			}
			if ( isset($this->aStacks[$aFieldSettings['key']])  ){
				$aFieldSettings['screen'] = $this->aStacks[$aFieldSettings['key']];
			}else{
				$aFieldSettings['screen'] = 'listingStack';
			}

			$aValues[] = $aFieldSettings;
		}
		SetSettings::setOptions(self::$mainMenuKey, $aValues);
		return true;
	}

	public function saveMobileMenuSettings(){
		$status = $this->saveMainMenu($_POST['data']);
		if ( $status ){
			wp_send_json_success(array(
				'msg' => 'Congratulations! The Main Apps Menu has been setup successfully!'
			));
		}else{
			wp_send_json_success(array(
				'msg' => 'Oos! You do not have permission to access this page or the data is emptied now.'
			));
		}
	}

	public function getPageID(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error();
		}
		global $wpdb;

		$this->sKeyWord = isset($_GET['s']) ? $wpdb->_real_escape(strtolower($_GET['s'])) : '';

		$query = new \WP_Query(array(
			'post_type' => 'page',
			'posts_per_page' => 20,
			'post_status' => 'publish',
			's' => $this->sKeyWord
		));
		if ( !$query->have_posts() ){
			wp_send_json_error();
		}

		$aResponse = array();
		while ($query->have_posts()){
			$query->the_post();
			$aResponse[] = array(
				'name'  => $query->post->post_title,
				'value' => $query->post->ID
			);
		}
		echo json_encode(array(
			'results' => $aResponse
		));die();
	}

	public function getDirectoryKey(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error();
		}
		global $wpdb;

		$this->sKeyWord = isset($_GET['s']) ? $wpdb->_real_escape(strtolower($_GET['s'])) : '';

		$aAllDirectoryPostTypes = General::getPostTypeKeys(false, false);

		if ( !empty($this->sKeyWord) ){
			$aMatchedDirectoryTypes = array_filter($aAllDirectoryPostTypes, function($type){
				return strpos($type, $this->sKeyWord) !== false;
			});
		}else{
			$aMatchedDirectoryTypes = $aAllDirectoryPostTypes;
		}

		$aResponse = array();
		foreach ($aMatchedDirectoryTypes as $postType){
			$aResponse[] = array(
				'name'  => $postType,
				'value' => $postType
			);
		}

		echo json_encode(array(
			'results' => $aResponse
		));die();
	}

	public function getListingDirectoryKey(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error();
		}
		global $wpdb;

		$this->sKeyWord = isset($_GET['s']) ? $wpdb->_real_escape(strtolower($_GET['s'])) : '';
		$aAllDirectoryPostTypes = General::getPostTypeKeys(false, false);

		if ( !empty($this->sKeyWord) ){
			$aMatchedDirectoryTypes = array_filter($aAllDirectoryPostTypes, function($type){
				return strpos($type, $this->sKeyWord) !== false;
			});
		}else{
			$aMatchedDirectoryTypes = $aAllDirectoryPostTypes;
		}

		$aResponse = array();
		foreach ($aMatchedDirectoryTypes as $postType){
			$aResponse[] = array(
				'name'  => $postType,
				'value' => $postType
			);
		}

		echo json_encode(array(
			'results' => $aResponse
		));die();
	}

	public function registerMenu(){
		if ( !defined('WILCITY_MOBILE_APP') ){
			return false;
		}

		add_submenu_page('wiloke-listing-tools', 'Mobile Menu', 'Mobile Menu', 'edit_theme_options', $this->slug, array($this, 'settings'));
	}

	protected static function getSecondaryMenuItems(){
		return wilokeListingToolsRepository()->get('mobile-menus:aSecondaryMenu');
	}

	protected static function parseSecondaryMenuSettings(){
		$aOptions = self::getSecondaryUsedFields();
		$aConfigurations = wilokeListingToolsRepository()->get('mobile-menus:aSecondaryMenu');
		$aSettings = array();
		foreach ($aOptions as $aOption){
			if ( !isset($aConfigurations[$aOption['screen']]) ){
				continue;
			}
			$aSegment = $aConfigurations[$aOption['screen']];
			foreach ($aSegment['aFields'] as $order => $aField){
				if ( isset($aOption[$aField['name']]) ){
					$aSegment['aFields'][$order]['value'] = $aOption[$aField['name']];
				}
			}
			$aSegment['oGeneral']['heading'] = $aOption['name'];
			$aSettings[] = $aSegment;

		}
		return $aSettings;
	}

	protected static function getSecondaryUsedFields(){
		$aOptions = GetSettings::getOptions(self::$secondaryMenuKey);
		if ( empty($aOptions) || !is_array($aOptions) ){
			SetSettings::setOptions(self::$secondaryMenuKey, self::$aSecondaryMenu);
			return self::$aSecondaryMenu;
		}

		return $aOptions;
	}

	public static function getMainMenuSettings(){
		$aOptions = GetSettings::getOptions(self::$mainMenuKey);
		if ( empty($aOptions) || !is_array($aOptions) ){
			SetSettings::setOptions(self::$mainMenuKey, self::$aDefaultMainMenu);
			$aOptions = self::$aDefaultMainMenu;
		}
		return $aOptions;
	}

	private static function parseMainMenuSettings(){
		$aOptions = self::getMainMenuSettings();
		$aConfigurations = wilokeListingToolsRepository()->get('mobile-menus:aMainMenu');
		$aSettings = array();
		foreach ($aOptions as $aOption){
			if ( !isset($aConfigurations[$aOption['screen']]) ){
				continue;
			}
			$aSegment = $aConfigurations[$aOption['screen']];
			foreach ($aSegment['aFields'] as $order => $aField){
				if ( isset($aOption[$aField['name']]) ){
					$aSegment['aFields'][$order]['value'] = $aOption[$aField['name']];
				}
			}
			$aSettings[] = $aSegment;
		}
		return $aSettings;
	}

	private static function getAvailableMainMenuSettings(){
		$aConfigurations = wilokeListingToolsRepository()->get('mobile-menus:aMainMenu');
		if ( empty(self::$aMainMenuSettings) ){
			self::$aAvailableMainMenuSettings = $aConfigurations;
			return self::$aAvailableMainMenuSettings;
		}

		$aUsedKeys = array_map(function($aField){
			return $aField['oGeneral']['key'];
		}, self::$aMainMenuSettings);

		foreach ($aConfigurations as $key => $aSetting){
			if ( !in_array($key, $aUsedKeys) || (isset($aSetting['oGeneral']['canClone']) && $aSetting['oGeneral']['canClone'] == 'yes') ){
				self::$aAvailableMainMenuSettings[] = $aSetting;
			}
		}
		return self::$aAvailableMainMenuSettings;
	}

	public function settings(){
		Inc::file('mobile-menu:index');
	}

	public function enqueueScripts($hook){
		if ( strpos($hook, $this->slug) === false ){
			return false;
		}
		self::$aMainMenuSettings = self::parseMainMenuSettings();
		$aAvailableMainMenuItems = self::getAvailableMainMenuSettings();
		$aSecondaryMenuItems     = self::getSecondaryMenuItems();
		$aAvailableMenuItems = array_values($aSecondaryMenuItems);
		$aUsedMenuItems      = self::parseSecondaryMenuSettings();
		$this->requiredScripts();
		$this->draggable();
		$this->generalScripts();

		wp_register_script('wilcity-mobile-menu', WILOKE_LISTING_TOOL_URL  . 'admin/source/js/mobile-menu.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);
		wp_enqueue_script('wilcity-mobile-menu');
		wp_localize_script('wilcity-mobile-menu', 'WILOKE_MAIN_MOBILE_MENU', self::$aMainMenuSettings);
		wp_localize_script('wilcity-mobile-menu', 'WILOKE_MAIN_MENU_AVAILABLE_MENU', $aAvailableMainMenuItems);
		wp_localize_script('wilcity-mobile-menu', 'WILOKE_SECONDARY_MENU_ITEMS', $aSecondaryMenuItems);
		wp_localize_script('wilcity-mobile-menu', 'WILOKE_SECONDARY_AVAILABLE_MENU_ITEMS', $aAvailableMenuItems);
		wp_localize_script('wilcity-mobile-menu', 'WILCITY_SECONDARY_USED_MENU_ITEMS', $aUsedMenuItems);
	}
}