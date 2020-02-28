<?php
namespace WilokeListingTools\Framework\Payment;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;

class Receipt{
	public $aInfo = array();
	public $thankyouUrl;
	public $cancelUrl;
	public $productID;
	public $userID;
	public $planID;
	protected $planSettingKey = null;
	protected $packageType    = null;
	public $aPlan       = array();
	public $aRequested  = array();
	public $couponCode  = '';
	public $aCouponInfo = array();
	public $subTotal    = 0;
	public $total       = 0;
	public $discount    = 0;
	public $tax         = 0;
	protected $isNonrecurringPayment = true;

	protected $aPlanSettings = array();

	public function __construct($aInfo) {
		$this->aInfo = $aInfo;
		$this->aRequested = isset($aInfo['aRequested']) ? $aInfo['aRequested'] : array();
	}

	protected function getPlanSettingKey($planID){
		if ( $this->planSettingKey !== null ){
			return $this->planSettingKey;
		}

		$postType = get_post_field('post_type', $planID);
		$this->packageType = $postType;
		$this->planSettingKey = 'add_'.$postType;
	}

	public function roundPrice($price){
		return round(trim($price), 2);
	}

	public function getPrice(){
		return $this->roundPrice($this->aPlanSettings['regular_price']);
	}

	public function getPackageType(){
		return $this->packageType;
	}

	public function getPlanID(){
		$this->planID = isset($this->aInfo['planID']) ? abs(trim($this->aInfo['planID'])) : '';
		return $this;
	}

	public function setPlanName($planID){
		return GetWilokeSubmission::getField('brandname') . ' - ' . get_the_title($planID);
	}

	public function setToken($token){
		$this->aInfo['token'] = $token;
		return $this;
	}

	public function setThankyouUrl($thankyouUrl){
		$this->thankyouUrl = $thankyouUrl;
		return $this;
	}

	public function setCancelUrl($cancelUrl){
		$this->cancelUrl = $cancelUrl;
		return $this;
	}

	public function setPlanID($planID){
		$this->planID = $planID;
		return $this;
	}

	public function setRegularPeriod(){
		$this->regularPeriod = isset($this->aPlanSettings['regular_period']) ? abs(trim($this->aPlanSettings['regular_period'])) : '';
		return $this;
	}

	public function setTotal($total){
		$this->total = $total;
		return $this;
	}

	public function setSubTotal($subTotal){
		$this->subTotal = $subTotal;
		return $this;
	}

	public function setCouponCode(){
		$this->couponCode = isset($this->aInfo['couponCode']) ? trim($this->aInfo['couponCode']) : '';
		return $this;
	}

	public function setNonRecurringPayment($isNonRecurringPayment){
		$this->isNonrecurringPayment = $isNonRecurringPayment;
	}

	public function getPlanName(){
		return $this->aPlan['planName'];
	}

	public function setupPriceDirectly(){
		$this->userID  = $this->aInfo['userID'];
		$this->thankyouUrl  = GetWilokeSubmission::getField('thankyou', true);
		$this->cancelUrl    = GetWilokeSubmission::getField('cancel', true);
		$this->isNonrecurringPayment = $this->aInfo['isNonRecurringPayment'];

		if ( !isset($this->aInfo['productID']) ){
			if ( ( !isset($this->aInfo['total']) || !isset($this->aInfo['planName']) || !isset($this->aInfo['packageType']) )  ){
				$msg = esc_html__('The total price, packageType, and plan name are required', 'wiloke-listing-tools');
				if ( wp_doing_ajax() ){
					wp_send_json_error(
						array(
							'msg' => $msg
						)
					);
				}else{
					throw new \Exception($msg);
				}
			}

			$this->total      = $this->roundPrice($this->aInfo['total']);
			$this->subTotal   = $this->total;
		}else{
			if ( get_post_type($this->aInfo['productID']) !== 'product' || get_post_status($this->aInfo['productID']) !== 'publish' ){
				$msg = esc_html__('The product does not exist', 'wiloke-listing-tools');
				if ( wp_doing_ajax() ){
					wp_send_json_error(
						array(
							'msg' => $msg
						)
					);
				}else{
					throw new \Exception($msg);
				}
			}

			$this->productID = $this->aInfo['productID'];
		}

		if ( isset($this->aInfo['oStripeData']) ){
			$this->aRequested['email'] = $this->aInfo['oStripeData']['email'];
			$this->aRequested['token'] = $this->aInfo['oStripeData']['token'];
		}

		$this->aPlan['planName'] = $this->aInfo['planName'];
		$this->packageType       = $this->aInfo['packageType'];
		$this->aCouponInfo['discountPrice'] =  0;
	}

	public function setupPlan(){
		$this->planID = $this->aInfo['planID'];
		$this->getPlanSettingKey($this->planID);
		$this->aPlanSettings = GetSettings::getPostMeta($this->planID, $this->planSettingKey);
		$this->productID = isset($this->aInfo['productID']) ? $this->aInfo['productID'] : '';
		$this->aPlan['ID'] = $this->planID;
		$this->aPlan['post_type'] = get_post_type($this->planID);
		$this->userID       = $this->aInfo['userID'];
		$this->thankyouUrl  = GetWilokeSubmission::getField('thankyou', true);
		$this->cancelUrl    = GetWilokeSubmission::getField('cancel', true);
		$this->aPlan['slug'] = get_post_field('post_name', $this->planID);

		$this->isNonrecurringPayment = $this->aInfo['isNonRecurringPayment'];

		if ( empty($this->productID) ){
			$this->aPlan['regularPeriod'] = isset($this->aPlanSettings['regular_period']) ? abs(trim($this->aPlanSettings['regular_period'])) : '';
			$this->aPlan['trialPeriod'] = isset($this->aPlanSettings['trial_period']) ? abs(trim($this->aPlanSettings['trial_period'])) : '';
			$this->aPlan['planName'] = $this->setPlanName($this->planID);

			$total = $this->getPrice();
			$this->aPlan['total'] = $total;
			$this->subTotal     = $this->aPlan['total'];
			$this->couponCode = isset($this->aInfo['couponCode']) ? $this->aInfo['couponCode'] : '';

			$this->total = $this->subTotal;

			if ( $this->isNonrecurringPayment ){
				if ( !empty($this->couponCode) ){
					$instCoupon = new Coupon();
					$instCoupon->getCouponID($this->couponCode);
					$instCoupon->getCouponSlug();
					$instCoupon->getCouponInfo();
					if ( !$instCoupon->isCouponExpired() && $instCoupon->isPostTypeSupported($this->aPlan['post_type']) ){
						$this->aCouponInfo = $instCoupon->aSettings;
						$this->aCouponInfo['amount'] = $this->roundPrice($this->aCouponInfo['amount']);

						if ( $this->aCouponInfo['type'] == 'percentage' ){
							$this->aCouponInfo['discountPrice'] = $this->roundPrice($this->total*$this->aCouponInfo['amount']/100);
						}else{
							$this->aCouponInfo['discountPrice'] = $this->aCouponInfo['amount'];
						}
					}
				}
			}else{
				if ( !empty($this->aPlanSettings['trialPeriod']) ){
					if ( GetWilokeSubmission::canUserTrial($this->aPlan['ID'], $this->userID) ){
						$this->aPlanSettings['trialPeriod'] = '';
					}
				}
			}
		}

		return $this;
	}
}