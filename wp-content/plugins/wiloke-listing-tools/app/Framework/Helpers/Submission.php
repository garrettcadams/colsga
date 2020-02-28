<?php

namespace WilokeListingTools\Framework\Helpers;


class Submission {
	protected static $aSupportedPostTypes;
	protected static $aAddListingPostTypes;
	public static function detectPostStatus(){
		if ( GetWilokeSubmission::getField('approved_method') == 'manual_review' ){
			$newStatus = 'pending';
		}else{
			$newStatus = 'publish';
		}

		return $newStatus;
	}

	public static function listingStatusWillPublishImmediately($postStatus){
		return !empty($postStatus) && in_array($postStatus, array('expired', 'publish'));
	}

	public static function getAddListingPostTypeKeys(){
		self::getSupportedPostTypes();
		$aPlans = GetSettings::getFrontendPostTypes(true);

		foreach ($aPlans as $key => $postType){
			if ( !in_array($postType, self::$aSupportedPostTypes) ){
				unset($aPlans[$key]);
			}
		}

		return $aPlans;
	}

	public static function getSupportedPostTypes(){
		if ( !empty(self::$aSupportedPostTypes) ){
			return self::$aSupportedPostTypes;
		}
		$aPostTypesInfo = GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));

		if ( empty($aPostTypesInfo) ){
			return false;
		}

		$aEventGeneralSetting = GetSettings::getOptions(wilokeListingToolsRepository()->get('event-settings:keys', true)->sub('general'));

		if ( isset($aEventGeneralSetting['toggle_event']) && $aEventGeneralSetting['toggle_event'] == 'disable' ){
			$aPostTypesInfo = array_filter($aPostTypesInfo, function($aPostType){
				if ( $aPostType['key'] == 'event' ){
					return false;
				}
				return true;
			});
		}

		$aPostTypesInfo = array_filter($aPostTypesInfo, function($aPostType){
			if ( isset($aPostType['isDisableOnFrontend']) && $aPostType['isDisableOnFrontend'] == 'yes' ){
				return false;
			}

			return true;
		});

		self::$aSupportedPostTypes = array_map(function($aData){
			return $aData['key'];
		}, $aPostTypesInfo);


		return self::$aSupportedPostTypes;
	}

	public static function getListingPostTypes(){
		if ( !empty(self::$aAddListingPostTypes) ){
			return self::$aAddListingPostTypes;
		}
		$aPostTypesInfo = GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));

		if ( empty($aPostTypesInfo) ){
			return false;
		}

		$aEventGeneralSetting = GetSettings::getOptions(wilokeListingToolsRepository()->get('event-settings:keys', true)->sub('general'));

		if ( isset($aEventGeneralSetting['toggle_event']) && $aEventGeneralSetting['toggle_event'] == 'disable' ){
			$aPostTypesInfo = array_filter($aPostTypesInfo, function($aPostType){
				if ( $aPostType['key'] == 'event' ){
					return false;
				}
				return true;
			});
		}
;
		self::$aAddListingPostTypes = array_map(function($aData){
			return $aData['key'];
		}, $aPostTypesInfo);


		return self::$aAddListingPostTypes;
	}
}