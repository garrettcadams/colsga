<?php
namespace WilokeListingTools\Framework\Payment\Stripe;

use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Framework\Payment\Coupon;
use WilokeListingTools\Models\UserModel;

trait StripeConfiguration{
	public $gateway = 'stripe';
	public $aConfiguration;
	private $oApiContext;
	protected $customerID;

	private function setApiContext(){
		$this->aConfiguration = GetWilokeSubmission::getAll();
		$msg = esc_html__('The Stripe has not configured yet!', 'wiloke-listing-tools');

		if ( !GetWilokeSubmission::isGatewaySupported($this->gateway) ){
			Message::error($msg);
		}

		$this->oApiContext['secretKey']     = $this->aConfiguration['stripe_secret_key'];
		$this->oApiContext['zeroDecimal']   = !isset($this->aConfiguration['stripe_zero_decimal']) || empty($this->aConfiguration['stripe_zero_decimal']) ? 1 : absint($this->aConfiguration['stripe_zero_decimal']);
		settype($this->oApiContext, 'object');

		\Stripe\Stripe::setApiKey($this->oApiContext->secretKey);
		$this->getCustomerID();
	}

	/**
	 * If user has already executed a session before, We will have his/her customer id
	 *
	 * @return void
	 */
	protected function getCustomerID(){
		$this->customerID = UserModel::getStripeID();
	}

	public function getConfiguration($field){
		return $this->aConfiguration[$field];
	}
}