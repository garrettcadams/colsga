<?php

namespace WilokeListingTools\Framework\Payment\Stripe;


use Stripe\Refund;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Models\InvoiceModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class StripeRefundNonRecurringPayment {
	use StripeConfiguration;

	protected $chargeID;

	public function getChargedID($paymentID){
		$this->chargeID = PaymentMetaModel::get($paymentID, wilokeListingToolsRepository()->get('payment:stripeChargedID'));

		if ( empty($this->chargeID) ){
			Message::error(esc_html__('The sale id does not exist', 'wiloke-listing-tools'));
		}

		return $this;
	}

	public function execute($paymentID){
		$this->getChargedID($paymentID);
		$this->setApiContext();
		$invoiceID = InvoiceModel::getInvoiceIDByPaymentID($paymentID);

		if ( empty($invoiceID) ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('We could not found the invoice of this session', 'wiloke-listing-tools')
			);
		}
		$amount = InvoiceModel::getField('total', $invoiceID);

		try{
			Refund::create(
				array(
					'charge' => $this->chargeID,
					'amount' => $amount*$this->oApiContext->zeroDecimal,
					'reason' => 'requested_by_customer'
				)
			);

			PaymentModel::updatePaymentStatus('refunded', $paymentID);
			return array(
				'status' => 'success'
			);
		}catch (\Exception $oE){
			return array(
				'status' => 'error',
				'msg'    => $oE->getMessage()
			);
		}
	}
}