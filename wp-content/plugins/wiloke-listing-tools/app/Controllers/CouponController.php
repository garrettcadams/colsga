<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Payment\Coupon;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use \WilokeListingTools\Models\Coupon as CouponModel;

class CouponController extends Controller {
	protected $planID;
	public function __construct() {
		add_action('wp_ajax_nopriv_wiloke_submission_verify_coupon', array($this, 'verifyCoupon'));
		add_action('wp_ajax_wiloke_submission_verify_coupon', array($this, 'verifyCoupon'));
		add_action('wilcity/footer/vue-popup-wrapper', array($this, 'printCouponPopup'));
	}

	public function printCouponPopup(){
		if ( !is_singular() ){
			return '';
		}

		global $post;
		$aCoupon = CouponModel::getCoupon($post->ID);
		if ( empty($aCoupon) ){
			return '';
		}

		?>
		<coupon-popup post-url="<?php echo esc_url(get_permalink($post->ID)); ?>" title="<?php echo esc_attr($aCoupon['title']); ?>" code="<?php echo esc_attr($aCoupon['code']); ?>" img="<?php echo GetSettings::getCouponFeatureImg($aCoupon); ?>" desc="<?php echo esc_attr
        ($aCoupon['popup_description']); ?>"></coupon-popup>
		<?php
	}

	public function verifyCoupon(){
		$this->planID = Session::getSession(wilokeListingToolsRepository()->get('payment:storePlanID'));
		$planPostType = get_post_field('post_type', $this->planID);
		$aPackageInfo = GetSettings::getPlanSettings($this->planID);
		$price = $aPackageInfo['regular_price'];

		$subTotal = GetWilokeSubmission::renderPrice($price);
		$aErrMsg = array(
			'msg' => \WilokeMessage::message(array(
				'msg'           => esc_html__('The coupon is invalid or has expired', 'wiloke-listing-tools'),
				'status'        => 'danger'
			), true),
			'subTotal'  => $subTotal,
			'discount'  => GetWilokeSubmission::renderPrice(0),
			'total'     => $subTotal
		);

		if ( !isset($_POST['code']) || empty($_POST['code']) ){
			wp_send_json_error($aErrMsg);
		}

		$instCoupon = new Coupon();
		if ( !$instCoupon->getCouponID($_POST['code']) ){
			wp_send_json_error($aErrMsg);
		}

		$instCoupon->getCouponInfo();
		if ( $instCoupon->isCouponExpired() ){
			wp_send_json_error($aErrMsg);
		}

		if ( !$instCoupon->isPostTypeSupported($planPostType) ){
			wp_send_json_error($aErrMsg);
		}

		if ( $instCoupon->aSettings['type'] == 'percentage' ){
			$discountPrice = round(($price*$instCoupon->aSettings['amount'])/100, 2);
		}else{
			$discountPrice = $instCoupon->aSettings['amount'];
		}

		if ( $price < $discountPrice ){
			$newPrice = 0;
			$aMessage = \WilokeMessage::message(array(
				'msg' => sprintf(__('You save %s', 'wiloke-listing-tools'), GetWilokeSubmission::renderPrice($price))
			), true);
			$discountPrice = $price;
		}else{
			$newPrice = round($price - $discountPrice, 2);
			$aMessage = \WilokeMessage::message(array(
				'msg' => sprintf(__('You save %s', 'wiloke-listing-tools'), GetWilokeSubmission::renderPrice($discountPrice))
			), true);
		}

		wp_send_json_success(
			array(
				'msg'      => $aMessage,
				'subTotal' => $price,
				'discount' => GetWilokeSubmission::renderPrice($discountPrice),
				'total'	   => GetWilokeSubmission::renderPrice($newPrice)
			)
		);
	}
}
