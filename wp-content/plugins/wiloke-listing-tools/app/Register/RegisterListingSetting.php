<?php

namespace WilokeListingTools\Register;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Inc;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\General;

class RegisterListingSetting {
	use ListingToolsGeneralConfig;
	use ParseSection;
	use GetAvailableSections;

	public static $slug = 'wiloke-listing-settings';
	protected $usedSectionsKey = 'wiloke_lt_addlisting_sections';
	protected $aUsedSections = array();
	protected $aAvailableSections = array();
	protected $aReviewSettings = array();
	protected $aListingCardFooter = array();
	protected $aListingHeaderCard = array();

	protected $aAllSections = array();
	protected $designSingleListingsKey = 'wiloke_lt_design_single_listing_tab';
	protected $aDefaultUsedSections;
	protected $isResetDefault=false;
	protected $oPredis;
	protected $aCustomPostTypes;
	protected $aCustomPostTypesKey;
	protected $aSingleNav;
	protected $aSidebarUsedSections;
	protected $aSidebarAvailableSections;
	protected $aSidebarAllSections;
	protected $aSearchUsedFields;
	protected $aAvailableSearchFields;

	protected $aUsedListingCardBody = array();
	protected $aListingCardDefaultBodyKeys = array('google_address', 'phone');

	public static $aHighlightBoxes;

	public function __construct() {
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_init', array($this, 'setDefaultAddListingSettings'));
		add_action('admin_init', array($this, 'setDefaultReviewSettings'));
		// add_action('admin_init', array($this, 'testResetDefault'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wilcity_design_fields_for_listing', array($this, 'saveUsedAddListingSections'));
		add_action('wp_ajax_save_review_settings', array($this, 'reviewSettings'));
		add_action('wp_ajax_wilcity_design_single_nav', array($this, 'saveDesignSingleNav'));
		add_action('wp_ajax_wilcity_design_single_sidebar', array($this, 'saveDesignSidebar'));
		add_action('wp_ajax_wilcity_reset_to_default_sidebar', array($this, 'resetDesignSidebar'));
		add_action('wp_ajax_wilcity_reset_addlisting_settings', array($this, 'resetSettings'));
		add_action('wp_ajax_wilcity_save_highlight_boxes', array($this, 'saveHighlightBoxes'));
		add_action('wp_ajax_wilcity_search_fields', array($this, 'saveSearchFields'));
		add_action('wp_ajax_wilcity_reset_to_default_search_form', array($this, 'resetSearchForm'));

		add_action('wp_ajax_wilcity_hero_search_fields', array($this, 'saveHeroSearchFields'));
		add_action('wp_ajax_wilcity_reset_to_default_hero_search_form', array($this, 'resetHeroSearchFields'));
		add_action('wp_ajax_wilcity_reset_listing_card', array($this, 'resetListingCard'));
		add_action('wp_ajax_wilcity_save_listing_card', array($this, 'saveListingCard'));
		add_action('wp_ajax_wilcity_save_schema_markup', array($this, 'saveSchemaMarkupSettings'));
		add_action('wp_ajax_wilcity_save_schema_markup_reset', array($this, 'resetSchemaMarkupSettings'));
		add_action( 'wp_print_scripts', array($this, 'dequeueScripts'), 100 );

		// Importing
		add_action('wilcity/wiloke-listing-tools/import-demo/setup-search-form', array($this, 'setDefaultSearchFields'));
		add_action('wilcity/wiloke-listing-tools/import-demo/setup-hero-search-form', array($this, 'setupDefaultHeroSearchFormWhenImporting'));
		add_action('wilcity/wiloke-listing-tools/import-demo/setup-sidebar-search-form', array($this, 'setupDefaultSidebarWhileImportingDemo'));
	}

	public function setupDefaultHeroSearchFormWhenImporting($postType){
		$this->setDefaultHeroSearchFields($postType);
	}

	public function setupDefaultSidebarWhileImportingDemo($postType){
		$this->setDefaultSidebarItems($postType);
	}

	public function register(){
		add_submenu_page($this->parentSlug, 'Listing Settings', 'Listing Settings', 'edit_theme_options', self::$slug, array($this, 'settings'));

		$this->parseCustomPostTypes();
		if ( !empty($this->aCustomPostTypes) ){
			foreach ($this->aCustomPostTypes as $menuSlug => $name){
				add_submenu_page($this->parentSlug, $name, $name, 'edit_theme_options', $menuSlug, array($this, 'settings'));
			}
		}
	}

	public function congratulationMsg(){
		return esc_html__('Congratulations! The settings have been reset successfully', 'wiloke-listing-tools');
	}

	protected function getDefaultUsedSections(){
		foreach ($this->aUsedSections as $aSection){
			$this->aDefaultUsedSections[$aSection['key']] = $this->aAllSections[$aSection['key']];
		}
	}

	public function reviewSettings(){
		if ( !current_user_can('edit_theme_options') ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('You do not have permission to access this page', 'wiloke-listing-tools')
				)
			);
		}

		$aData = $_POST['value'];

		SetSettings::setOptions(General::getReviewKey('toggle', $_POST['postType']), $aData['toggle']);
		SetSettings::setOptions(General::getReviewKey('toggle_gallery', $_POST['postType']), $aData['toggle_gallery']);
		SetSettings::setOptions(General::getReviewKey('mode', $_POST['postType']), $aData['mode']);
		SetSettings::setOptions(General::getReviewKey('toggle_review_discussion', $_POST['postType']), $aData['toggle_review_discussion']);
		SetSettings::setOptions(General::getReviewKey('is_immediately_approved', $_POST['postType']), $aData['is_immediately_approved']);

		if ( isset($aData['details']) ){
			foreach ($aData['details'] as $key => $aDetail){
				$aData['details'][$key]['isEditable'] = 'disable';
			}

			SetSettings::setOptions(General::getReviewKey('details', $_POST['postType']), $aData['details']);
		}else{
			SetSettings::setOptions(General::getReviewKey('details', $_POST['postType']), array());
		}

		wp_send_json_success(
			array(
				'msg' => esc_html__('Congratulations! This setting has been changed successfully.', 'wiloke-listing-tools')
			)
		);
	}

	protected function getReviewSettings($postType){
		$this->aReviewSettings['toggle']         = GetSettings::getOptions(General::getReviewKey('toggle', $postType));
		$this->aReviewSettings['toggle_gallery'] = GetSettings::getOptions(General::getReviewKey('toggle_gallery', $postType));
		$this->aReviewSettings['toggle_review_discussion'] = GetSettings::getOptions(General::getReviewKey('toggle_review_discussion', $postType));
		$this->aReviewSettings['mode']           = GetSettings::getOptions(General::getReviewKey('mode', $postType));
		$this->aReviewSettings['details']        = GetSettings::getOptions(General::getReviewKey('details', $postType));
		$this->aReviewSettings['is_immediately_approved'] = GetSettings::getOptions(General::getReviewKey('is_immediately_approved', $postType));

		$this->aReviewSettings['toggle'] = empty($this->aReviewSettings['toggle']) ? 'disable' : $this->aReviewSettings['toggle'];
		$this->aReviewSettings['toggle_gallery'] = empty($this->aReviewSettings['toggle_gallery']) ? 'disable' : $this->aReviewSettings['toggle_gallery'];
		$this->aReviewSettings['toggle_review_discussion'] = empty($this->aReviewSettings['toggle_review_discussion']) ? 'disable' : $this->aReviewSettings['toggle_review_discussion'];

		$this->aReviewSettings['is_immediately_approved'] = empty($this->aReviewSettings['is_immediately_approved']) ? 'yes' : $this->aReviewSettings['is_immediately_approved'];

		if ( empty($this->aReviewSettings['mode']) ){
			$this->aReviewSettings['mode'] = 5;
			SetSettings::setOptions(General::getReviewKey('mode', $postType), 5);
		}

		if ( !$this->aReviewSettings['details'] ){
			$this->aReviewSettings['details'] = array(
				array(
					'name' => 'Overall',
					'key'  => 'overall',
					'isEditable' => 'disable'
				)
			);
		}
	}

	protected function getDefaultField($aField){
		if ( $aField['type'] != 'group' ){
			$aValue['value'] = $aField['value'];
			$aValue['type']  = $aField['type'];
			return $aValue;
		}else{
			$aValues = array_map(array($this, 'getDefaultFields'), array($aField['fields']));
			return $aValues[0];
		}
	}

	public function getDefaultFields($aFields){
		$aFieldValues = array();
		foreach ($aFields as $aField){
			$aFieldValues[$aField['key']] = $this->getDefaultField($aField);
		}
		return $aFieldValues;
	}

	protected function setDefaultSidebarItems($postType=''){
		$aSidebarItems = wilokeListingToolsRepository()->get('listing-settings:sidebar_settings', true)->sub('items');
		$postType = empty($postType) ? General::detectPostType() : $postType;
		$postType = $this->getPostType($postType);

		SetSettings::setOptions(General::getSingleListingSettingKey('sidebar', $postType), $aSidebarItems);
		return $aSidebarItems;
	}

	protected function getAllSidebarSections() {
		if ( !empty($this->aSidebarAllSections) ){
			return $this->aSidebarAllSections;
		}

		$this->aSidebarAllSections = wilokeListingToolsRepository()->get('listing-settings:sidebar_settings', true)->sub('items');
		$this->aSidebarAllSections = apply_filters('wiloke-listing-tools/single/sidebar-items', $this->aSidebarAllSections);
	}

	protected function getUsedSidebarSections(){
		$postType = General::detectPostType();
		$postType = $this->getPostType($postType);

		$this->aSidebarUsedSections = GetSettings::getOptions(General::getSingleListingSettingKey('sidebar', $postType));

		if ( empty($this->aSidebarUsedSections) ){
			$this->aSidebarUsedSections = $this->setDefaultSidebarItems();
		}
	}

	protected function getAvailableSidebarSections() {
		$this->getAllSidebarSections();
		$this->getUsedSidebarSections();

		if ( !empty($this->aSidebarAvailableSections) ){
			return $this->aSidebarAvailableSections;
		}

		$aAllSectionsKey = array_keys($this->aSidebarAllSections);

		if ( empty($this->aSidebarUsedSections) ){
			$this->aSidebarAvailableSections = $aAllSectionsKey;
		}else{
			$aUsedSectionsKey = array_keys($this->aSidebarUsedSections);
			foreach ($aAllSectionsKey as $sectionKey){
				if ( !in_array($sectionKey, $aUsedSectionsKey) ){
					$this->aSidebarAvailableSections[$sectionKey] = $this->aSidebarAllSections[$sectionKey];
				}
			}
		}

		$aCustomSection = array(
			'customSection' => array(
				'isCustomSection' => 'yes',
				'icon' => '',
				'key'  => uniqid('custom_section_'),
				'name' => 'Custom Section',
				'content' => ''
			)
		);

		// Finding Sections that can be used more than 1 time
		$aSpecialSections = array_filter($this->aSidebarAllSections, function($aSection){
			return isset($aSection['isMultipleSections']) && $aSection['isMultipleSections'] == 'yes' && !isset($this->aSidebarAvailableSections['key']);
		});

		$this->aSidebarAvailableSections = empty($this->aSidebarAvailableSections) ? $aCustomSection : $this->aSidebarAvailableSections + $aCustomSection;

		if ( !empty($aSpecialSections) ){
			$this->aSidebarAvailableSections = $this->aSidebarAvailableSections + $aSpecialSections;
		}
	}

	protected function getUsedSections(){
		if ( !$this->isResetDefault ){
			$this->aUsedSections = GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:usedSectionKey'));
		}
		if ( !empty($this->aUsedSections) ){
			return true;
		}
		$this->aAllSections  = wilokeListingToolsRepository()->get('settings:allSections');
		if ( empty($this->aUsedSections) ){
			$order = 0;
			foreach ($this->aAllSections as $sectionKey => $aSection) {
				if ( isset($aSection['isDefault']) && $aSection['isDefault'] ){
					$this->aUsedSections[$order] = $this->parseSection($aSection);
					$order++;
				}
			}
		}

		return true;
	}

	public function resetDesignSidebar(){
		$this->validateAjaxPermission();
		$this->setDefaultSidebarItems($_POST['postType']);
		wp_send_json_success(array(
			'msg' => esc_html__('The settings have been reset successfully', 'wiloke-listing-tools')
		));
	}

	public function saveDesignSidebar(){
		$this->validateAjaxPermission();
		$aSidebarItems = array();
		foreach ($_POST['data'] as $aItem){
			if ( $aItem['key'] == 'promotion' ){
				$key = isset($aItem['promotionID']) && !empty($aItem['promotionID']) ? $aItem['key'] . '_' . $aItem['promotionID'] : $aItem['key'] . '_' . uniqid();
			}else if ( isset($aItem['isMultipleSections']) && $aItem['isMultipleSections'] ){
				$key = $aItem['key'] . '_' .  uniqid();
			}else{
				$key = $aItem['key'];
			}
			$aSidebarItems[$key] = $aItem;
		}

		SetSettings::setOptions(General::getSingleListingSettingKey('sidebar', $_POST['postType']), $aSidebarItems);
		$msg = $this->congratulationMsg();
		wp_send_json_success(array('msg'=>$msg));
	}

	public function saveDesignSingleNav(){
		$this->validateAjaxPermission();

		if ( isset($_POST['isReset']) && $_POST['isReset'] == 'yes' ){
			$this->setSingleContentDefault();
			$msg = $this->congratulationMsg();
		}else{
			$aNavOrder = array();

			foreach ($_POST['data'] as $aItem){
				$aNavOrder[$aItem['key']] = $aItem;
			}

			SetSettings::setOptions(General::getSingleListingSettingKey('navigation', $_POST['postType']), $aNavOrder);
			$msg = $this->congratulationMsg();
		}

		wp_send_json_success(array(
			'msg' => $msg
		));
	}

	protected function setSingleContentDefault(){
		$aDefault = wilokeListingToolsRepository()->get('listing-settings:navigation', true)->sub('draggable');
		$postType = General::detectPostType();
		$postType = $this->getPostType($postType);
		SetSettings::setOptions(General::getSingleListingSettingKey('navigation', $postType), $aDefault);
	}

	public function setDefaultReviewSettings(){
		$postType = General::detectPostType();
		$postType = $this->getPostType($postType);

		if ( !empty(GetSettings::getOptions(General::getReviewKey('details', $postType))) ){
			return false;
		}

		SetSettings::setOptions(General::getReviewKey('details', $postType), array(
			array(
				'name' => 'Overall',
				'key'  => 'overall',
				'isEditable' => 'disable'
			)
		));
	}

	public function setDefaultAddListingSettings(){
		$postType = General::detectPostType();
		$postType = $this->getPostType($postType);

		if ( empty(GetSettings::getOptions(General::getSingleListingSettingKey('navigation', $postType))) ){
			$this->setSingleContentDefault();
		}

		if ( !$this->isResetDefault ){
			$aAddListingSettings = GetSettings::getOptions(General::getUsedSectionKey($postType, true));
			if ( empty($aAddListingSettings) || !is_array($aAddListingSettings) ){
				$this->setDefaultAddListing($postType);
			}
		}
	}

	public function setDefaultSearchFields($postType=''){
		if ( empty($postType) ){
			if ( wp_doing_ajax() ){
				$postType = $_POST['postType'];
			}else{
				$postType = $this->getPostType($postType);
			}
		}

		$aDefaults = wilokeListingToolsRepository()->get('listing-settings:searchFields');
		$aUsedFields = array();
		foreach ($aDefaults as $key => $aField){
			if ( isset($aField['isDefault']) && !$this->isRemoveField($aField, $postType) ){
				$aUsedFields[$key] = $aField;
			}
		}
		SetSettings::setOptions(General::getSearchFieldsKey($postType), $aUsedFields);
	}

	protected function getSingleNavigation(){
		$postType = General::detectPostType();
		$postType = $this->getPostType($postType);
		$aDefault = wilokeListingToolsRepository()->get('listing-settings:navigation', true)->sub('draggable');
		$aNavSettings = GetSettings::getOptions(General::getSingleListingSettingKey('navigation', $postType));

		$this->aSingleNav = !empty($aNavSettings) && is_array($aNavSettings) ? $aNavSettings + $aDefault : $aDefault;
	}

	protected function getAvailableSearchFields(){
		$postType = General::detectPostType();
		$postType = $this->getPostType($postType);

		$this->getSearchFields($postType);
		$aDefault = wilokeListingToolsRepository()->get('listing-settings:searchFields');

		foreach ($aDefault as $key => $aDefaultField){
			if ( $this->isRemoveField($aDefaultField, $postType)  ){
				unset($aDefault[$key]);
			}
		}

		if ( empty($this->aSearchUsedFields) ){
			$this->aAvailableSearchFields = $aDefault;
		}else{
			$aUsedSearchFieldKeys = array_map(function($aField){
				return $aField['key'];
			}, $this->aSearchUsedFields);

			foreach ($aDefault as $aField){
				if ( isset($aField['isCustomField']) || (!in_array($aField['key'], $aUsedSearchFieldKeys)) ){
					$this->aAvailableSearchFields[] = $aField;
				}
			}
		}

		$this->aAvailableSearchFields = empty($this->aAvailableSearchFields) ? array() : $this->aAvailableSearchFields;
	}

	protected function getSearchFields($postType=''){
		if ( $postType ){
			$postType = General::detectPostType();
			$postType = $this->getPostType($postType);
		}

		$this->aSearchUsedFields = GetSettings::getOptions(General::getSearchFieldsKey($postType));
		if ( empty($this->aSearchUsedFields) || !is_array($this->aSearchUsedFields) ){
			$this->setDefaultSearchFields();
		}
	}

	public function testResetDefault(){
		$this->setDefaultAddListing('listing');
	}

	protected function setDefaultAddListing($postType){
		$aAllListings = wilokeListingToolsRepository()->get('settings:allSections');
		$order = 0;
		$aUsedSections = array();
		foreach ($aAllListings as $sectionKey => $aSection) {
			if ( isset($aSection['isDefault']) && $aSection['isDefault'] ){
				if ( $postType != 'event' && $sectionKey == 'listing_title' ){
					continue;
				}
				$aUsedSections[$order] = $this->parseSection($aSection);
				$order++;
			}
		}
		SetSettings::setOptions(General::getUsedSectionKey($postType, true), $aUsedSections);
	}

	public function resetSettings(){
		$this->validateAjaxPermission();
		$this->isResetDefault = true;
		$this->setDefaultAddListing($_POST['postType']);
        SetSettings::setOptions(General::getUsedSectionSavedAt($_POST['postType'], true), current_time('timestamp', true));

		wp_send_json_success(
			array(
				'msg' => esc_html__('Congrats! This setting has been reset successfully', 'wiloke-design-addlisting')
			)
		);
	}

	public function saveUsedAddListingSections(){
		$this->validateAjaxPermission();

		$aValues = $_POST['results'];

		SetSettings::setOptions(General::getUsedSectionKey($_POST['postType'], true), $aValues);
        SetSettings::setOptions(General::getUsedSectionSavedAt($_POST['postType'], true), current_time('timestamp', true));
		wp_send_json_success(
			array(
				'msg' => $this->congratulationMsg()
			)
		);
	}

	public function saveHighlightBoxes(){
		$this->validateAjaxPermission();
		SetSettings::setOptions(General::getSingleListingSettingKey('highlightBoxes', $_POST['postType']), $_POST['data']);
		wp_send_json_success(
			array(
				'msg' => $this->congratulationMsg()
			)
		);
	}

	public function saveSearchFields(){
		$this->validateAjaxPermission();
		SetSettings::setOptions(General::getSearchFieldsKey($_POST['postType']), $_POST['data']);
		SetSettings::setOptions(General::mainSearchFormSavedAtKey($_POST['postType']), current_time('timestamp', 1));

		wp_send_json_success(
			array(
				'msg' => 'Congratulations! Your search form has been designed successfully'
			)
		);
	}

	public function resetSearchForm(){
		$this->validateAjaxPermission();
		$this->setDefaultSearchFields();
		wp_send_json_success(
			array(
				'msg' => $this->congratulationMsg()
			)
		);
	}

	public static function getHighlightBoxes($postType){
		if ( !empty(self::$aHighlightBoxes) ){
			return self::$aHighlightBoxes;
		}

		self::$aHighlightBoxes = GetSettings::getOptions(General::getSingleListingSettingKey('highlightBoxes', $postType));

		if ( empty(self::$aHighlightBoxes) || !is_array(self::$aHighlightBoxes) ){
			self::$aHighlightBoxes = array();
			self::$aHighlightBoxes['aItems'] = array();
			self::$aHighlightBoxes['isEnable'] = 'no';
			self::$aHighlightBoxes['itemsPerRow'] = 'col-md-4 col-lg-4';
		}else{
			if ( !isset(self::$aHighlightBoxes['aItems']) ){
				self::$aHighlightBoxes['aItems'] = array();
			}
		}

		self::$aHighlightBoxes['ajaxAction'] = 'wilcity_save_highlight_boxes';

		return self::$aHighlightBoxes;
	}

	private function setDefaultBodyListingCard($postType){
		$aDefaultCard = wilokeListingToolsRepository()->get('listing-settings:listingCard', true)->sub('aBodyItems');
		$aDefault = array_filter($aDefaultCard, function ($aItem){
			return in_array($aItem['key'], $this->aListingCardDefaultBodyKeys);
		});
		SetSettings::setOptions(General::getSingleListingSettingKey('card', $postType), $aDefault);

		$aFooterSettings = wilokeListingToolsRepository()->get('listing-settings:listingCard', true)->sub('aFooter');
		SetSettings::setOptions(General::getSingleListingSettingKey('footer_card', $postType), $aFooterSettings);
		return $aDefault;
	}

	public function resetListingCard(){
		$this->validateAjaxPermission();
		$aDefault = $this->setDefaultBodyListingCard($_POST['postType']);
		wp_send_json_success(array('msg' => 'Congratulations! The Listing Card settings have been reset successfully', 'aData'=>$aDefault));
	}

	public function saveListingCard(){
		$this->validateAjaxPermission();
		if ( !empty($_POST['aBodyUsedFields']) ){
			SetSettings::setOptions(General::getSingleListingSettingKey('card', $_POST['postType']), $_POST['aBodyUsedFields']);
		}

		if ( !empty($_POST['aFooterSettings']) ){
			SetSettings::setOptions(General::getSingleListingSettingKey('footer_card', $_POST['postType']), $_POST['aFooterSettings']);
		}

		if ( !empty($_POST['aHeaderSettings']) ){
			SetSettings::setOptions(General::getSingleListingSettingKey('header_card', $_POST['postType']), $_POST['aHeaderSettings']);
		}

		wp_send_json_success(array('msg' => 'Congratulations! The Listing Card settings have been saved successfully'));
	}

	public function saveSchemaMarkupSettings(){
		$this->validateAjaxPermission();
		if ( isJson($_POST['data']) ){
			SetSettings::setOptions(General::getSchemaMarkupKey($_POST['postType']), json_encode($_POST['data']));
		}else{
			SetSettings::setOptions(General::getSchemaMarkupKey($_POST['postType']), array());
		}

		SetSettings::setOptions(General::getSchemaMarkupSavedAtKey($_POST['postType']), current_time('timestamp', 1));
		wp_send_json_success(array('msg' => 'Congratulations! The Schema Markup Setting has been saved successfully'));
	}

	public function resetSchemaMarkupSettings(){
		$settings = $this->setDefaultSchemaMarkup($_POST['postType']);

		wp_send_json_success(array(
			'msg' => 'The Schema Markup has been reset successfully. Please re-fresh the browser to update the setting area.',
			'settings' => $settings
		));
	}

	private function getUsedBodyListingCard(){
		$postType = General::detectPostType();
		$postType = $this->getPostType($postType);

		$this->aUsedListingCardBody = GetSettings::getOptions(General::getSingleListingSettingKey('card', $postType));

		if ( empty($this->aUsedListingCardBody) ){
			$this->aUsedListingCardBody = $this->setDefaultBodyListingCard($postType);
		}
	}

	private function getKeyTypeRelationship(){
		$aFieldTypeOptions = wilokeListingToolsRepository()->get('listing-settings:listingCard', true)->sub('aBodyTypeFields');
		return array_map(function($aOption){
			return array($aOption['name']=>$aOption['key']);
		}, $aFieldTypeOptions);
	}

	public function enqueueScripts($hook){
		if ( !$this->matchedSlug($hook) ){
			return false;
		}

		$postType = $this->getPostType($hook);
		$this->requiredScripts();

		$this->getSearchFields($postType);
		$this->getAvailableSearchFields();
		$this->getAvailableHeroSearchFields();
		$this->getUsedBodyListingCard();

		wp_enqueue_script('design-directory-type', WILOKE_LISTING_TOOL_URL . 'admin/source/js/script.js', array('jquery', 'vuejs'), WILOKE_LISTING_TOOL_VERSION, true);

		$this->aUsedSections = GetSettings::getOptions(General::getUsedSectionKey($postType));

		$this->aUsedSections = !empty($this->aUsedSections) ? self::unSlashDeep($this->aUsedSections) : array();
		$this->aAllSections  = wilokeListingToolsRepository()->get('settings:allSections');
		$this->mergeNewFieldToUsedSection($postType);

		self::getHighlightBoxes($postType);
		$this->getSingleNavigation();
		$this->getAvailableSections();
		$this->getReviewSettings($postType);
		$this->schemaMarkup();

		$this->aSingleNav = !empty($this->aSingleNav) ? self::unSlashDeep($this->aSingleNav) : array();


		$this->aSearchUsedFields = !empty($this->aSearchUsedFields) ? self::unSlashDeep($this->aSearchUsedFields) : array();
		$this->aUsedHeroSearchFields = !empty($this->aUsedHeroSearchFields) ? self::unSlashDeep($this->aUsedHeroSearchFields) : array();
		$this->aReviewSettings = !empty($this->aReviewSettings) ? self::unSlashDeep($this->aReviewSettings) : array();
		self::$aHighlightBoxes = !empty(self::$aHighlightBoxes) ? self::unSlashDeep(self::$aHighlightBoxes) : array();

		$this->getAvailableSidebarSections();
		$this->aSidebarUsedSections = !empty($this->aSidebarUsedSections) ? self::unSlashDeep($this->aSidebarUsedSections) : array();

		$this->aListingCardFooter = GetSettings::getOptions(General::getSingleListingSettingKey('footer_card', $postType));

		if ( empty($this->aListingCardFooter) ){
			$this->aListingCardFooter['taxonomy'] = 'listing_cat';
		}
		$this->aListingHeaderCard = GetSettings::getOptions(General::getSingleListingSettingKey('header_card', $postType));

		if ( empty($this->aListingHeaderCard) ){
			$this->aListingHeaderCard = array(
				'btnAction' => 'total_views'
			);
		}

		wp_localize_script('design-directory-type', 'WILOKE_LISTING_TOOLS',
			array(
				'postType'   => $postType,
				'addListing' => array(
					'usedSections'          => $this->aUsedSections,
					'allSections'           => $this->aAllSections,
					'availableSections'     => array_values($this->aAvailableSections),
					'ajaxAction'            => 'wilcity_design_fields_for_listing'
				),
				'reviewSettings'        => $this->aReviewSettings,
				'aSingleNavigation'      => array(
					'aSections'     => $this->aSingleNav,
					'ajaxAction'    => 'wilcity_design_single_nav'
				),
				'aSidebar'  => array(
					'aUsedSections'         => $this->aSidebarUsedSections,
					'aAllSections'          => $this->aSidebarAllSections,
					'aAvailableSections'    => $this->aSidebarAvailableSections,
					'aStyles'               => wilokeListingToolsRepository()->get('listing-settings:sidebar_settings', true)->sub('aStyles'),
					'aRelatedBy'            => wilokeListingToolsRepository()->get('listing-settings:sidebar_settings', true)->sub('aRelatedBy'),
					'aOrderBy'              => wilokeListingToolsRepository()->get('general:aOrderBy'),
					'aOrderFallbackBy'      => wilokeListingToolsRepository()->get('general:aOrderByFallback'),
					'ajaxAction'            => 'wilcity_design_single_sidebar'
				),
				'aSearchForm'  => array(
					'aUsedFields'     => $this->aSearchUsedFields,
					'aAllFields'      => wilokeListingToolsRepository()->get('listing-settings:searchFields'),
					'aAvailableFields'=> empty($this->aAvailableSearchFields) ? array() : $this->aAvailableSearchFields,
					'ajaxAction' => 'wilcity_search_fields'
				),
				'aHeroSearchForm' => array(
					'aUsedFields'     => $this->aUsedHeroSearchFields,
					'aAllFields'      => $this->getAllHeroSearchFields(),
					'aAvailableFields'=> empty($this->aAvailableHeroSearchFields) ? array() : $this->aAvailableHeroSearchFields,
					'ajaxAction' => 'wilcity_hero_search_fields'
				),
				'aBoxes' => self::$aHighlightBoxes,
				'aListingCard' => array(
					'aSettings' => array(
						'aHeaderButtonInfoOptions' => wilokeListingToolsRepository()->get('listing-settings:listingCard', true)->sub('aButtonInfoOptions'),
						'aHeader' => $this->aListingHeaderCard,
						'aBody' => array(
							'aAvailableFields'  => wilokeListingToolsRepository()->get('listing-settings:listingCard', true)->sub('aBodyItems'),
							'aUsedFields'       => $this->aUsedListingCardBody,
							'aTypeOptions'      => wilokeListingToolsRepository()->get('listing-settings:listingCard', true)->sub('aBodyTypeFields'),
							'aTypeKeyRelationship' => $this->getKeyTypeRelationship()
						),
						'aFooter' => $this->aListingCardFooter
					),
					'ajaxAction' => 'wilcity_save_listing_card'
				),
				'aSchemaMarkup' => array(
					'aSettings' => $this->getSchemaMarkup($postType),
					'ajaxAction' => 'wilcity_save_schema_markup'
				)
			)
		);

		$this->designSingleNav();
		$this->designHighlightBoxes();
		$this->designSidebar();
		$this->designSearchForm();
		$this->designHeroSearchForm();
		$this->designFieldsScript();
		$this->generalScripts();
		$this->enqueueListingCard();
	}

	public function settings(){
		Inc::file('listing-settings:index');
	}
}
