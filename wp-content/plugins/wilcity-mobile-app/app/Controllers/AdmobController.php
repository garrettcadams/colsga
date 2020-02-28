<?php

namespace WILCITY_APP\Controllers;


use WILCITY_SC\SCHelpers;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WILCITY_APP\Helpers\AppHelpers;
class AdmobController {
	public function __construct() {
		add_filter('wilcity/wilcity-mobile-app/before-save-homepage-sections', array($this, 'addAdmobToTopOfHomepage'));
		add_filter('wilcity/wilcity-mobile-app/filter/get-listings', array($this, 'addAdmobToTopOfTermPage'));
		add_filter('wilcity/wilcity-mobile-app/filter/listing-detail', array($this, 'addAdmobToTopSingleListingPage'), 10, 3);
	}

	protected function hasAdmob($aSectionKeys){
		foreach ($aSectionKeys as $type){
			if ( $type == 'GOOGLE_ADMOB' ){
				return true;
			}
		}
		return false;
	}

	private function getAdmob($where){
		if ( \WilokeThemeOptions::isEnable($where, false) ){
			return AppHelpers::getAdMobConfiguration();
		}

		return false;
	}

	/*
	 * If Admob is enabled on Taxonomy Page, let's add it
	 *
	 * @since 1.4.2
	 */
	public function addAdmobToTopOfTermPage($aResponse){
		if ( $aAdmob = $this->getAdmob('app_google_admob_taxonomy_page') ){
			$aResponse['oAdmob'] = $aAdmob;
		}

		return $aResponse;
	}

	/*
	 * If Admob is enabled on Single Listing Home Page, let's add it
	 *
	 * @since 1.4.2
	 */
	public function addAdmobToTopSingleListingPage($aPost, $aData, $postID){
		$belongsTo = GetSettings::getPostMeta($postID, 'belongs_to');
		if ( !empty($belongsTo) && !GetSettings::isPlanAvailableInListing($postID, 'toggle_admob') ){
			return $aPost;
		}

		if ( $aAdmob = $this->getAdmob('app_google_admob_single_listing') ){
			$aPost['oAdmob'] = $aAdmob;
		}

		return $aPost;
	}

	/*
	 * If Admob Shortcode is empty and Admin is enabling on Homepage under Theme Options, We will add Admob shortcode
	 * the top of the homepage
	 *
	 * @since 1.4.2
	 */
	public function addAdmobToTopOfHomepage($aSettings){
		if ( !$this->hasAdmob($aSettings['aSectionKeys']) ){
			$aAdmob = $this->getAdmob('app_google_admob_homepage');
			if ( !empty($aAdmob) ){
				$id = uniqid('section_');

				$aSettings['aSectionKeys'] = array_merge(array(
					$id => 'GOOGLE_ADMOB'
				), $aSettings['aSectionKeys']);

				$aSettings['aSectionsSettings'] = array_merge(
					array(
						$id => base64_encode(json_encode(array(
							'oResults'  => $aAdmob,
							'TYPE'      => 'GOOGLE_ADMOB'
						)))
					),
					$aSettings['aSectionsSettings']
				);
			}
		}

		return $aSettings;
	}
}