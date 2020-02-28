<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Routing\Controller;

class RestaurantMenuController extends Controller {
	private $hasChangedMenu = false;
	private $isDeletedOldMenu = false;

	public function __construct() {
		add_action('save_post', array($this, 'hasChangedMenuRestaurant'), 1, 1);
		add_action('save_post', array($this, 'saveRestaurantMenus'), 999, 3);
		add_action('wilcity/wiloke-listing-tools/before-update-new-menu', array($this, 'onUpdatedNumberOfRestaurantMenu'), 10, 1);
	}

	/*
	 * Re-updating Restaurant menu. Only administrator can pass this step
	 *
	 * @since 1.2.1.2
	 */
	public function saveRestaurantMenus($postID, $post, $updated){
		if ( !General::isAdmin() || !current_user_can('administrator') ){
			return false;
		}

		if ( !isset($_POST['wilcity_number_restaurant_menus']) || empty($_POST['wilcity_number_restaurant_menus']) || !isset($_POST['wilcity_changed_menu_restaurant']) || empty($_POST['wilcity_changed_menu_restaurant']) ){
			return false;
		}

		if ( !$this->hasChangedMenu ){
			$oldNumberOfMenus = GetSettings::getPostMeta($postID, 'number_restaurant_menus');
			if ( $oldNumberOfMenus == $_POST['wilcity_number_restaurant_menus'] ){
				return false;
			}
			$numberOfMenus = abs($_POST['wilcity_number_restaurant_menus']);
		}else{
			$numberOfMenus = GetSettings::getPostMeta($postID, 'number_restaurant_menus');
		}

		if ( empty($numberOfMenus) ){
			return false;
		}

		$menuOrders = explode(',', $_POST['wilcity_menu_restaurant_keys']);
		$aMenuOrders = array_map(function($groupKey){
			return str_replace('wilcity_restaurant_menu_group_', '', $groupKey);
		}, $menuOrders);

		$aRestaurantMenuPrefixKeys = array('wilcity_group_title_', 'wilcity_group_description_', 'wilcity_group_icon_', 'wilcity_restaurant_menu_group_');
		foreach( $aMenuOrders as $order => $realOrder ){
			foreach ($aRestaurantMenuPrefixKeys as $prefixKey){
				if ( isset($_POST[$prefixKey.$realOrder]) ){
					SetSettings::setPostMeta($postID, $prefixKey.$order, $_POST[$prefixKey.$realOrder]);
				}
			}
		}
	}

	/*
	 * Deleting old menus before updating a new one. This step is very important
	 *
	 * @since 1.2.1.2
	 */

	protected function deleteOldRestaurantMenus($postID){
		global $wpdb;
		if ( $this->isDeletedOldMenu ){
			return false;
		}

		$this->hasChangedMenu = true;
		$this->isDeletedOldMenu = true;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->postmeta WHERE post_id=%d AND (meta_key LIKE 'wilcity_restaurant_menu_group_%' || meta_key LIKE 'wilcity_group_title_%' || meta_key LIKE 'wilcity_group_description_%' || meta_key LIKE 'wilcity_group_icon_%')",
				$postID
			)
		);
	}

	/*
	 * @since 1.2.1.2
	 *
	 * Delete old menu restaurant after submitting a Listing on Front-end
	 */
	public function onUpdatedNumberOfRestaurantMenu($listingID){
		$this->deleteOldRestaurantMenus($listingID);
	}

	/*
	 * @since 1.2.1.2
	 */
	public function hasChangedMenuRestaurant($postID){
		if ( !isset($_POST['wilcity_changed_menu_restaurant']) || empty($_POST['wilcity_changed_menu_restaurant']) || !General::isAdmin() ){
			return false;
		}
		$this->deleteOldRestaurantMenus($postID);
	}
}