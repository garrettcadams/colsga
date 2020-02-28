<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Routing\Controller;

class AuthorPageController extends Controller {
	public static $aAvailablePostTypes = array();

	public function __construct() {
		add_action('init', array($this, 'rewriteAuthorModeUrl'), 10, 0);
		add_filter('query_vars', array($this, 'addModeQueryVar'), 10, 1);
		add_filter('author_rewrite_rules', array($this, 'filterAuthorRewriteRules'));
	}

	public static function navigationWrapperClass($currentTab, $tabKey){
		if ( strpos($tabKey, '|') !==false ){
			$aTabKeys = explode('|', $tabKey);
			foreach ($aTabKeys as $key){
				if ( ($key == 'empty' && empty($currentTab)) || $currentTab == $key ){
					return 'list_item__3YghP active';
				}
			}
		}else{
			if ( $currentTab == $tabKey ){
				return 'list_item__3YghP active';
			}
		}

		return 'list_item__3YghP';
	}

	public static function getAuthorPostTypes($authorID){
		if ( isset(self::$aAvailablePostTypes[$authorID]) ){
			return self::$aAvailablePostTypes[$authorID];
		}

		$aPostTypes = General::getPostTypes(false);
		foreach ($aPostTypes as $postType => $aInfo){
			$totalPosts = count_user_posts($authorID, $postType);
			if ( !empty($totalPosts) ){
				$aInfo['totalPosts'] = $totalPosts;
				self::$aAvailablePostTypes[$authorID][$postType] = $aInfo;
			}
		}

		if ( isset(self::$aAvailablePostTypes[$authorID]) ){
			return self::$aAvailablePostTypes[$authorID];
		}

		self::$aAvailablePostTypes[$authorID] = false;
		return self::$aAvailablePostTypes[$authorID];
	}

	public static function getAuthorMode($mode){
		return apply_filters('wilcity/filter_author_mode', $mode);
	}

	public function rewriteAuthorModeUrl() {
		add_rewrite_rule('^author/([^/]+)/([^/]+)/?$', 'index.php?author_name=$matches[1]&mode=$matches[2]', 'top');
	}

	public function addModeQueryVar($vars) {
		$vars[] = 'mode';
		return $vars;
	}

	public function filterAuthorRewriteRules($rules){
		return array_merge(array(self::getAuthorMode('about').'/([^/]+)/([^/]+)/?$'=>'index.php?author_name=$matches[1]&mode=$matches[2]'), $rules);
	}
}