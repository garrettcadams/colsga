<?php

namespace WILCITY_APP\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;

class Taxonomies {
	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/taxonomies/listing-locations', array(
				'methods' => 'GET',
				'callback' => array($this, 'getLocationTerms'),
			));
			register_rest_route( WILOKE_PREFIX.'/v2', '/taxonomies/listing-categories', array(
				'methods' => 'GET',
				'callback' => array($this, 'getCategoryTerms'),
			));
			register_rest_route( WILOKE_PREFIX.'/v2', '/taxonomies/listing-tags', array(
				'methods' => 'GET',
				'callback' => array($this, 'getTagTerms'),
			));
		} );
	}

	public function getTerms($taxonomy, $aData){
		$isHideEmpty = isset($aData['hideEmpty']) && $aData['hideEmpty']=='yes';
		$aArgs = array();

		if ( isset($aData['orderBy']) ){
			$aArgs['orderBy'] = $aData['orderBy'];
		}else{
			$aArgs['orderBy'] = 'count';
		}

		if ( isset($aData['postType']) && !empty($aData['postType']) ){
			$aRawTerms = GetSettings::getTaxonomyHierarchy(array(
				'taxonomy' => $taxonomy,
				'orderby'  => isset($aField['orderBy']) ? $aField['orderBy'] : 'count',
				'parent'   => 0
			), $aData['postType'], false, false);

			if ( empty($aRawTerms) || is_wp_error($aRawTerms) ){
				return false;
			}

			$aTermIDs = array();
			foreach ($aRawTerms as $oTerm){
				$aTermIDs[] = $oTerm->term_id;
			}

			$aArgs['include'] = $aTermIDs;
		}

		$aArgs = $aArgs + array(
			'taxonomy' => $taxonomy,
			'hide_empty' => $isHideEmpty
		);
		$aTerms = GetSettings::getTerms($aArgs);

		if ( !$aTerms ){
			return false;
		}

		$aResponse = array();
		foreach ($aTerms as $key => $oTerm){
			$aTerm                  = get_object_vars($oTerm);
			$aTerm['featuredImg']   = GetSettings::getTermMeta($oTerm->term_id, 'featured_image');
			$aTerm['oIcon']         = \WilokeHelpers::getTermOriginalIcon($oTerm);
			if ( isset($aData['postType']) && !empty($aData['postType']) ){
				$aPostTypes = explode(',', $aData['postType']);
				$aTerm['count']         = GetSettings::getTermCountInPostType($aPostTypes, $oTerm->term_id);
			}

			$aResponse[$key]        = $aTerm;
		}

		return $aResponse;
	}

	protected function responseTerms($aTerms, $aData){
		return apply_filters('wilcity/wilcity-mobile-app/term', array(
			'status' => 'success',
			'aTerms' => $aTerms
		), $aData);
	}

	public function getLocationTerms($aData){
		$aTerms = $this->getTerms('listing_location', $aData);
		if ( !$aTerms ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('There are no terms', WILCITY_MOBILE_APP)
			);
		}

		return $this->responseTerms($aTerms, $aData);
	}

	public function getCategoryTerms($aData){
		$aTerms = $this->getTerms('listing_cat', $aData);

		if ( !$aTerms ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('There are no terms', WILCITY_MOBILE_APP)
			);
		}

		return $this->responseTerms($aTerms, $aData);
	}

	public function getTagTerms($aData){
		$aTerms = $this->getTerms('listing_tag', $aData);

		if ( !$aTerms ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('There are no terms', WILCITY_MOBILE_APP)
			);
		}

		return $this->responseTerms($aTerms, $aData);
	}
}