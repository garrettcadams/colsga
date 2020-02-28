<?php
namespace WilokeListingTools\Framework\Payment\DirectBankTransfer;


use WilokeListingTools\Framework\Payment\PaymentMethodInterface;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class DirectBankTransferNonRecurringPayment implements PaymentMethodInterface {
	use DirectBankTransferConfiguration;
	use GeneratePaymentInfo;

	protected $paymentID;
	protected $storeTokenPlanSession;
	protected $oReceipt;
	protected $thankyouUrl = null;
	protected $cancelUrl = null;
	protected $aPayPayConfiguration;
	protected $instPayPalConfiguration;
	protected $total;
	protected $subTotal;
	protected $tax=0;
	protected $discountPrice;
	protected $currency;

	public function getBillingType() {
		return wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring');
	}

	public function setup(){
		$this->setupConfiguration();
	}

	public function proceedPayment(Receipt $oReceipt) {
		$this->oReceipt = $oReceipt;
		$this->setup();

		if ( isset($this->oReceipt->aCouponInfo['discountPrice']) && !empty($this->oReceipt->aCouponInfo['discountPrice']) ){
			$this->subTotal  = $this->oReceipt->subTotal;
			$this->total = floatval($this->oReceipt->total);
			$this->discountPrice = -$this->oReceipt->aCouponInfo['discountPrice'];
		}else{
			$this->total = $this->subTotal = $this->oReceipt->total;
			$this->discountPrice = 0;
		}

		$this->currency = $this->aConfiguration['currency_code'];

		$this->paymentID = PaymentModel::setPaymentHistory($this, $this->oReceipt);

		/*
		 * @hooked EventController@setPlanRelationshipBeforePayment
		 */
		do_action('wiloke-listing-tools/before-payment-process', array(
			'paymentID' => $this->paymentID,
			'planID'    => $this->oReceipt->planID,
			'gateway'   => $this->gateway
		));
		
		PaymentMetaModel::set($this->paymentID, wilokeListingToolsRepository()->get('payment:paymentInfo'), $this->generateTransactionInfo());

		/*
		 * @PlanRelationshipController:update 5
		 */
//		$category = Session::getSession(wilokeListingToolsRepository()->get('payment:category'));

		$claimID = Session::getSession(wilokeListingToolsRepository()->get('claim:sessionClaimID'), true);
		$aResponse = array(
			'status'        => 'pending',
			'gateway'       => $this->gateway,
			'billingType'   => $this->getBillingType(),
			'paymentID'     => $this->paymentID,
			'userID'        => get_current_user_id(),
			'planID'        => isset($this->oReceipt->aPlan['ID']) ? $this->oReceipt->aPlan['ID'] : '',
			'planRelationshipID' => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionRelationshipStore')),
			'claimID'       => $claimID
		);

		$category = Session::getSession(wilokeListingToolsRepository()->get('payment:category'), true);
		$category = empty($category) ? 'dadadzzdadad' : $category;
		do_action('wiloke-listing-tools/payment-pending/'.$category, $aResponse);
		do_action('wiloke-listing-tools/payment-pending/'.$this->oReceipt->getPackageType(), $aResponse);
		do_action('wiloke-listing-tools/payment-pending', $aResponse);

		/*
		 * We will delete all sessions here
		 */
		do_action('wiloke-submission/payment-succeeded-and-updated-everything');
		return array(
			'status'    => 'success',
			'paymentID' => $this->paymentID,
			'claimID'   => $claimID,
			'msg'       => esc_html__('Congratulations! Your payment has been succeeded', 'wiloke-listing-tools')
		);
	}

}