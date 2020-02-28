<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Models\UserModel;

trait SetPostDuration {
	private function setDuration($billingType, $postID, $planID, $isTrial=false){
		$aPlanSettings = GetSettings::getPlanSettings($planID);
		if ( GetWilokeSubmission::isNonRecurringPayment($billingType) ){
			$duration = $aPlanSettings['regular_period'];
			SetSettings::setPostMeta($postID, 'duration', $duration);
		}else{
			$aUserPlan = UserModel::getSpecifyUserPlanID($planID);
			if ( is_array($aUserPlan) && !empty($aUserPlan['nextBillingDateGMT']) ){
				SetSettings::setPostMeta($postID, 'durationTimestampUTC', $aUserPlan['nextBillingDateGMT']);
			}else{
				$duration = $isTrial ? $aPlanSettings['trial_period'] : $aPlanSettings['regular_period'];
				SetSettings::setPostMeta($postID, 'duration', $duration);
			}
		}
	}
}