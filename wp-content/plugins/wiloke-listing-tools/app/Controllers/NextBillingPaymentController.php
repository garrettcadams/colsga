<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class NextBillingPaymentController {
	public function __construct() {
		add_action('woocommerce_subscription_payment_complete', array($this, 'wooSubscriptionUpdateNextBillingDate'));
	}


	/*
	 * Update Next Billing Date to Payment Meta
	 *
	 * @var $paymentID Wiloke Payment ID
	 * @var $nextBillingDateUTC Timestamp UTC
	 */
	private function updateNextBillingDate($paymentID, $nextBillingDateUTC){
		PaymentMetaModel::setNextBillingDateGMT($paymentID, $nextBillingDateUTC);
	}

	/*
	 * Get Next Billing Date and then insert to Payment Meta
	 *
	 * @since 1.2.0
	 */
	public function wooSubscriptionUpdateNextBillingDate(\WC_Subscription $that){
		$aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($that->get_parent_id());
		if ( empty($aPaymentIDs) ){
			return false;
		}

		$nextPaymentUTC = $that->get_date('next_payment', 'GMT');

		foreach ($aPaymentIDs as $aPaymentID) {
			$this->updateNextBillingDate($aPaymentID['ID'], $nextPaymentUTC);
		}
	}
}