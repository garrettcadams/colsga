<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PlanRelationshipModel;

class IsExceededFreePlan implements InterfaceMiddleware {
	public $msg = '';

	public function handle( array $aOptions ) {
		if ( GetWilokeSubmission::getField('add_listing_mode') == 'free_add_listing' ){
			return true;
		}

		if ( isset($aOptions['listingID']) && !empty($aOptions['listingID']) ){
			$postStatus = get_post_status($aOptions['listingID']);
			if ( $postStatus != 'unpaid' ){
				return true;
			}
		}

		$aPlanSettings = GetSettings::getPlanSettings($aOptions['planID']);

		if ( empty($aPlanSettings['availability_items']) || !empty($aPlanSettings['regular_price']) ){
			return true;
		}
		$userID = isset($aOptions['userID']) ? $aOptions['userID'] : User::getCurrentUserID();

		$totalListingsSubmitted = PlanRelationshipModel::countListingsUserSubmittedInPlan($aOptions['planID'], $userID);
		if ( $totalListingsSubmitted >= abs($aPlanSettings['availability_items']) ){
			$this->msg = esc_html__('Oops! You exceeded the number of free listings of this plan', 'wiloke-listing-tools');
			return false;
		}
		return true;
	}
}