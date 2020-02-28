<?php

namespace WilokeListingTools\Register;


use Stripe\Util\Set;
use WilokeListingTools\Framework\Helpers\GetSettings;
use \WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\SetSettings;

class RegisterImportExportWilokeTools {
	use ListingToolsGeneralConfig;
	use GetAvailableSections;
	use ParseSection;
	private $settingType;
	private $postType;

	public function __construct() {
		add_action('wilcity/wiloke-listing-tools/wiloke-tools-settings', array($this, 'aImportExportSettings'), 10, 2);
		add_action('admin_enqueue_scripts', array($this, 'enqueueImportExportScripts'));
		add_action('wp_ajax_wilcity_export_wiloke_tools', array($this, 'exportWilokeToolSettings'));
		add_action('wp_ajax_wilcity_import_wiloke_tools', array($this, 'importWilokeToolSettings'));
	}

	public function exportWilokeToolSettings(){
		$aSettings = array();
		$postType = sanitize_text_field($_POST['postType']);
		$settingType = sanitize_text_field($_POST['settingType']);
		$postType = str_replace('_settings', '', $postType);

		if ( $postType == 'event' && ($settingType == 'fields' || $settingType == 'content') ){
			switch ($settingType){
				case 'fields':
					$aSettings = GetSettings::getOptions(wilokeListingToolsRepository()->get('event-settings:designFields', true)->sub('usedSectionKey'), true);
					break;
				case 'content':
					$aSettings = GetSettings::getOptions('event_content_fields');
					break;
			}
		}else{
			switch ($settingType){
				case 'addlisting':
					$aSettings = GetSettings::getOptions(General::getUsedSectionKey($postType), true);
					break;
				case 'listing-card':
					$aSettings['aBodyUsedFields'] = GetSettings::getOptions(General::getSingleListingSettingKey('card', $postType), true);
					$aSettings['aFooterSettings'] = GetSettings::getOptions(General::getSingleListingSettingKey('footer_card', $postType), true);
					$aSettings['aHeaderSettings'] = GetSettings::getOptions(General::getSingleListingSettingKey('header_card', $postType), true);
					break;
				case 'reviews':
					$aSettings['details'] = GetSettings::getOptions(General::getReviewKey('details', $postType), true);
					$aSettings['toggle'] = GetSettings::getOptions(General::getReviewKey('toggle', $postType), true);
					$aSettings['toggle_gallery'] = GetSettings::getOptions(General::getReviewKey('toggle_gallery', $postType), true);
					$aSettings['mode'] = GetSettings::getOptions(General::getReviewKey('mode', $postType), true);
					$aSettings['toggle_review_discussion'] = GetSettings::getOptions(General::getReviewKey('toggle_review_discussion', $postType), true);
					$aSettings['is_immediately_approved'] = GetSettings::getOptions(General::getReviewKey('is_immediately_approved', $postType), true);
					break;
				case 'single-highlightbox':
					$aSettings['settings'] = GetSettings::getOptions(General::getSingleListingSettingKey('highlightBoxes', $postType), true);
					break;
				case 'single-nav':
					$aSettings['settings'] = GetSettings::getOptions(General::getSingleListingSettingKey('navigation', $postType), true);
					break;
				case 'single-sidebar':
					$aSettings['settings'] = GetSettings::getOptions(General::getSingleListingSettingKey('sidebar', $postType), true);
					break;
				case 'search-form':
					$aSettings['settings'] = GetSettings::getOptions(General::getSearchFieldsKey($postType), true);
					break;
				case 'hero-search-form':
					$aSettings['settings'] = GetSettings::getOptions(General::getHeroSearchFieldsKey($postType), true);
					break;
				case 'schema-markup':
					$aSettings['settings'] = GetSettings::getOptions(General::getSchemaMarkupKey($postType), true);
					break;
			}
		}

		wp_send_json_success(array('msg'=>json_encode(array(
			'settings'      => $aSettings,
			'postType'      => $postType,
			'settingType'   => $settingType
		))));
	}

	public function importWilokeToolSettings(){
		$postType = sanitize_text_field($_POST['postType']);
		$postType = str_replace('_settings', '', $postType);
		$settingType = sanitize_text_field($_POST['settingType']);
		$aData = json_decode(stripslashes($_POST['data']), true);

		if ( $settingType !== $aData['settingType']  ){
			wp_send_json_success(array('msg'=>sprintf('The import data must the same current type setting. Export Data Type:%s. Current Type: %s', $aData['settingType'], $settingType)));
		}

		if ( $postType == 'event' && ( in_array($settingType, array('fields', 'content')) ) ){
			switch ($settingType) {
				case 'fields':
					SetSettings::setOptions( wilokeListingToolsRepository()->get('event-settings:designFields', true)->sub('usedSectionKey'), $aData['settings'] );
					break;
				case 'content':
					SetSettings::setOptions('event_content_fields', $aData['settings']);
					break;
			}
		}else{
			switch ($settingType){
				case 'addlisting':
					SetSettings::setOptions(General::getUsedSectionKey($postType), $aData['settings']);
					break;
				case 'listing-card':
					if ( isset($aData['settings']['aBodyUsedFields']) ){
						SetSettings::setOptions(General::getSingleListingSettingKey('card', $postType), $aData['settings']['aBodyUsedFields']);
					}

					if ( isset($aData['settings']['aFooterSettings']) ){
						SetSettings::setOptions(General::getSingleListingSettingKey('footer_card', $postType), $aData['settings']['aFooterSettings']);
					}

					if ( isset($aData['settings']['aHeaderSettings']) ){
						SetSettings::setOptions(General::getSingleListingSettingKey('header_card', $postType), $aData['settings']['aHeaderSettings']);
					}
					break;
				case 'reviews':
					SetSettings::setOptions(General::getReviewKey('details', $postType), $aData['settings']['details']);
					SetSettings::setOptions(General::getReviewKey('toggle', $postType), $aData['settings']['toggle']);
					SetSettings::setOptions(General::getReviewKey('toggle_gallery', $postType), $aData['settings']['toggle_gallery']);
					SetSettings::setOptions(General::getReviewKey('mode', $postType), $aData['settings']['mode']);
					SetSettings::setOptions(General::getReviewKey('toggle_review_discussion', $postType), $aData['settings']['toggle_review_discussion']);
					SetSettings::setOptions(General::getReviewKey('is_immediately_approved', $postType), $aData['settings']['is_immediately_approved']);
					break;
				case 'single-highlightbox':
					SetSettings::setOptions(General::getSingleListingSettingKey('highlightBoxes', $postType), $aData['settings']['settings']);
					break;
				case 'single-nav':
					SetSettings::setOptions(General::getSingleListingSettingKey('navigation', $postType), $aData['settings']['settings']);
					break;
				case 'single-sidebar':
					SetSettings::setOptions(General::getSingleListingSettingKey('sidebar', $postType), $aData['settings']['settings']);
					break;
				case 'search-form':
					SetSettings::setOptions(General::getSearchFieldsKey($postType), $aData['settings']['settings']);
					SetSettings::setOptions(General::mainSearchFormSavedAtKey($postType), current_time('timestamp', 1));
					break;
				case 'hero-search-form':
					SetSettings::setOptions(General::getHeroSearchFieldsKey($postType), $aData['settings']['settings']);
					SetSettings::setOptions(General::heroSearchFormSavedAt($postType), current_time('timestamp', 1));
					break;
				case 'schema-markup':
					SetSettings::setOptions(General::getSchemaMarkupKey($postType), $aData['settings']['settings']);
					SetSettings::setOptions(General::getSchemaMarkupSavedAtKey($postType), current_time('timestamp', 1));
					break;
			}
		}

		wp_send_json_success(array('msg'=>'The data has been imported. Please refresh website to update the new settings'));
	}

	public function aImportExportSettings($postType, $settingType){
		include WILOKE_LISTING_TOOL_DIR . 'views/import-export-wiloke-tools/index.php';
	}

	public function enqueueImportExportScripts(){
		wp_enqueue_script('wiloke-tools-import-export', plugin_dir_url(__FILE__) . '../../admin/source/js/import-export-wiloke-tools.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);
	}
}