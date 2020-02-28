<?php

namespace WilokeListingTools\Framework\Payment\WooCommerce;


use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Framework\Payment\PaymentMethodInterface;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class WooCommerceRecurringPayment implements PaymentMethodInterface {
	public $gateway = 'woocommerce';
	protected $userID;
	protected $paymentID;
	protected $oReceipt;
	protected $orderID;

	public function getBillingType() {
		return wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring');
	}

	protected function setup(){
		$this->userID = get_current_user_id();
	}

	private function insertingNewSession(){
		$this->paymentID = PaymentModel::setPaymentHistory($this, $this->oReceipt, $this->orderID);
	}

	public function setOrderID($orderID){
		if ( empty($orderID) ){
			Message::error(esc_html__('The order id is required', 'wiloke-listing-tools'));
		}
		$this->orderID = $orderID;
	}

	public function proceedPayment(Receipt $oReceipt ) {
		$this->oReceipt = $oReceipt;
		$this->setup();
		$this->insertingNewSession();

		if ( empty($this->paymentID) ){
			Message::error(esc_html__('Could not insert Payment History', 'wiloke-listing-tools'));
		}

		PaymentMetaModel::set($this->paymentID, wilokeListingToolsRepository()->get('addlisting:productIDPaymentID'), $this->oReceipt->productID);

		/*
		 * @hooked EventController@setPlanRelationshipBeforePayment
		 */
		do_action('wiloke-listing-tools/before-payment-process', array(
			'paymentID' => $this->paymentID,
			'planID'    => $this->oReceipt->planID,
			'gateway'   => $this->gateway
		));

		/*
		 * @PaymentStatusController:updatePaymentStatus 5
		 * @PlanRelationshipController:update 5
		 * @WooCommerceController:maybeSaveOldOrderIDIfItIsChangePlanSession 10
		 */
		$aInfo = array(
			'userID'        => User::getCurrentUserID(),
			'status'        => 'pending',
			'gateway'       => $this->gateway,
			'billingType'   => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring'),
			'paymentID'     => $this->paymentID,
			'planID'        => isset($this->oReceipt->aPlan['ID']) ? $this->oReceipt->aPlan['ID'] : '',
			'planRelationshipID' => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionRelationshipStore'))
		);

		do_action('wiloke-listing-tools/payment-pending', $aInfo);
		do_action('wiloke-listing-tools/payment-pending/'.$this->oReceipt->getPackageType(), $aInfo);

		do_action('wiloke-submission/payment-succeeded-and-updated-everything');
		return $aInfo;
	}
}