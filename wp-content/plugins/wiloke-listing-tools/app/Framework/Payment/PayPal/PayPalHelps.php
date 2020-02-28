<?php
namespace WilokeListgoFunctionality\Framework\Payment\PayPal;

use PayPal\Api\Agreement;

class PayPalHelps{
	protected static $oApiContext;

	protected static function setup(){
		$instPayPalConfiguration = PayPalConfiguration::setup();
		self::$oApiContext = $instPayPalConfiguration->getApiContext();
	}


	/*
	 * get Billing Agreement Status
	 *
	 * @param string $agreementID
	 * @return string $status
	 */
	public static function billingAgreementStatus($agreementID){
		$instSubscriptionStatus = new PayPalGetSubscriptionStatus;

		$instSubscriptionStatus->setRequestID($agreementID);
		$status = $instSubscriptionStatus->getStatus();

		return $status;
	}

	/**
	 * Get Billing Info By Specifying Agreement ID
	 *
	 * @param string $agreementID
	 * @return array $aStatus
	 */
	public static function getAgreementInfo($agreementID){
		self::setup();
		try {
			$instAgreement = Agreement::get($agreementID, self::$oApiContext);
			return array(
				'status' => 'success',
				'msg'    => $instAgreement
			);
		}catch (\Exception $ex) {
			return array(
				'status' => 'error',
				'msg'    => $ex->getMessage()
			);
		}
	}
}