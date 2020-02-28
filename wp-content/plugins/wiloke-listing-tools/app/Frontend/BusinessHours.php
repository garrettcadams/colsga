<?php

namespace WilokeListingTools\Frontend;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\MetaBoxes\Listing;

class BusinessHours {
	public static $aTimezones;
	public static $aUTCs;

	public static function isEnableBusinessHour($post=null){
		if ( !empty($post) ){
			$planID = GetSettings::getListingBelongsToPlan($post->ID);
			if ( !empty($planID) && get_post_status($planID) == 'publish' && get_post_type($planID) == 'listing_plan' ){
				$aPlanSettings = GetSettings::getPlanSettings($planID);
				if ( isset($aPlanSettings['toggle_business_hours']) && $aPlanSettings['toggle_business_hours'] == 'disable' ){
					return false;
				}
			}
			$hourMode = GetSettings::getPostMeta($post->ID, 'hourMode');
			if ( !$hourMode || $hourMode == 'no_hours_available' ){
				return false;
			}
			return apply_filters('wilcity/is_enable_business_hour', true);
		}
		return false;
	}

	public static function getTimezone($post){
		if ( isset(self::$aTimezones[$post->ID]) ){
			return self::$aTimezones[$post->ID];
		}

		$individualTimeFormat = GetSettings::getPostMeta($post->ID, 'timezone');
		if ( !empty($individualTimeFormat) ){
			self::$aTimezones[$post->ID] = GetSettings::getPostMeta($post->ID, 'timezone');
		}else{
			self::$aTimezones[$post->ID] = get_option('timezone_string');
		}

		return self::$aTimezones[$post->ID];
	}

	public static function getListingUTC($post){
		if ( isset(self::$aUTCs[$post->ID]) ){
			return self::$aUTCs[$post->ID];
		}

		$timezone = self::getTimezone($post);
		return Time::findUTCOffsetByTimezoneID($timezone);
	}

	public static function getTodayKey($post){
		$timezone = self::getTimezone($post);
		if ( !empty($timezone) ){
			date_default_timezone_set($timezone);
		}

		return strtolower(date('l', time()));
	}

	public static function getTodayBusinessHours($post){
		$todayKey = self::getTodayKey($post);
		return Listing::getBusinessHoursOfDay($post->ID, $todayKey);
	}

	public static function isSecondHourExists($aTodayBusinessHour){
		if ( empty($aTodayBusinessHour['secondOpenHour']) || empty($aTodayBusinessHour['secondCloseHour']) || $aTodayBusinessHour['secondOpenHour'] == $aTodayBusinessHour['secondCloseHour'] ){
			return false;
		}

		return true;
	}

	public static function invalidFirstHours($aTodayBusinessHour){
		if ( empty($aTodayBusinessHour['firstOpenHour']) || empty($aTodayBusinessHour['firstCloseHour']) || $aTodayBusinessHour['firstOpenHour'] == $aTodayBusinessHour['firstCloseHour'] ){
			return true;
		}

		return false;
	}

	public static function getCurrentBusinessHourStatus($post, $aTodayBusinessHour=array()){
		$openNow    = __('Open now', 'wiloke-listing-tools');
		$closed     = __('Closed', 'wiloke-listing-tools');
		$hourMode = GetSettings::getPostMeta($post->ID, 'hourMode');

		if ( empty($aTodayBusinessHour) ){
			if ( $hourMode == 'always_open' ){
				return array(
					'status' => 'open',
					'class'  => 'color-secondary',
					'text'   => $openNow
				);
			}
			$aTodayBusinessHour = self::getTodayBusinessHours($post);
		}

		if ( $aTodayBusinessHour['isOpen'] == 'no' ){
			return array(
				'status' => 'day_off',
				'class'  => 'color-secondary',
				'text'   => esc_html__('Day Off', 'wiloke-listing-tools')
			);
		}

		$timezone = self::getTimezone($post);

		if ( !empty($timezone) ){
			date_default_timezone_set($timezone);
		}

		$oNewDateTime = new \DateTime();
		$oCurrentHour = $oNewDateTime->setTimestamp(time());
		$oFirstStarts = $oNewDateTime->createFromFormat('H:i:s', $aTodayBusinessHour['firstOpenHour']);
		$oFirstClosed = $oNewDateTime->createFromFormat('H:i:s', $aTodayBusinessHour['firstCloseHour']);

		if ( $oCurrentHour >= $oFirstStarts && $oCurrentHour <= $oFirstClosed ){
			return array(
				'status' => 'open',
				'class'  => 'color-secondary',
				'text'   => $openNow
			);
		}

		$oLastOpen = $oFirstStarts;
		$oLastEnd = $oFirstClosed;

		if ( self::isSecondHourExists($aTodayBusinessHour) ){
			$oSecondStarts = $oNewDateTime->createFromFormat('H:i:s', $aTodayBusinessHour['secondOpenHour']);
			$oSecondClosed = $oNewDateTime->createFromFormat('H:i:s', $aTodayBusinessHour['secondCloseHour']);

			if ( $oCurrentHour >= $oSecondStarts && $oSecondClosed > $oCurrentHour ){
				return array(
					'status' => 'open',
					'class'  => 'color-secondary',
					'text'   => $openNow
				);
			}

			$oLastEnd   = $oSecondClosed;
			$oLastOpen  = $oSecondStarts;
		}

		if ( ($oLastOpen > $oLastEnd) && (($oCurrentHour > $oLastOpen) || ($oCurrentHour < $oLastEnd)) ){
			return array(
				'status' => 'open',
				'class'  => 'color-secondary',
				'text'   => $openNow
			);
		}

		return array(
			'status' => 'close',
			'class'  => 'color-quaternary',
			'text'   => $closed
		);
	}
}