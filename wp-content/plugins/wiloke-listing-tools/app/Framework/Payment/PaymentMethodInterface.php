<?php
namespace WilokeListingTools\Framework\Payment;

interface PaymentMethodInterface{
	public function proceedPayment(Receipt $receipt);
	public function getBillingType();
}