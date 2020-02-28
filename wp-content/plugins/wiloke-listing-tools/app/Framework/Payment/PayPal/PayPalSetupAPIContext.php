<?php
namespace WilokeListgoFunctionality\Framework\Payment\PayPal;


abstract class PayPalSetupAPIContext {
	protected $oApiContext;

	public function __construct() {
		$this->setup();
	}

	protected function setup(){
		$instPayPalConfiguration = PayPalConfiguration::setup();
		$this->oApiContext = $instPayPalConfiguration->getApiContext();
	}
}