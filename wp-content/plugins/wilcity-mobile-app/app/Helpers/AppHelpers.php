<?php
namespace WILCITY_APP\Helpers;

use WILCITY_APP\Controllers\JsonSkeleton;
use WILCITY_SC\SCHelpers;

class AppHelpers{
	use JsonSkeleton;

	public static function removeUnnecessaryParamOnApp($aData){
		unset($aData['isApp']);
		unset($aData['extra_class']);
		unset($aData['alignment']);
		unset($aData['blur_mark']);
		unset($aData['blur_mark_color']);

		return $aData;
	}

	public static function getViewMoreArgs($atts)
    {
        $aViewmoreParams['postType'] = $atts['post_type'];
        $locationSlug                = AppHelpers::getFirstTermSlugBySelectedTerms($atts['listing_locations'],
            'listing_location');
        if (!empty($locationSlug)) {
            $aViewmoreParams['listing_location'] = $locationSlug;
        }
    
        $catSlug = AppHelpers::getFirstTermSlugBySelectedTerms($atts['listing_cats'], 'listing_cat');
        if (!empty($catSlug)) {
            $aViewmoreParams['listing_cat'] = $catSlug;
        }
    
        $tagSlug = AppHelpers::getFirstTermSlugBySelectedTerms($atts['listing_tags'], 'listing_tag');
        if (!empty($tagSlug)) {
            $aViewmoreParams['listing_tag'] = $tagSlug;
        }
        
        return $aViewmoreParams;
    }
	
	public static function getAddFullwidthConfiguration(){
		$type = \WilokeThemeOptions::getOptionDetail('app_google_fullwidth_admob_type');
		if ( empty($type) ){
			return '';
		}

		return  array(
			'oFullWidth' => array(
				'adUnitID'  => \WilokeThemeOptions::getOptionDetail('app_google_fullwidth_unit_id'),
				'variant'	=> $type
			)
		);
	}

	public static function getAddBannerConfiguration(){
		$bannerID = \WilokeThemeOptions::getOptionDetail('app_google_banner_unit_id');
		$bannerSize     = \WilokeThemeOptions::getOptionDetail('app_google_banner_size');

		return array(
			'oBanner' => array(
				'adUnitID'     => $bannerID,
				'bannerSize'   => !empty($bannerSize) ? $bannerSize : 'banner'
			)
		);
	}
	
	public static function getFirstTermSlugBySelectedTerms($values, $taxonomy)
    {
        $aTerms = SCHelpers::getAutoCompleteVal($values); // listing_locations
        if (!empty($aTerms)) {
            $termID = $aTerms[0];
            $oTerm = get_term_by('id', $termID, $taxonomy);
    
            if ($oTerm) {
                return $oTerm->slug;
            }
        }
        
        return '';
    }

	public static function getAdMobConfiguration(){
		$aFullWidth = self::getAddFullwidthConfiguration();
		$aBanner = self::getAddBannerConfiguration();

		if ( !empty($aFullWidth) && !empty($aBanner) ){
			return $aFullWidth + $aBanner;
		}

		if ( empty($aFullWidth) && empty($aBanner) ){
			return '';
		}

		if ( !empty($aFullWidth) ){
			return $aFullWidth;
		}

		return $aBanner;
	}
}
