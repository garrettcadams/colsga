<?php

namespace WilokeListingTools\Models;


class BookingCom {
	private static $creatorBannerPrefix = '_bdotcom_bc_mbe_';

	public static function getCreatorIDByParentID($listingID){
		global $wpdb;

		$ID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $wpdb->posts.ID FROM $wpdb->posts WHERE post_type=%s AND post_parent=%d AND post_status='publish' ORDER BY $wpdb->posts.ID DESC",
				'bdotcom_bm', $listingID
			)
		);

		return empty($ID) ? false : $ID;
	}

	private static function getBookingComBannerCreatorKey($key){
		$realKey = '';
		switch ($key){
			case 'buttonName':
				$realKey = self::$creatorBannerPrefix.'button_copy';
				break;
			case 'buttonColor':
				$realKey = self::$creatorBannerPrefix.'button_copy_colour';
				break;
			case 'buttonBg':
				$realKey = self::$creatorBannerPrefix.'button_bg';
				break;
			case 'bannerImg':
				$realKey = self::$creatorBannerPrefix.'img_path';
				break;
			case 'bannerCopy':
				$realKey = self::$creatorBannerPrefix.'copy';
				break;
			case 'bannerCopyColor':
				$realKey = self::$creatorBannerPrefix.'copy_colour';
				break;
			case 'bannerLink':
				$realKey = self::$creatorBannerPrefix.'banner_link';
				break;
		}
		return $realKey;
	}

	public static function getBookingComCreatorVal($bookingID, $key){
		if ( strpos($key, self::$creatorBannerPrefix) === false ){
			$key = self::getBookingComBannerCreatorKey($key);
		}

		if ( empty($key) ){
			return '';
		}

		return get_post_meta($bookingID, $key, true);
	}

	public static function updateBannerCreator($parentID, $bookingID, $aData){
		wp_update_post(
			array(
				'ID'            => $bookingID,
				'post_type'     => 'bdotcom_bm',
				'post_status'   => 'publish',
				'post_title'    => sprintf(esc_html__('Display On %s', 'wiloke-listing-tools'), get_the_title($parentID)),
				'post_parent'   => $parentID
			)
		);

		update_post_meta($bookingID, '_bdotcom_bc_mbe_themes', 'custom_theme');
		foreach ($aData as $key => $val){
			$realKey = self::getBookingComBannerCreatorKey($key);
			if ( !empty($realKey) ){
				update_post_meta($bookingID, $realKey, sanitize_text_field($val));

				if ( $realKey == 'bdotcom_bc_mbe_button_copy' && !empty($val) ){
					update_post_meta($bookingID, 'bdotcom_bc_mbe_button', 1);
				}else{
					delete_post_meta($bookingID, 'bdotcom_bc_mbe_button');
				}
			}
		}

		$aThemeOptions = \Wiloke::getThemeOptions(true);
		update_post_meta($bookingID, '_bdotcom_bc_mbe_aid', $aThemeOptions['bookingcom_affiliate_id']);

		return true;
	}

	public static function insertBannerCreator($parentID, $aData){
		$bookingID = wp_insert_post(
			array(
				'post_status'=> 'publish',
				'post_type' => 'bdotcom_bm',
				'post_title'=> sprintf(esc_html__('Display On %s', 'wiloke-listing-tools'), get_the_title($parentID)),
				'post_parent'=> $parentID
			)
		);

		self::updateBannerCreator($parentID, $bookingID, $aData);
	}
}