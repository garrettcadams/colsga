<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsPlanExists implements InterfaceMiddleware {
	public $msg;
	public function handle( array $aOptions ) {
		$this->msg = esc_html__('ERROR: This plan does not exist.', 'wiloke-listing-tools');
		if ( isset($aOptions['planType']) && !empty($aOptions['planType']) ){
			$planKey = $aOptions['planType'];
		}else if ( isset($aOptions['listingType']) && !empty($aOptions['listingType']) ){
			$planKey = $aOptions['listingType'] . '_plans';
		}else{
			$planKey = '';
		}

		if ( empty($planKey) || !isset($aOptions['planID']) || empty($aOptions['planID']) ){
			return false;
		}

		if ( get_post_field('post_status', $aOptions['planID']) == 'publish' ){
			if ( in_array(get_post_status($aOptions['listingID']), array('expired', 'editing', 'publish')) ){
				return true;
			}
		}

		$aCustomerPlans = GetWilokeSubmission::getField($planKey);

		if ( empty($aCustomerPlans) ){
			return false;
		}

		$aCustomerPlans = explode(',', $aCustomerPlans);
		if ( !in_array($aOptions['planID'], $aCustomerPlans) || (get_post_field('post_status', $aOptions['planID']) != 'publish') ){
			return false;
		}

		return true;
	}
}
