<?php

namespace WilokeListingTools\Register;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Inc;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\General;

class RegisterEventSettings {
	use ListingToolsGeneralConfig;
	use ParseSection;
	use GetAvailableSections;

	public static $slug = 'wiloke-event-settings';
	public static $forPostType = 'event';
	protected $aGeneralSettings;
	protected $aEventContent;
	protected $aUsedSections;
	protected $aAllSections;
	protected $aAvailableSections;
	protected $aSearchUsedFields;
	protected $aAvailableSearchFields;
	public $aExcludeSearchFields = array('price_range', 'best_rated');

	public function __construct() {
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wiloke_save_event_general_settings', array($this, 'saveGeneralSettings'));
		add_action('wp_ajax_wilcity_design_fields_for_event', array($this, 'saveUsedAddListingSections'));
		add_action('wp_ajax_wilcity_reset_event_settings', array($this, 'resetDefaultFields'));
		add_action('admin_init', array($this, 'setDefault'));
		add_action('wp_ajax_wlt_search_event_key', array($this, 'searchKey'));
		add_action('wp_ajax_wlt_save_event_content', array($this, 'saveContent'));
	}

	public function congratulationMsg(){
		return esc_html__('Congratulations! The settings have been reset successfully', 'wiloke-listing-tools');
	}

	public function setDefaultSearchFields(){
		$data = wilokeListingToolsRepository()->get('event-settings:default-fields');
		$aData = json_decode(stripslashes($data), true);
		SetSettings::setOptions( wilokeListingToolsRepository()->get('event-settings:designFields', true)->sub('usedSectionKey'), $aData['settings'] );
	}

	protected function getSearchFields($postType=''){
		$this->aSearchUsedFields = GetSettings::getOptions(General::getSearchFieldsKey(self::$forPostType));
		if ( empty($this->aSearchUsedFields) || !is_array($this->aSearchUsedFields) ){
			$this->setDefaultSearchFields();
		}
	}

	protected function getAvailableSearchFields(){
		$this->getSearchFields(self::$forPostType);
		$aDefault = wilokeListingToolsRepository()->get('listing-settings:searchFields');

		foreach ($aDefault as $key => $aDefaultField){
			if ( $this->isRemoveField($aDefaultField, self::$forPostType)  ){
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

	public function saveContent(){
		$this->validateAjaxPermission();
		SetSettings::setOptions('event_content_fields', $_POST['fields']);
        SetSettings::setOptions(General::getUsedSectionSavedAt('event', true), current_time('timestamp', true));

		wp_send_json_success(array(
			'msg' => 'Congratulations! Your settings have been updated'
		));
	}

	public function searchKey(){
		$aFields = GetSettings::getOptions(wilokeListingToolsRepository()->get('event-settings:designFields', true)->sub('usedSectionKey'));
		$aData = array();

		foreach ($aFields as $aContent){
			if ( in_array($aContent['key'], array('event_calendar', 'listing_address', 'event_belongs_to_listing', 'contact_info', 'single_price', 'price_range', 'listing_title')) ){
				continue;
			}

			if ( (isset($aContent['isCustomSection']) && $aContent['isCustomSection'] == 'yes') || ($aContent['type'] == 'group') ){
				$value = $aContent['key'].'|'.$aContent['type'];
			}else{
				$value = $aContent['key'];
			}

			$aData[] = array(
				'name' => $aContent['heading'],
				'value'=> $value,
				'text' => $aContent['heading'],
				'isCustomSection' => isset($aContent['isCustomSection']) ? $aContent['isCustomSection'] : 'no'
			);
		}
		echo json_encode(array(
			'success' => true,
			'results' => $aData
		));
		die();
	}

	public function getAllSections(){
		$this->aAllSections  = wilokeListingToolsRepository()->get('settings:allSections');
		$this->aAllSections  = array_merge($this->aAllSections, array(
			'event_calendar'  => array(
				'isDefault' => true,
				'type'      => 'event_calendar',
				'key'       => 'event_calendar',
				'icon'      => 'la la-certificate',
				'heading'	=> 'Event Calendar',
				'fields'    => array(
					array(
						'heading' 	=> 'Settings',
						'type' 		=> 'event_calendar',
						'desc'      => '',
						'key' 		=> 'event_calendar',
						'fields'    => array(
							array(
								'heading'   => 'Label Name',
								'type' 		=> 'text',
								'desc'      => '',
								'key'       => 'label',
								'label'     => 'Frequency'
							),
							array(
								'heading' 		=> 'Is Required?',
								'type' 			=> 'checkbox',
								'desc'          => '',
								'key'           => 'isRequired',
								'isRequired' 	=> 'yes'
							)
						)
					)
				)
			),
			'hosted_by'  => array(
				'isDefault' => true,
				'type'      => 'hosted_by',
				'key'       => 'hosted_by',
				'icon'      => 'la la-user',
				'heading'	=> 'Event Hosted By',
				'fields'    => array(
					array(
						'heading' 	=> 'Host',
						'type' 		=> 'text',
						'desc'      => '',
						'key' 		=> 'hosted_by',
						'fields'    => array(
							array(
								'heading'   => 'Label Name',
								'type' 		=> 'text',
								'desc'      => '',
								'key'       => 'label',
								'label'     => 'Host'
							)
						)
					),
					array(
						'heading' 	=> 'Profile URL',
						'type' 		=> 'text',
						'desc'      => '',
						'key' 		=> 'hosted_by_profile_url',
						'fields'    => array(
							array(
								'heading'   => 'Label Name',
								'type' 		=> 'text',
								'desc'      => '',
								'key'       => 'label',
								'label'     => 'Profile URL'
							)
						)
					)
				)
			),
			'event_belongs_to_listing'  => array(
				'isDefault' => true,
				'type'      => 'event_belongs_to_listing',
				'key'       => 'event_belongs_to_listing',
				'icon'      => 'la la-certificate',
				'heading'	=> esc_html__('Event Belongs To', 'wiloke-listing-tools'),
				'fields'    => array(
					array(
						'heading' 	=> esc_html__('Settings', 'wiloke-listing-tools'),
						'type' 		=> 'select2',
						'desc'      => '',
						'key' 		=> 'event_belongs_to_listing',
						'fields'    => array(
							array(
								'heading'   => esc_html__('Label Name', 'wiloke-listing-tools'),
								'type' 		=> 'text',
								'desc'      => '',
								'key'       => 'label',
								'label'     => 'Listing Parent'
							),
							array(
								'heading' 		=> esc_html__('Is Required?', 'wiloke-listing-tools'),
								'type' 			=> 'checkbox',
								'desc'          => '',
								'key'           => 'isRequired',
								'isRequired' 	=> 'no'
							),
							array(
								'heading' 		=> '',
								'type' 			=> 'hidden',
								'desc'          => '',
								'key'           => 'ajaxAction',
								'ajaxAction'    => 'wilcity_fetch_post'
							),
							array(
								'heading' 		=> 'Specify Parent Directory Types',
								'type' 			=> 'text',
								'desc'          => 'Each Directory Type is separated by a comma. Eg: listing,education. You can find the Directory Type under Wiloke Tools -> Add Directory Type',
								'key'           => 'eventParents',
								'eventParents'  => 'listing'
							),
							array(
								'heading' 		=> '',
								'type' 			=> 'hidden',
								'desc'          => '',
								'key'           => 'isAjax',
								'isAjax' 	    => 'yes'
							)
						)
					)
				)
			)
		));

		unset($this->aAllSections['header']);
		unset($this->aAllSections['business_hours']);
		unset($this->aAllSections['date_time']);
		unset($this->aAllSections['image']);
	}

	public function setDefault(){
		$this->aEventContent = GetSettings::getOptions('event_content_fields');
		if ( empty($this->aEventContent) || !is_array($this->aEventContent) ){
			SetSettings::setOptions('event_content_fields', array(array(
				'name' => 'Description',
				'key'  => 'listing_content',
				'icon' => 'la la-file-text'
			)));
		}
	}

	public function getEventContent(){
		$this->aEventContent = GetSettings::getOptions('event_content_fields');
		if ( empty($this->aEventContent) || !is_array($this->aEventContent) ){
			$this->aEventContent = array(array(
				'name' => 'Description',
				'key'  => 'listing_content',
				'icon' => 'la la-file-text'
			));
		}
	}

	public function saveUsedAddListingSections(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('You do not have permission to access this page', 'wiloke-design-addlisting')
				)
			);
		}

		$aValues = $_POST['results'];
		SetSettings::setOptions(wilokeListingToolsRepository()->get('event-settings:designFields', true)->sub('usedSectionKey'), $aValues);
        SetSettings::setOptions(General::getUsedSectionSavedAt('event', true), current_time('timestamp', true));

		wp_send_json_success(
			array(
				'msg' => 'Congrats! Your settings have been updated'
			)
		);
	}

	public function resetDefaultFields(){
		$this->setFieldDefaults(true);
        SetSettings::setOptions(General::getUsedSectionSavedAt('event', true), current_time('timestamp', true));

		wp_send_json_success(
			array(
				'msg' => 'Congrats! The settings have been reset successfully.'
			)
		);
	}

	private function setFieldDefaults($isFocus=false){
		if ( !$isFocus ){
			if ( !isset($_GET['page']) || $_GET['page'] !== 'wiloke-event-settings' ){
				return false;
			}

			$aUsedSections = GetSettings::getOptions(wilokeListingToolsRepository()->get('event-settings:designFields', true)->sub('usedSectionKey'), true);

			if ( !empty($aUsedSections) && is_array($aUsedSections) ){
				return $aUsedSections;
			}
		}

		$this->getAllSections();

		$aListingTitle = $this->aAllSections['listing_title']['fields'][0];
		$aRawDefaultFields['listing_title'] = $this->aAllSections['listing_title'];
		unset($aRawDefaultFields['listing_title']['fields'][0]);
		$aRawDefaultFields['listing_title']['fields']['listing_title'] = $aListingTitle;

		$aImage = $this->aAllSections['featured_image']['fields'][0];
		$aRawDefaultFields['featured_image'] = $this->aAllSections['featured_image'];
		unset($aRawDefaultFields['featured_image']['fields'][0]);
		$aRawDefaultFields['featured_image']['fields']['featured_image'] = $aImage;

		$aEventCalendar = $this->aAllSections['event_calendar']['fields'][0];
		$aRawDefaultFields['event_calendar'] = $this->aAllSections['event_calendar'];
		unset($aRawDefaultFields['event_calendar']['fields'][0]);
		$aRawDefaultFields['event_calendar']['fields']['event_calendar'] = $aEventCalendar;

		$aDefaultFields[] = $aRawDefaultFields['listing_title'];
		$aDefaultFields[] = $aRawDefaultFields['featured_image'];
		$aDefaultFields[] = $aRawDefaultFields['event_calendar'];

		unset($aRawDefaultFields['listing_title']);
		unset($aRawDefaultFields['featured_image']);
		unset($aRawDefaultFields['event_calendar']);

		SetSettings::setOptions(wilokeListingToolsRepository()->get('event-settings:designFields', true)->sub('usedSectionKey'), $aDefaultFields);

		return $aDefaultFields;
	}

	public function saveGeneralSettings(){
		if ( !current_user_can('edit_theme_options') ){
			wp_send_json_error();
		}

		$aOptions = array();
		foreach ($_POST['settings'] as $key => $val){
			$aOptions[sanitize_text_field($key)] = sanitize_text_field($val);
		}

		SetSettings::setOptions(wilokeListingToolsRepository()->get('event-settings:keys', true)->sub('general'), $aOptions);
		wp_send_json_success();
	}

	public function setupValue(){
		$this->aGeneralSettings = GetSettings::getOptions('event_general_settings');

		$isSetDef = empty($this->aGeneralSettings);

		$this->aGeneralSettings = wp_parse_args(
			$this->aGeneralSettings,
			wilokeListingToolsRepository()->get('event-settings', true)->sub('general')
		);

		if ( $isSetDef ){
			SetSettings::setOptions(wilokeListingToolsRepository()->get('event-settings:keys', true)->sub('general'), $this->aGeneralSettings);
		}
	}

	protected function addRequiredSections(){
		foreach ($this->aUsedSections as $key => $aSection){
			if ( $aSection['type'] == 'event_calendar' || $aSection['type'] == 'listing_address' ){
				$this->aUsedSections[$key]['isNotDeleteAble'] = true;
			}
		}
	}

	public function enqueueScripts($hook){
		if ( strpos($hook, self::$slug) === false ){
			return false;
		}

		$this->requiredScripts();
		$this->getAvailableSearchFields();
		$this->getAvailableHeroSearchFields();
		$this->setupValue();
		$this->getAllSections();
		$this->aUsedSections = $this->setFieldDefaults();
		$this->mergeNewFieldToUsedSection('event');
		$this->addRequiredSections();
		$this->getAvailableSections();
		$this->getEventContent();

		wp_enqueue_script('wiloke-event-script', WILOKE_LISTING_TOOL_URL . 'admin/source/js/event-script.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);
		wp_localize_script('wiloke-event-script', 'WILOKE_EVENT_GENERAL_SETTINGS', $this->aGeneralSettings);
		wp_localize_script('wiloke-event-script', 'WILOKE_EVENT_CONTENT', $this->aEventContent);

		wp_localize_script('wiloke-event-script', 'WILOKE_LISTING_TOOLS',
			array(
				'postType' => self::$forPostType,
				'addListing' => array(
					'usedSections'          => $this->aUsedSections,
					'allSections'           => $this->aAllSections,
					'availableSections'     => array_values($this->aAvailableSections),
					'postType'              => 'event',
					'ajaxAction'            => 'wilcity_design_fields_for_event'
				),
				'aSearchForm'  => array(
					'aUsedFields'     => $this->aSearchUsedFields,
					'aAllFields'      => wilokeListingToolsRepository()->get('listing-settings:searchFields'),
					'aAvailableFields'=> $this->aAvailableSearchFields,
					'ajaxAction' => 'wilcity_search_fields'
				),
				'aHeroSearchForm' => array(
					'aUsedFields'     => $this->aUsedHeroSearchFields,
					'aAllFields'      => $this->getAllHeroSearchFields(),
					'aAvailableFields'=> $this->aAvailableHeroSearchFields,
					'ajaxAction' => 'wilcity_hero_search_fields'
				),
				'aSchemaMarkup' => array(
					'aSettings' => $this->getSchemaMarkup('event'),
					'ajaxAction' => 'wilcity_save_schema_markup'
				)
			)
		);

		$this->generalScripts();
		$this->schemaMarkup();
		$this->designFieldsScript();
		$this->designHeroSearchForm();
		$this->designSearchForm();
	}

	public function settings(){
		Inc::file('event-settings:index');
	}

	public function register(){
		add_submenu_page($this->parentSlug, 'Event Settings', 'Event Settings', 'edit_theme_options', self::$slug, array($this, 'settings'));
	}
}
