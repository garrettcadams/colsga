<?php
namespace WILCITY_APP\SidebarOnApp;

use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\BusinessHours as BH;
use WilokeListingTools\Framework\Helpers\Time;

class BusinessHours {
	public function __construct() {
		add_filter('wilcity/mobile/sidebar/business_hours', array($this, 'render'), 10, 2);
	}

	public function render($post, $aAtts){
		$hourMode = GetSettings::getPostMeta($post->ID, 'hourMode');

		if ( $hourMode == 'no_hours_available' ){
			return json_encode(array(
				'mode' => 'no_hours_available'
			));
		}else if ( $hourMode == 'always_open' ){
			return json_encode(array(
				'mode' => 'always_open'
			));
		}else{
			$aBusinessHours = GetSettings::getBusinessHours($post->ID);

			if ( empty($aBusinessHours) ){
				return false;
			}

			$aResponse['mode'] = 'rest';

			$timeFormat = GetSettings::getPostMeta($post->ID, 'timeFormat');
			$aDefineDaysOfWeek = wilcityShortcodesRepository()->get('config:aDaysOfWeek');
			$aTodayBusinessHours = BH::getTodayBusinessHours($post);
			$isInvalidFirstHour  = BH::invalidFirstHours($aTodayBusinessHours);

			if ( $aTodayBusinessHours['isOpen'] == 'no' || $isInvalidFirstHour ){
				$aResponse['oCurrent'] = array(
					'status' => 'day_off',
					'is'     => $aDefineDaysOfWeek[BH::getTodayKey($post)],
					'text'   => esc_html__('Day Off', WILCITY_MOBILE_APP)
				);
			}else{
				$aBusinessStatus = BH::getCurrentBusinessHourStatus($post, $aTodayBusinessHours);
				$aResponse['oCurrent'] = $aBusinessStatus;
				$aResponse['oCurrent']['is'] = $aDefineDaysOfWeek[BH::getTodayKey($post)];

				$aResponse['oCurrent']['firstOpenHour'] = Time::renderTime($aTodayBusinessHours['firstOpenHour'], $timeFormat);
				$aResponse['oCurrent']['firstCloseHour'] = Time::renderTime($aTodayBusinessHours['firstCloseHour'], $timeFormat);

				if ( BH::isSecondHourExists($aTodayBusinessHours)  ){
					$aResponse['oCurrent']['secondOpenHour'] = Time::renderTime($aTodayBusinessHours['secondOpenHour'], $timeFormat);
					$aResponse['oCurrent']['secondCloseHour'] = Time::renderTime($aTodayBusinessHours['secondCloseHour'], $timeFormat);
				}
			}

			foreach ($aBusinessHours as $aDayInfo){
				$aDay = array();
				if ( $aDayInfo['isOpen'] == 'no' ){
					$aDay['status'] = 'day_off';
					$aDay['text'] = esc_html__('Day Off', 'wilcity-mobile-app');
				}else{
					$aDay = array(
						'firstOpenHour' => Time::renderTime($aDayInfo['firstOpenHour'], $timeFormat),
						'firstCloseHour'=> Time::renderTime($aDayInfo['firstCloseHour'], $timeFormat)
					);
					if ( BH::isSecondHourExists($aDayInfo) ){
						$aDay['secondOpenHour'] = Time::renderTime($aDayInfo['secondOpenHour'], $timeFormat);
						$aDay['secondCloseHour'] = Time::renderTime($aDayInfo['secondCloseHour'], $timeFormat);
					}
					$aDay['status'] = 'working_day';
				}
				$aDay['is'] = $aDefineDaysOfWeek[$aDayInfo['dayOfWeek']];
				$aResponse['oAllBusinessHours'][] = $aDay;
			}
		}

		return json_encode(array(
			'mode' => 'rest',
			'oDetails'=> $aResponse
		));
	}
}