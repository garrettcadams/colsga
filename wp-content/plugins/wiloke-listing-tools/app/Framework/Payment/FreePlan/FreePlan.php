<?php
namespace WilokeListingTools\Framework\Payment\FreePlan;


use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\InvoiceModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class FreePlan{
	public $gateway = 'free';
	protected $paymentID;
	protected $oReceipt;
	protected $thankyouUrl = null;
	protected $cancelUrl = null;
	protected $total=0;
	protected $subTotal=0;
	protected $tax=0;
	protected $discountPrice=0;
	protected $currency;
	protected $planID;

	public function __construct($planID) {
		$this->planID = $planID;
	}

	public function getBillingType() {
		return wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring');
	}

	private function setupReceipt(){
		$this->oReceipt = new Receipt(array(
			'planID'    => $this->planID,
			'userID'    => User::getCurrentUserID(),
			'couponCode'            => '',
			'isNonRecurringPayment' => true
		));
		$this->oReceipt->setupPlan();
	}

	public function proceedPayment() {
		$this->setupReceipt();
		$this->currency = GetWilokeSubmission::getField('currency_code');
		$this->paymentID = PaymentModel::setPaymentHistory($this, $this->oReceipt);

		$oUserInfo = get_userdata(get_current_user_id());
		PaymentMetaModel::set($this->paymentID, wilokeListingToolsRepository()->get('payment:paymentInfo'), array(
			'created_gmt_at'  => Time::timestampUTCNow(),
			'created_at'      => Time::timeStampNow(),
			'sessionID'       => $this->paymentID,
			'userInfo'        => array(
				'ID'          => get_current_user_id(),
				'email'       => $oUserInfo->user_email,
				'user_meta'   => $oUserInfo->user_meta,
			)
		));

		/*
		 * @hooked EventController@setPlanRelationshipBeforePayment
		 */
		do_action('wiloke-listing-tools/before-payment-process', array(
			'paymentID' => $this->paymentID,
			'planID'    => $this->oReceipt->planID,
			'gateway'   => $this->gateway
		));

		InvoiceModel::set(
			$this->paymentID,
			array(
				'subTotal'      => 0,
				'total'         => 0,
				'currency'      => $this->currency,
				'discount'      => 0,
				'tax'           => 0
			)
		);

		$aResponse = apply_filters('wiloke-listing-tools/framework/payment/response', array(
			'status'        => 'succeeded',
			'gateway'       => $this->gateway,
			'billingType'   => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring'),
			'paymentID'     => $this->paymentID,
			'planID'        => $this->planID,
			'planRelationshipID' => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionRelationshipStore'), true),
			'postID'        => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'), true),
			'isFreePlanSubmittingOnFrontend' => true
		));

		do_action('wiloke-listing-tools/payment-succeeded/'.get_post_type($this->planID), $aResponse);
		do_action('wiloke-listing-tools/payment-succeeded', $aResponse);

		return array(
			'status' => 'success',
			'paymentID' => $this->paymentID,
			'msg'    => esc_html__('Congratulations! Your payment has been succeeded', 'wiloke-listing-tools')
		);
	}

}