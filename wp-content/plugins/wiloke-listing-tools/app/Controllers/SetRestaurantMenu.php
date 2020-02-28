<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetRestaurantMenu {
	protected function setRestaurantMenu(){
		$totalNewMenus = empty($this->aRestaurantMenu) ? 0 : count($this->aRestaurantMenu);
		SetSettings::setPostMeta($this->listingID, 'number_restaurant_menus', $totalNewMenus);
		/*
		 * @hooked RestaurantMenuController:onUpdatedNumberOfRestaurantMenu
		 */
		do_action('wilcity/wiloke-listing-tools/before-update-new-menu', $this->listingID);
		$order = 0;

		// Make sure that cache is deleted before updating
		wp_cache_delete($this->listingID, 'post_meta');
		foreach ($this->aRestaurantMenu as $aMenus){
			SetSettings::setPostMeta($this->listingID, 'group_title_'.$order, $aMenus['group_title']);
			SetSettings::setPostMeta($this->listingID, 'group_description_'.$order, $aMenus['group_description']);
			SetSettings::setPostMeta($this->listingID, 'group_icon_'.$order, $aMenus['group_icon']);

			unset($aMenus['group_title']);
			unset($aMenus['group_description']);
			unset($aMenus['group_icon']);
			SetSettings::setPostMeta($this->listingID, 'restaurant_menu_group_'.$order, $aMenus['items']);
			$order++;
		}
	}
}