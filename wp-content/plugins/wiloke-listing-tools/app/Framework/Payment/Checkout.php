<?php
namespace WilokeListingTools\Framework\Payment;


class Checkout{
	private $oReceipt = null;

	public function begin( $oReceipt, PaymentMethodInterface $oPaymentMethod){
		$this->oReceipt = $oReceipt;
		return $oPaymentMethod->proceedPayment( $oReceipt);
	}
}