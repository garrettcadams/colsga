<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\PlanRelationshipModel;

trait SetPlanRelationship {

	/*
	 * @param array $aInfo: planID, objectID, userID, paymentID
	 */
	protected function setPlanRelationship($aUserPlan, $aInfo){
		if ( empty($aUserPlan) ){
			$aInfo['paymentID'] = 0;
		}else{
			$aInfo['paymentID'] = $aUserPlan['paymentID'];
			$status = PaymentModel::getField('status', $aInfo['paymentID']);
			if ( $status !== 'active' && $status !== 'succeeded' ){
				$aInfo['paymentID'] = 0;
			}
		}
		$planRelationshipID = PlanRelationshipModel::setPlanRelationship($aInfo);
		if ( !empty($planRelationshipID) ){
			Session::setSession(wilokeListingToolsRepository()->get('payment:sessionRelationshipStore'), $planRelationshipID);
		}

		return $planRelationshipID;
	}
}