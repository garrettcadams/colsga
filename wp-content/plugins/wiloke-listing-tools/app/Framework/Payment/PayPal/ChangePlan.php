<?php
namespace WilokeListingTools\Framework\Payment\PayPal;


use WilokeListingTools\Framework\Payment\PayPal\PayPalSuspendPlan;
use WilokeListingTools\Framework\Payment\Checkout;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\s;
use WilokeListingTools\Models\UserModel;
use WilokeListingTools\Submit\User;

class ChangePlan {
	protected $newPlanID;
	protected $oldPlanID;
	protected $userID;
	private $oldAgreementID;
	private $newAgreementID;
	protected $isCreatNewPlan = true;
	protected $oldSessionID;
	protected $newSessionID;

	public function __construct($userID, $planID) {
		$this->userID = $userID;
		$this->newPlanID = $planID;
	}

	public function processChangingPlan(){
		// Before we gonna do it, We have to switch the current plan to suspend status first
		$oldAgreementStatus = $this->getBillingStatusOfTheCurrentPlan();

		if ( $oldAgreementStatus == 'active' ){
			$instPayPalSuspendPlan = new PayPalSuspendPlan($this->oldAgreementID);
			$aSuspendStatus = $instPayPalSuspendPlan->processSuspending();
			if ( $aSuspendStatus['status'] != 'success' ){
				return array(
					'status' => 'error',
					'msg'    => esc_html__('The changing plan was failure. Error: We could not change the current plan to suspend status', 'wiloke')
				);
			}else{
				// Updating Payment Status
				PaymentModel::updateToSuspendedStatusWhereEqualToSessionID($this->oldSessionID);
				PaymentMetaModel::update($this->oldSessionID, wilokeRepository('paymentKeys:info'), $aSuspendStatus['msg']);
			}
		}

		$agreementStatus = $this->getBillingStatusOfTheNewPlan();

		//If this plan is already created before, We will reactivated it
		if ( $agreementStatus == wilokeRepository('app:paymentStatus', true)->sub('suspended') ){
			$instPayPalRenewPlan = new PayPalReactivePlan($this->newAgreementID);
			$aReactivationStatus = $instPayPalRenewPlan->processReactivating();

			// If we could not renew the plan, We will create new one
			if ( $aReactivationStatus['status'] == 'success' ){
				PaymentMetaModel::update($this->newSessionID, wilokeRepository('paymentKeys:info'), $aReactivationStatus['msg']);

				if ( !empty($this->oldPlanID) ){
					UserModel::removeUserPlanByPlanID($this->userID, $this->oldPlanID);
				}

				/**
				 * @hook PlanRelationship@updateSessionID 5
				 * @hook UserController@createUserPlan 10
				 */
				do_action('wiloke/wiloke-submission/payment/after_payment', array(
					'gateway'            => 'paypal',
					'status'             => 'succeeded',
					'billingType'        => wilokeRepository('app:billingTypes', true)->sub('recurring'),
					'sessionID'          => $this->newSessionID,
					'planID'             => $this->newPlanID,
					'planName'           => get_the_title($this->newPlanID),
					'nextBillingDate'    => strtotime($aReactivationStatus['msg']->agreement_details->next_billing_date),
					'postID'             => '',
					'planRelationshipID' => ''
				));

				return array(
					'status' => 'success',
					'msg'    => esc_html__('Congratulations! Your plan has been reactivated successfully!', 'wiloke')
				);
			}
		}

		// If it's first time you use this plan, We will create a new billing plan
		$aData = array(
			'planID'    => $this->newPlanID,
			'couponID'  => ''
		);
		$oReceipt = new Receipt($aData);
		$oPayPalMethod = new PayPalRecurringPaymentMethod();
		$oCheckout = new Checkout();

		/*
		 * Referring PayPalRecurringPayment.php to get more
		 */
		$aCheckAcceptPaymentStatus = $oCheckout->begin($oReceipt, $oPayPalMethod);

		return $aCheckAcceptPaymentStatus;
	}
	/**
	 * Check whether this plan contain an agreement ID or not
	 */
	protected function getBillingStatusOfTheNewPlan(){
		$lastSuspendedSessionID = PayPalModel::getUserLastSuspendedSessionIDEnqualToPlanID($this->userID, $this->newPlanID);
		$this->newSessionID = abs($lastSuspendedSessionID);
		if ( $this->newSessionID ){
			$this->newAgreementID = PayPalModel::getAgreementID($this->newSessionID);
			$agreementStatus = PayPalHelps::billingAgreementStatus($this->newAgreementID);

			return $agreementStatus;
		}

		return false;
	}

	/**
	 * Get current Plan Key
	 */
	protected function getBillingStatusOfTheCurrentPlan(){
		$aCurrentPlan = UserModel::getUserPlansByPlanType($this->userID, get_post_type($this->newPlanID));
		if ( empty($aCurrentPlan) ){
			return false;
		}

		$aPlanKeys = array_keys($aCurrentPlan);
		$planID = end($aPlanKeys);
		$aPlanSettings = $aCurrentPlan[$planID];

		if ( isset($aPlanSettings['sessionID']) && !empty($aPlanSettings['sessionID']) ){
			$this->oldSessionID = abs($aPlanSettings['sessionID']);
			$this->oldAgreementID = PayPalModel::getAgreementID($this->oldSessionID);
			$this->oldPlanID = $planID;
			$agreementStatus = PayPalHelps::billingAgreementStatus($this->oldAgreementID);

			return $agreementStatus;
		}

		return false;
	}
}