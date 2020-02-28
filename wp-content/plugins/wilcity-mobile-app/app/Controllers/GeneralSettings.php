<?php

namespace WILCITY_APP\Controllers;


use WilokeListingTools\Controllers\RegisterLoginController;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\User;

class GeneralSettings {
	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', 'general-settings', array(
				'methods'   => 'GET',
				'callback'  => array($this, 'getColorPrimary')
			));
		});
	}

	public function getColorPrimary(){
		$aThemeOptions = \Wiloke::getThemeOptions(true);
		$themeColor = $aThemeOptions['advanced_main_color'];
		if ( $themeColor == 'custom' ){
			if ( isset($aThemeOptions['advanced_custom_main_color']['rgba']) ){
				$themeColor = $aThemeOptions['advanced_custom_main_color']['rgba'];
			}
		}else{
			$themeColor = '#f06292';
		}

		$googleAPI = isset($aThemeOptions['general_google_api']) && !empty($aThemeOptions['general_google_api']) ? $aThemeOptions['general_google_api'] : '';
		$googleLang = isset($aThemeOptions['general_google_language']) && !empty($aThemeOptions['general_google_language']) ? $aThemeOptions['general_google_language'] : '';

		if ( !isset($aThemeOptions['content_position']) ){
			$contentPosition = 'above_sidebar';
		}else{
			$contentPosition = $aThemeOptions['content_position'];
		}

		$customLoginPageID = \WilokeThemeOptions::getOptionDetail('custom_login_page');
		if (!empty($customLoginPageID)) {
			$resetPasswordURL = get_permalink($customLoginPageID);
		} else {
			$resetPasswordURL = get_permalink(\WilokeThemeOptions::getOptionDetail('reset_password_page'));
		}

		$isFBLogin = \WilokeThemeOptions::getOptionDetail('fb_toggle_login') == 'enable';
		$unit = \WilokeThemeOptions::getOptionDetail('distance');

		if ( \WilokeThemeOptions::isEnable('toggle_custom_login_page') && !empty(\WilokeThemeOptions::getOptionDetail('custom_login_page')) ) {
			$resetPasswordURL = get_permalink(\WilokeThemeOptions::getOptionDetail('custom_login_page'));
			$resetPasswordURL = add_query_arg(
				array(
					'action' => 'rp'
				),
				$resetPasswordURL
			);
		} else {
			$resetPasswordURL = get_permalink(\WilokeThemeOptions::getOptionDetail('reset_password_page'));
		}
		
		return array(
			'colorPrimary' => $themeColor,
			'oGoogleMapAPI'=> array(
				'key'           => $googleAPI,
				'language'      => $googleLang,
				'types'         => 'geocode'
			),
			'defaultZoom'   => 39.5,
			'oSingleListing' => array(
				'contentPosition' => $contentPosition
			),
			'isAllowRegistering' => RegisterLoginController::canRegister() ? 'yes' : 'no',
			'oAdmob' => array(

			),
			'oFacebook' => array(
				'isEnableFacebookLogin' => apply_filters('wilcity/wilcity-mobile-app/filter/is-fb-login', $isFBLogin),
				'appID' => \WilokeThemeOptions::getOptionDetail('fb_api_id')
			),
			'unit' => empty($unit) ? 'km' : $unit,
            'resetPasswordURL' => $resetPasswordURL
		);
	}
}
