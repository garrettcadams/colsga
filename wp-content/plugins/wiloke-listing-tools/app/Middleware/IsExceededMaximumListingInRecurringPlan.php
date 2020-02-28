<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsExceededMaximumListingInRecurringPlan implements InterfaceMiddleware {
	public $msg;
	public function handle( array $aOptions ) {
		if ( !isset($aOptions['aUserPlan']) || empty($aOptions['aUserPlan']) ){
			return true;
		}

		if ( GetWilokeSubmission::isNonRecurringPayment($aOptions['aUserPlan']['billingType']) ){
			return true;
		}

		if ( $aOptions['aUserPlan']['remainingItems'] <= 0 ){
			$this->msg = esc_html__('You have reached the maximum of availability listings in this plan. Please go to your Dashboard, then navigate to Billing and upgrade to higher plan.', 'wiloke-listing-tools');
			return false;
		}

		return true;
	}
}