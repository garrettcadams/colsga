<?php

namespace WilokeListingTools\Models;


use WilokeListingTools\Framework\Helpers\GetSettings;

class Coupon {
	private static $aCoupon;

	public static function getCoupon($postID, $field=''){
		if ( isset(self::$aCoupon[$postID]) ){
			$aCoupon = self::$aCoupon[$postID];
		}else{
			$aCoupon = GetSettings::getPostMeta($postID, 'coupon');
			self::$aCoupon[$postID] = $aCoupon;
		}

		if ( empty($aCoupon) ){
			return false;
		}
		if ( empty($field) ){
			return $aCoupon;
		}

		if ($field == 'expiry_date' && isset($aCoupon[$field]) && is_numeric($aCoupon[$field])) {
            return date('Y-m-d\TH:i:s', $aCoupon[$field]);
        }

		return isset($aCoupon[$field]) ? $aCoupon[$field] : '';
	}
}
