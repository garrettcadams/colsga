<?php
namespace WilokeListingTools\Framework\Payment;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Time;

class Coupon{
	protected $couponID;
	protected $slug;
	public $aSettings;

	public function setCouponID($couponID){
		$this->couponID = $couponID;
		return $this;
	}

	public function getCouponID($name){
		global $wpdb;
		$tbl = $wpdb->prefix . 'posts';

		$couponID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tbl WHERE post_title=%s AND post_status=%s AND post_type=%s",
				$name, 'publish', 'discount'
			)
		);

		if ( empty($couponID) ){
			return false;
		}

		$this->couponID = $couponID;
		return $this;
	}

	public function getCouponSlug(){
		$this->slug = get_post_field('post_name', $this->couponID);
		return $this;
	}

	public function isPostTypeSupported($postType){
		if ( $this->aSettings['for_post_type'] == 'both' ){
			return true;
		}
		return $postType == $this->aSettings['for_post_type'];
	}

	public function isCouponExpired(){
		$toTime = strtotime($this->aSettings['expiry_date']);
		$now = Time::timeStampNow();

		if ( $now > $toTime ){
			return true;
		}

		return false;
	}

	public function getCouponInfo(){
		$this->aSettings = GetSettings::getPostMeta($this->couponID, 'discount_general');
		$this->aSettings['ID']      = $this->couponID;
		$this->aSettings['slug']    = $this->slug;
		return $this;
	}
}