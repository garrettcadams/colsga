<?php
namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Models\PaymentModel;

class PaymentStatusController extends Controller {
	public function __construct() {
		add_action('wiloke-listing-tools/woocommerce/after-order-succeeded', array($this, 'updateWooCommercePaymentsStatus'), 5);
		add_action('wiloke-listing-tools/payment-failed', array($this, 'updateFailedStatus'), 5);
		add_action('wiloke-listing-tools/payment-succeeded', array($this, 'updateSucceededStatus'), 5);
	}

	public function updateWooCommercePaymentsStatus($aResponse){
		$aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($aResponse['orderID']);
		if ( empty($aPaymentIDs) ){
			return false;
		}

		foreach ($aPaymentIDs as $aPaymentID){
			PaymentModel::updatePaymentStatus('succeeded', $aPaymentID['ID']);
		}
	}

	public function updateFailedStatus($aData){
		PaymentModel::updatePaymentStatus('failed', $aData['paymentID']);
	}

	public function updateSucceededStatus($aData){
		PaymentModel::updatePaymentStatus($aData['status'], $aData['paymentID']);
	}
}