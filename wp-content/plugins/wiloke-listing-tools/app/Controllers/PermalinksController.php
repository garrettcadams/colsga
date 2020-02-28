<?php
namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Routing\Controller;

class PermalinksController extends Controller {
	public function __construct(){
		add_filter('wilcity/filter/register-post-types/listings', array($this, 'filterRewriteRule'));
		add_filter('post_type_link', array($this, 'modifyLink'), 1, 2 );
		add_filter('rewrite_rules_array', array($this, 'addRewriteRulesArray'));
	}

	public function addRewriteRulesArray($aRules){
		if ( !class_exists('Wiloke') ){
			return $aRules;
		}
		$new = array();
		$aThemeOptions = \Wiloke::getThemeOptions(true);

		if ( !isset($aThemeOptions['listing_permalink_settings']) || empty($aThemeOptions['listing_permalink_settings']) ){
			return $aRules;
		}

		$lCase = 0; $cCase = 0;

		if ( '%listingLocation%' == $aThemeOptions['listing_permalink_settings'] ){
			$lCase = 3;
		}else if ('%listingCat%' == $aThemeOptions['listing_permalink_settings']){
			$cCase = 3;
		}else if ( strpos($aThemeOptions['listing_permalink_settings'], '%listingLocation%/%listingCat%') !== false ){
			$lCase = 1;
			$cCase = 2;
		}else if ( strpos($aThemeOptions['listing_permalink_settings'], '%listingCat%/%listingLocation%') !== false ){
			$lCase = 2;
			$cCase = 1;
		}

		$aCustomPostTypes = GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));

		foreach ($aCustomPostTypes as $aPostType){
			if ( $lCase == 1 || $cCase == 1 ){
				$new[$aPostType['slug'].'/([^/]+)/(.+)/(.+)/?$'] = 'index.php?'.$aPostType['key'].'=$matches[3]';
				if ( $lCase == 1 ){
					$new[$aPostType['slug'].'/(.+)/(.+)/(.+)/?$'] = 'index.php?listing_location=$matches[1]';
					$new[$aPostType['slug'].'/(.+)/(.+)/(.+)/?$'] = 'index.php?listing_cat=$matches[2]';
				}else if ( $lCase == 2 ){
					$new[$aPostType['slug'].'/(.+)/(.+)/(.+)/?$'] = 'index.php?listing_cat=$matches[1]';
					$new[$aPostType['slug'].'/(.+)/(.+)/(.+)/?$'] = 'index.php?listing_location=$matches[2]';
				}
			}else if ( $lCase == 3 || $cCase == 3 ){
				$new[$aPostType['slug'].'/([^/]+)/(.+)/?$'] = 'index.php?'.$aPostType['key'].'=$matches[2]';
				if ( $lCase == 3 ){
					$new[$aPostType['slug'].'/(.+)/(.+)/?$'] = 'index.php?listing_location=$matches[1]';
				}else if ( $cCase == 3 ){
					$new[$aPostType['slug'].'/(.+)/(.+)/?$'] = 'index.php?listing_cat=$matches[1]';
				}
			}
		}

		return array_merge( $new, $aRules ); // Ensure our rules come first
	}

	protected function getChildCategory($aTerms){
		foreach ($aTerms as $oTerm){
			if ( $oTerm->parent != 0 ){
				return $oTerm;
			}
		}
		return end($aTerms);
	}

	public function modifyLink($postLink, $post){
		$aThemeOptions = \Wiloke::getThemeOptions(true);
		if ( !isset($aThemeOptions['listing_permalink_settings']) || empty($aThemeOptions['listing_permalink_settings']) ){
			return $postLink;
		}

		$aPostTypes = General::getPostTypeKeys(false, true);

		if ( is_object( $post ) && in_array($post->post_type, $aPostTypes) ){
			if ( strpos($aThemeOptions['listing_permalink_settings'],'%listingLocation%') !== false ){
				$aTerms = wp_get_object_terms( $post->ID, 'listing_location' );

				if ( !empty($aTerms) && !is_wp_error($aTerms) ){
					$oTerm = $this->getChildCategory($aTerms);
					$slug = $oTerm->slug;

					if ( $oTerm->parent !== 0 && $aThemeOptions['taxonomy_add_parent_to_permalinks'] == 'enable' && strpos($aThemeOptions['listing_permalink_settings'], '%listingCat%') !== false ){
						$oParent = get_term_by('term_taxonomy_id', $oTerm->parent);
						if ( !empty($oParent) && !is_wp_error($oParent) ){
							$slug =  $oParent->slug .'/' . $slug;
						}
					}
				}else{
					$slug = apply_filters('wilcity/wiloke-listing-tools/custom-permalinks/unlocation', 'unlocation');
				}
				$postLink = str_replace( '%listingLocation%' , $slug , $postLink );
			}

			if ( strpos($aThemeOptions['listing_permalink_settings'],'%listingCat%') !== false ){
				$aTerms = wp_get_object_terms( $post->ID, 'listing_cat' );

				if ( !empty($aTerms) && !is_wp_error($aTerms) ){
					$oTerm = $this->getChildCategory($aTerms);
					$slug = $oTerm->slug;
				}else{
					$slug = 'uncategory';
				}
				$postLink = str_replace( '%listingCat%' , $slug , $postLink );
			}
		}
		return $postLink;
	}

	public function filterRewriteRule($aConfiguration){
		if ( !class_exists('Wiloke') ){
			return $aConfiguration;
		}

		if ( current_user_can('administrator') && defined('WPSEO_VERSION') ){
			$currentPageUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			if ( preg_match('/\.xml$/', $currentPageUrl, $aMatches) ){
				return $aConfiguration;
			}
		}

		$aThemeOptions = \Wiloke::getThemeOptions(true);
		if ( isset($aThemeOptions['listing_permalink_settings']) && !empty($aThemeOptions['listing_permalink_settings']) ){
			$aConfiguration['rewrite']['slug'] = $aConfiguration['rewrite']['slug'] . '/' . $aThemeOptions['listing_permalink_settings'];
			$aConfiguration['has_archive'] = true;
			$aConfiguration['hierarchical'] = true;
		}
		return $aConfiguration;
	}
}