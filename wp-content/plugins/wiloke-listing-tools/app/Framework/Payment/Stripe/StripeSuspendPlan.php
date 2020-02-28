<?php

namespace WilokeListingTools\Framework\Payment\Stripe;


use Stripe\Coupon;
use Stripe\Stripe;
use WilokeListingTools\Framework\Payment\SuspendInterface;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class StripeSuspendPlan implements SuspendInterface {
	use StripeConfiguration;

	protected $paymentID;
	protected $subscriptionID;

	public function setPaymentID($paymentID){
		$this->paymentID = $paymentID;
	}

	public function suspend(){
		$status = PaymentModel::getField('status', $this->paymentID);
		if ( $status !== 'active' ){
			return true;
		}
		$this->setApiContext();
		$this->subscriptionID = PaymentMetaModel::get($this->paymentID, wilokeListingToolsRepository()->get('payment:stripeSubscriptionID'));

		if ( empty($this->subscriptionID) ){
			return true;
		}

		$this->createFreeForeverCoupon();
		try{
			$oSubscription = \Stripe\Subscription::retrieve($this->subscriptionID);
			$oSubscription->coupon = wilokeListingToolsRepository()->get('payment:stripeForeverCoupon');
			$oSubscription->save();

			PaymentModel::updatePaymentStatus('suspended', $this->paymentID);
			return true;
		}catch (\Exception $e){
			return false;
		}
	}

	protected function createFreeForeverCoupon(){
		Stripe::setApiKey($this->oApiContext->secretKey);

		try{
			Coupon::retrieve(wilokeListingToolsRepository()->get('payment:stripeForeverCoupon'));
		}catch (\Exception $oE){
			try{
				Coupon::create(array(
					'id'            => wilokeListingToolsRepository()->get('payment:stripeForeverCoupon'),
					'duration'      => 'forever',
					'percent_off'   => 100,
				));

				return true;

			}catch (\Exception $oE){
				return false;
			}

		}
	}
}