<?php
namespace WILCITY_APP\SidebarOnApp;

use WilokeListingTools\Framework\Helpers\GetSettings;

class BusinessInfo {
	public function __construct() {
		add_filter('wilcity/mobile/sidebar/business_info', array($this, 'render'), 10, 2);
	}

	public function render($post, $aAtts){
		$aResponse['oAddress'] = GetSettings::getListingMapInfo($post->ID);
		$aResponse['email'] = !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_address') ? '' : GetSettings::getPostMeta($post->ID, 'email');
		$aResponse['phone'] = !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_phone') ? '' : GetSettings::getPostMeta($post->ID, 'phone');
		$aResponse['website'] = !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_website') ? '' : GetSettings::getPostMeta($post->ID, 'website');
		$aRawSocialNetworks = !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_social_networks') ? '' : GetSettings::getPostMeta($post->ID, 'social_networks');

		if ( empty($aRawSocialNetworks) ){
			$aResponse['oSocialNetworks'] = false;
		}else{
			$aSocialNetworks = array();
			foreach ($aRawSocialNetworks as $icon => $link){
				if ( empty($link) ){
					continue;
				}
				$aSocialNetworks[] = array(
					'icon' => $icon,
					'link' => $link
				);
			}
			if ( empty($aSocialNetworks) ){
				$aResponse['oSocialNetworks'] = false;
			}else{
				$aResponse['oSocialNetworks'] = $aSocialNetworks;
			}
		}

		return json_encode($aResponse);
	}
}