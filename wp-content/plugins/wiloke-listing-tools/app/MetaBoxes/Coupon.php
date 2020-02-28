<?php

namespace WilokeListingTools\MetaBoxes;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Models\Coupon as CouponModel;

class Coupon {
	public function __construct() {
		add_action('save_post', array($this, 'saveCoupon'));
	}

	public function saveCoupon($listingID){
		if ( !current_user_can('edit_theme_options') ){
			return false;
		}

		if ( !isset($_POST['wilcity_coupon']) || empty($_POST['wilcity_coupon']) ){
			return false;
		}

		$aCoupon = array();

		foreach ($_POST['wilcity_coupon'] as $key => $val){
            if ($key == 'expiry_date') {
                if (is_array($val)) {
                    if (!empty($val['date'])) {
                        $val = Time::toTimestamp('m/d/Y g:i A', $val['date'] . ' ' . $val['time'], 'UTC');
                    } else {
                        $val = Time::toTimestamp('m/d/Y', $val['date'], 'UTC');
                    }
                }
            }
			$aCoupon[sanitize_text_field($key)] = sanitize_text_field($val);
		}

		SetSettings::setPostMeta($listingID, 'coupon', $aCoupon);
	}

	private static function getCouponInfo($field){
		if ( !is_admin() ){
			return false;
		}

		if ( isset($_GET['post']) && is_numeric($_GET['post']) ){
			$val = CouponModel::getCoupon($_GET['post'], $field);
			return $val;
		}

		return false;
	}

	public static function getPopupImage(){
		if ( !is_admin() ){
			return false;
		}

		if ( isset($_GET['post']) && is_numeric($_GET['post']) ){
			$val = CouponModel::getCoupon($_GET['post'], 'popup_image');
			return empty($val) ? $val : wp_get_attachment_image_url($val);
		}

		return false;
	}

	public static function getTitle(){
		return self::getCouponInfo('title');
	}

	public static function getExpiry(){
		return date('m/d/Y g:i A', self::getCouponInfo('expiry_date'));
	}

	public static function getDescription(){
		return self::getCouponInfo('description');
	}

	public static function getHighlight(){
		return self::getCouponInfo('highlight');
	}

	public static function getCode(){
		return self::getCouponInfo('code');
	}

	public static function getRedirectTo(){
		return self::getCouponInfo('redirect_to');
	}

	public static function getPopupDescription(){
		return self::getCouponInfo('popup_description');
	}
}
