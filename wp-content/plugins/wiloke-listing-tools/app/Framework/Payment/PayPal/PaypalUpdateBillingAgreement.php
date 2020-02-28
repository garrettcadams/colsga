<?php
namespace WilokeListgoFunctionality\Framework\Payment\PayPal;

use PayPal\Api\Agreement;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;

class PaypalUpdateBillingAgreement {
	protected $oApiContext;

	public function test($agreementID){
		$createdAgreement = Agreement::get($agreementID);

		$instPayPalConfiguration = PayPalConfiguration::setup();
		$this->oApiContext = $instPayPalConfiguration->getApiContext();

		$patch = new Patch();
		$patch->setOp('replace')
		      ->setPath('/')
		      ->setValue(json_decode('{
		            "description": "New Description",
		            "shipping_address": {
		                "line1": "2065 Hamilton Ave",
		                "city": "San Jose",
		                "state": "CA",
		                "postal_code": "95125",
		                "country_code": "US"
		            }
                }'));
		$patchRequest = new PatchRequest();
		$patchRequest->addPatch($patch);
		try {
			$createdAgreement->update($patchRequest, $this->oApiContext);
			// Lets get the updated Agreement Object
			$agreement = Agreement::get($createdAgreement->getId(), $this->oApiContext);
			var_export($agreement);die();
		} catch (\Exception $ex) {
			var_export($ex->getMessage());die();
		}
	}
}