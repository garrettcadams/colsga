<?php

namespace WilokeListingTools\Frontend;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Store\Session;

class GenerateURL {
	public function __construct() {
		add_filter('wilcity/submission/planBtnURL', array($this, 'renderPlanBtnURL'));
		add_filter('wilcity/submission/submitBtnURL', array($this, 'renderSubmitBtnURL'));
	}

	public function renderSubmitBtnURL(){
//		$planID     = Session::getSession(wilokeListingToolsRepository()->get('addlisting:sessionStore'));
		$planID     = Session::getSession(wilokeListingToolsRepository()->get('payment:storePlanID'));
		$productID  = GetSettings::getPostMeta($planID, 'woocommerce_association');

		if ( !empty($productID) ){
			return GetWilokeSubmission::getAddToCardUrl($productID);
		}

		return GetWilokeSubmission::getField('checkout', true);
	}

	public function renderPlanBtnURL($planID){
		return add_query_arg(
			array(
				'planID'    => $planID
			),
			GetWilokeSubmission::getField('addlisting', true)
		);
	}
}