<?php

namespace WilokeListingTools\Framework\Payment\PayPal;


use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\PlanRelationshipModel;
use WilokeListingTools\Framework\UserPlan\GetUserPlan;
use WilokeListingTools\Framework\UserPlan\RemainingItemsInNonRecurringPaymentInterface;

class RemainingItemsInNonRecurringPayPal extends GetUserPlan{
	protected $userID;
	protected $planID;
	protected $sessionID;
	protected $unlimitedItems = 100000;

	/**
	 * @param array $aUserInfo: It contains userID and planID
	 */
	public function __construct(array $aUserInfo) {
		$this->setUserID($aUserInfo['userID']);
		$this->setPlanID($aUserInfo['planID']);
		$this->setSessionID($aUserInfo['sessionID']);
		$this->getUserPlan();
		$this->getPlanType();
		$this->getPlan();
		$this->getBilledDate();
		$this->getPlanSettings();
	}

	public function setUserID( $userID ) {
		$this->userID = $userID;
	}

	public function setPlanID( $planID ) {
		$this->planID = $planID;
	}

	public function setSessionID( $sessionID ) {
		$this->sessionID = $sessionID;
	}

	/**
	 * Calculating the remaining item
	 *
	 * @return number $remainingItems
	 */
	public function remainingItems(){
		$status = PaymentModel::getSessionStatus($this->sessionID);
		if ( $status != 'succeeded' ){
			return 0;
		}

		$maximumAllowableItems = abs($this->aPlanSettings['number_of_posts']);
		if ( empty($maximumAllowableItems) ){
			return $this->unlimitedItems;
		}

		$countItemsUsed = PlanRelationshipModel::countItemsUsedBySessionIDAndPlanID($this->userID,  $this->sessionID, $this->planID);
		return $maximumAllowableItems - $countItemsUsed;
	}
}