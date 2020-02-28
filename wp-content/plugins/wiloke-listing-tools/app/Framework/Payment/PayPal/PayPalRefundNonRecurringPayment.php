<?php
namespace WilokeListgoFunctionality\Framework\Payment\PayPal;

use PayPal\Api\Amount;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;

use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Framework\Payment\PayPal\PayPalConfiguration;
use WilokeListingTools\Models\InvoiceModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;


class PayPalRefundNonRecurringPayment {
	use PayPalConfiguration;

	protected $oApiContext;
	protected $saleID;
	protected $oSale;

	protected function getSaleInfo(){
		try {
			$this->oSale = Sale::get($this->saleID, $this->oApiContext);
		} catch (\Exception $ex) {
			Message::error(esc_html__('This sale does not exist', 'wiloke-listing-tools'));
		}

		return $this;
	}

	protected function getSaleIDByPaymentID($paymentID){
		$this->saleID = PaymentMetaModel::get($paymentID, wilokeListingToolsRepository()->get('payment:paypalPaymentID'));
		if ( empty($this->saleID) ){
			Message::error(esc_html__('This session does not exist', 'wiloke-listing-tools'));
		}

		return $this;
	}

	public function execute($paymentID){
		$this->setupConfiguration();
		$this->getSaleIDByPaymentID($paymentID);
		$this->getSaleInfo();

		$invoiceID  = InvoiceModel::getInvoiceIDByPaymentID($paymentID);

		if ( empty($invoiceID) ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('We could not found the invoice of this session', 'wiloke-listing-tools')
			);
		}

		$amount     = InvoiceModel::getField('total', $invoiceID);
		$currency   = InvoiceModel::getField('currency', $invoiceID);

		$oAmount = new Amount();
		$oAmount->setTotal($amount)
		    ->setCurrency($currency);

		$oRefundRequest = new RefundRequest();
		$oRefundRequest->setAmount($oAmount);

		$oSale = new Sale();
		$oSale->setId($this->saleID);

		try {
			$refundedSale = $oSale->refundSale($oRefundRequest, $this->oApiContext);
			PaymentModel::updatePaymentStatus('refunded', $paymentID);

			return array(
				'status' => 'success',
				'msg'    => $refundedSale
			);
		}catch (\Exception $oE) {
			return array(
				'status' => 'error',
				'msg'    => $oE->getMessage()
			);
		}

	}
}