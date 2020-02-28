<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetGeneral {
	protected function getTimeZoneByGeocode(){
		global $wiloke;

		if ( \WilokeThemeOptions::getOptionDetail('map_type') == 'google_map' ){
			$url = 'https://maps.googleapis.com/maps/api/timezone/json?location='.$this->aGoogleAddress['latLng'].'&timestamp='.time().'&key='.$wiloke->aThemeOptions['general_google_api'];
			$aTimeZone = wp_remote_get(esc_url_raw($url));
			if ( is_wp_error($aTimeZone)  ){
				return '';
			}else{
				$oTimeZone = json_decode($aTimeZone['body']);
				return isset($oTimeZone->timeZoneId) ? $oTimeZone->timeZoneId : '';
			}

		}else{
			$aParseLatLng = explode(',', $this->aGoogleAddress['latLng']);
			$url = 'https://api.mapbox.com/v4/examples.4ze9z6tv/tilequery/'.$aParseLatLng[1] .','.$aParseLatLng[0].'.json?access_token='.\WilokeThemeOptions::getOptionDetail('mapbox_api');

			$aTimeZone = wp_remote_get(esc_url_raw($url));
			if ( is_wp_error($aTimeZone)  ){
				return '';
			}else{
				$oTimeZone = json_decode($aTimeZone['body']);
				return isset($oTimeZone->features[0]) ? $oTimeZone->features[0]->properties->TZID : '';
			}
		}
	}

	protected function setListingTimeZone(){
		$timeZone = $this->getTimeZoneByGeocode();
		SetSettings::setPostMeta($this->listingID, 'timezone', $timeZone);
	}

	protected function setGeneralSettings(){
		$this->setListingTimeZone();
		foreach ( $this->aGeneralData as $fieldKey => $val ) {
			if ( $fieldKey == 'cover_image' || $fieldKey == 'logo' ){
				SetSettings::setPostMeta($this->listingID, $fieldKey, $val['src']);
				SetSettings::setPostMeta($this->listingID, $fieldKey.'_id', $val['id']);
			}else{
				SetSettings::setPostMeta($this->listingID, $fieldKey, $val);
			}
		}
	}
}