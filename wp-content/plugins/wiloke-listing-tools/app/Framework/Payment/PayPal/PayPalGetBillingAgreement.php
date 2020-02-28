<?php
namespace WilokeListgoFunctionality\Framework\Payment\PayPal;


use PayPal\Api\Agreement;

class PayPalGetBillingAgreement{
	/**
	 * @param string $agreementID
	 * @return object $oAgreement
	 */
	public static function get($agreementID){
		$instPayPalConfiguration = PayPalConfiguration::setup();

		try {
			$oAgreement = Agreement::get($agreementID, $instPayPalConfiguration->getApiContext());
			return $oAgreement;
		} catch (\Exception $ex) {
		   die($ex->getMessage());
		}
	}
}