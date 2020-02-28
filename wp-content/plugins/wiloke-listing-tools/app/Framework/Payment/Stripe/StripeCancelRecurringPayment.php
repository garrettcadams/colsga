<?php

namespace WilokeListingTools\Framework\Payment\Stripe;


use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class StripeCancelRecurringPayment {
	use StripeConfiguration;

	protected $subscriptionID;

	public function getSubscriptionIDByPaymentID($paymentID){
		$this->subscriptionID = PaymentMetaModel::get($paymentID, wilokeListingToolsRepository()->get('payment:stripeSubscriptionID'));

		if ( empty($this->subscriptionID) ){
			Message::error(esc_html__('The subscription id does not exist', 'wiloke-listing-tools'));
		}

		return $this;
	}

	public function execute($paymentID){
		$this->getSubscriptionIDByPaymentID($paymentID);
		$this->setApiContext();

		try{
			$oSubscription = \Stripe\Subscription::retrieve($this->subscriptionID);
			$oSubscription->cancel();

			PaymentModel::updatePaymentStatus('cancelled', $paymentID);

			return array(
				'status' => 'success',
				'msg'    => $oSubscription
			);
		}catch (\Exception $oE){
			return array(
				'status' => 'error',
				'msg'    => $oE->getMessage()
			);
		}
	}
}