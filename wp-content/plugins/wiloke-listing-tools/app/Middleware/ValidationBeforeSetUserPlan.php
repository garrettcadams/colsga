<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;
use WilokeListingTools\Models\UserModel;

class ValidationBeforeSetUserPlan implements InterfaceMiddleware {
	public $msg;
	public function handle( array $aOptions ) {
		$this->msg = esc_html__('%s is require', 'wiloke-listing-tools');

		$aRequired = array(
			'paymentID',
			'userID',
			'gateway',
			'billingType',
			'planID'
		);

		if ( !GetWilokeSubmission::isNonRecurringPayment($aOptions['billingType']) ){
			$aRequired[] = 'nextBillingDateGMT';
		}

		$instUserModel = $aOptions['instUserModel'];

		foreach ($aRequired as $property){
			$val = $instUserModel->{$property};
			if ( empty($val) ) {
				$this->msg = sprintf($this->msg, $property);
				return false;
			}
		}

		return true;
	}
}