<?php
namespace WilokeListgoFunctionality\Framework\Payment\PayPal;


use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use WilokeListgoFunctionality\Framework\Payment\PayPal\PayPalConfiguration;

class PayPalReactivePlan {
	protected $descriptor = 'Reactivating the agreement';
	protected $agreementID;
	protected $oApiContext;

	public function __construct($agreementID) {
		$this->descriptor  = apply_filters('wiloke-submission/app/payment/paypal/PayPalReactivePlan/descriptor', $this->descriptor);
		$this->agreementID = $agreementID;
		$this->setup();
	}

	/**
	 * Setup Payment Configuration
	 *
	 */
	protected function setup() {
		$instPayPalConfiguration = PayPalConfiguration::setup();
		$this->oApiContext = $instPayPalConfiguration->getApiContext();
	}

	public function processReactivating(){
		//Create an Agreement State Descriptor, explaining the reason to suspend.
		$insAgreementStateDescriptor = new AgreementStateDescriptor();
		$insAgreementStateDescriptor->setNote($this->descriptor);
		$instAgreement = Agreement::get($this->agreementID, $this->oApiContext);

		try {
			$instAgreement->reActivate($insAgreementStateDescriptor, $this->oApiContext);
			$oNewAgreementInfo = Agreement::get($this->agreementID, $this->oApiContext);
			return array(
				'status'  => 'success',
				'msg'     => $oNewAgreementInfo
			);
		} catch (\Exception $ex) {
			return array(
				'status'  => 'error',
				'msg'     => $ex->getMessage()
			);
		}
	}
}