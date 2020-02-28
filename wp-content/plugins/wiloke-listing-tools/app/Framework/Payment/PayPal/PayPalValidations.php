<?php
namespace WilokeListingTools\Framework\Payment\PayPal;


use WilokeListingTools\Framework\Store\Session;

trait PayPalValidations{
	function validateBeforeExecuting(){
		if ( !isset($_GET['token'])  ){
			return false;
		}

		$storeTokenPlanSession = wilokeListingToolsRepository()->get('sessionkeys:storeTokenPlanSession');
		$aStoreTokenPlan = Session::getSession($storeTokenPlanSession);
		if ( empty($aStoreTokenPlan) || !is_array($aStoreTokenPlan) ){
			return false;
		}

		if ( !array_key_exists($_GET['token'], $aStoreTokenPlan) ){
			return false;
		}

//		Session::destroySession($storeTokenPlanSession);
		return true;
	}
}