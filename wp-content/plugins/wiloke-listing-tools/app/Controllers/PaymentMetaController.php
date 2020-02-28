<?php
namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Models\InvoiceModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\UserModel;

class PaymentMetaController extends Controller {
	public function __construct() {
		add_action('wiloke-listing-tools/changed-payment-status', array($this, 'setNewNextBillingDateGMT'), 5);
		add_action('wiloke-listing-tools/subscription-created', array($this, 'setTrial'), 10);
		add_action('wiloke-listing-tools/subscription-created', array($this, 'updateNextBillingDateGMT'), 10);
		add_action('wiloke-listing-tools/payment-renewed', array($this, 'updateNextBillingDateGMT'), 10);
		add_action('wiloke-listing-tools/payment-updated-subscription', array($this, 'updateNextBillingDateGMT'), 10);

		add_action('wp_ajax_extend_next_billing_date', array($this, 'extendNextBillingDate'));
		/**
		 * WooCommerce Subscription
		 */
		add_action('woocommerce_subscription_payment_complete', array($this, 'updateWooCommerceNextBillingDate'), 1, 1);
	}

	public function extendNextBillingDate(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('You do not have permission to access this page', 'wiloke-listing-tools')
				)
			);
		}

		if ( empty($_POST['paymentID']) || empty($_POST['planID']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('The payment ID is required', 'wiloke-listing-tools')
				)
			);
		}

		PaymentModel::updatePaymentStatus('active', $_POST['paymentID']);

		$aPlanSettings = GetSettings::getPlanSettings($_POST['planID']);
		$nextBillingDateGMT = PaymentMetaModel::getNextBillingDateGMT($_POST['paymentID']);
		$plusDays = '+' . $aPlanSettings['regular_period'] . ' day';

		$nextBillingDateGMT = Time::timestampUTC(Time::toAtomUTC($nextBillingDateGMT), $plusDays);


		$aTransactionInfo = PaymentMetaModel::get($_POST['paymentID'], wilokeListingToolsRepository()->get('payment:paymentInfo'));

		InvoiceModel::set(
			$_POST['paymentID'],
			array(
				'currency'      => $aTransactionInfo['currency'],
				'subTotal'      => $aTransactionInfo['subTotal'],
				'discount'      => $aTransactionInfo['discount'],
				'tax'           => $aTransactionInfo['tax'],
				'total'         => $aTransactionInfo['total']
			)
		);

		do_action('wiloke-listing-tools/payment-renewed', array(
			'paymentID'          => $_POST['paymentID'],
			'nextBillingDateGMT' => $nextBillingDateGMT,
			'gateway'            => 'banktransfer',
			'billingType'   => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring')
		));

		wp_send_json_success(
			array(
				'msg' => array(
					'next_billing_date' => Time::toAtom($nextBillingDateGMT)
				)
			)
		);
	}

	public function setNewNextBillingDateGMT($aInfo){
		$billingType = PaymentModel::getField('billingType', $aInfo['paymentID']);
		if ( GetWilokeSubmission::isNonRecurringPayment($billingType) ){
			return false;
		}

		$gateway = PaymentModel::getField('gateway', $aInfo['paymentID']);

		if ( $gateway !== 'banktransfer' ){
			return false;
		}

		if ( $aInfo['newStatus'] !== 'active' && $aInfo['oldStatus'] != 'processing' ){
			return false;
		}

		$aTransactionInfo = PaymentMetaModel::get($aInfo['paymentID'], wilokeListingToolsRepository()->get('payment:paymentInfo'));
		$aPlanSettings = GetSettings::getPlanSettings($aInfo['planID']);

		if ( !empty($aTransactionInfo['discount']) ){
			$nextBillingDateGMT = Time::timestampUTCNow('+' . $aPlanSettings['trial_period'] . ' day');
			PaymentMetaModel::setNextBillingDateGMT($nextBillingDateGMT, $aInfo['paymentID']);
		}else{
			$nextBillingDateGMT = Time::timestampUTCNow('+' . $aPlanSettings['regular_period'] . ' day');
			PaymentMetaModel::setNextBillingDateGMT($nextBillingDateGMT, $aInfo['paymentID']. ' day');
		}
	}

	/*
	 * $aData: nextBillingDateGMT (timestamp), paymentID
	 */
	public function updateNextBillingDateGMT($aData){
		if ( !is_numeric($aData['nextBillingDateGMT']) ){
			date_default_timezone_set('UTC');
			$nextBillingDateGMT = strtotime($aData['nextBillingDateGMT']);
		}else{
			$nextBillingDateGMT = $aData['nextBillingDateGMT'];
		}

		PaymentMetaModel::setNextBillingDateGMT($nextBillingDateGMT, $aData['paymentID']);
	}

	public function setTrial($aData){
		if ( !isset($aData['paymentID']) || GetWilokeSubmission::isNonRecurringPayment($aData['billingType']) || !$aData['isTrial'] ){
			return false;
		}

		PaymentMetaModel::set($aData['paymentID'], wilokeListingToolsRepository()->get('addlisting:isUsingTrial'), true);
	}

	public function updateWooCommerceNextBillingDate(\WC_Subscription $that){
		$aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($that->get_parent_id());

		if ( empty($aPaymentIDs) ){
			return false;
		}

		$nextPayment = $that->get_date('next_payment', 'gmt');

		foreach ($aPaymentIDs as $aPayment){
			$this->updateNextBillingDateGMT(array(
				'nextBillingDateGMT' => strtotime($nextPayment),
				'paymentID' => $aPayment['ID']
			));
		}
	}
}