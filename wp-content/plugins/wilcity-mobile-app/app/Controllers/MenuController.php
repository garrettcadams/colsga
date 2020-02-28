<?php

namespace WILCITY_APP\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;

class MenuController {
	private $aStackNavigationRelationship = array(
		'homeStack'     => 'MenuHomeScreen',
		'listingStack'  => 'MenuListingScreen',
		'blogStack'     => 'MenuBlogScreen',
		'pageStack'     => 'MenuPageScreen',
		'eventStack'    => 'MenuEventScreen',
		'menuStack'     => 'MenuScreen'
	);

	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2/', 'navigators',  array(
				'methods'   => 'GET',
				'callback'  => array($this, 'getNavigators')
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2/', 'navigators/(?P<menuID>\w+)',  array(
				'methods'   => 'GET',
				'callback'  => array($this, 'getNavigator')
			));
		});
	}

	private function parseMenuNavigation($aMenuItem){
		$aMenuItem['navigation'] = '';
		if ( isset($this->aStackNavigationRelationship[$aMenuItem['screen']]) ){
			$aMenuItem['navigation'] = $this->aStackNavigationRelationship[$aMenuItem['screen']];
		}
		return $aMenuItem;
	}

	private function getMainMenu(){
		$aRawMainMenus = GetSettings::getOptions('mobile_main_menu');
		if ( empty($aRawMainMenus) ){
			return false;
		}

		$aMainMenu = array();
		foreach ($aRawMainMenus as $aMenuItem){
			if ( isset($aMenuItem['status']) && $aMenuItem['status'] == 'disable' ){
				continue;
			}
			$aMenuItem   = $this->parseMenuNavigation($aMenuItem);
			$aMainMenu[] = $aMenuItem;
		}

		return $aMainMenu;
	}

	private function getSecondaryMenu(){
		$aMenus = array();
		$aRawSecondaryMenus = GetSettings::getOptions('mobile_secondary_menu');
		if ( empty($aRawSecondaryMenus) ){
			return false;
		}

		foreach ($aRawSecondaryMenus as $aMenuItem){
			if ( isset($aMenuItem['status']) && $aMenuItem['status'] == 'disable' ){
				continue;
			}
			$aMenuItem   = $this->parseMenuNavigation($aMenuItem);
			$aMenus[] = $aMenuItem;
		}

		return $aMenus;
	}

	public function getNavigators(){
		$aMainMenu = $this->getMainMenu();
		$aSecondaryMenu = $this->getSecondaryMenu();

		if ( empty($aMainMenu) && empty($aSecondaryMenu) ){
			return array(
				'status' => 'error'
			);
		}

		return array(
			'status'    => 'success',
			'oResults'  => array(
				'aTabNavigator'   => $aMainMenu,
				'aStackNavigator' => $aSecondaryMenu,
			)
		);
	}

	public function getNavigator($aData){
		if ( $aData['menuID'] == 'stackNavigator' ){
			$aMenu = $this->getSecondaryMenu();
		}else{
			$aMenu = $this->getMainMenu();
		}

		if ( empty($aMenu) ){
			return array(
				'status' => 'error'
			);
		}

		return array(
			'status'    => 'success',
			'oResults'  => $aMenu
		);
	}
}