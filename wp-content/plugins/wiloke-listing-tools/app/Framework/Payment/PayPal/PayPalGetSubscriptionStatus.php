<?php
namespace WilokeListgoFunctionality\Framework\Payment\PayPal;


use PayPal\Api\Agreement;

class PayPalGetSubscriptionStatus{
	protected $requestID;
	protected $oApiContext;

	/**
	 * Set Request ID that returns from each complete payment. In this case, Request ID is your PayPal Agreement ID
	 *
	 * @param string $requestID
	 * @return void
	 */
	public function setRequestID($requestID) {
		$this->requestID = $requestID;
	}

	/**
	 * Setup Payment Configuration
	 *
	 */
	public function setup() {
		$instPayPalConfiguration = PayPalConfiguration::setup();
		$this->oApiContext = $instPayPalConfiguration->getApiContext();
	}

	public function getStatus() {
		$this->setup();
		try {
			$instPlan = Agreement::get($this->requestID, $this->oApiContext);
		} catch (\Exception $ex) {
			return 'empty';
		}

		return ConvertPayPalStatusToWilokeStatus::convert($instPlan->state);
	}
}