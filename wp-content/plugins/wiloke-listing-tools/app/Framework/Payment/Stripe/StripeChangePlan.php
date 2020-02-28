<?php

namespace WilokeListingTools\Framework\Payment\Stripe;


use WilokeListingTools\Framework\Payment\AbstractSuspend;
use WilokeListingTools\Framework\Payment\Billable;
use WilokeListingTools\Framework\Payment\Checkout;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class StripeChangePlan extends AbstractSuspend {
	use StripeConfiguration;

	protected $newPaymentID;
	protected $userID;
	private $currentAgreementID;
	private $token;
	private $email;

	public function __construct($userID, $currentPaymentID, $newPlanID, $currentPlanID, $listingType, $token='', $email='') {
		$this->userID = $userID;
		$this->currentPaymentID = $currentPaymentID;
		$this->newPlanID = $newPlanID;
		$this->listingType = $listingType;
		$this->currentPlanID = $currentPlanID;
		$this->token = $token;
		$this->email = $email;
	}

	private function suspendCurrentPlan(){
		$this->setPaymentID($this->currentPaymentID);
		return $this->suspend();
	}

	private function reactivate($subscriptionID, $paymentID){
		if ( empty($subscriptionID) ){
			$subscriptionID = PaymentMetaModel::get($this->currentPaymentID, wilokeListingToolsRepository()->get('payment:stripeSubscriptionID'));
		}

		if ( empty($subscriptionID) ){
			return false;
		}

		try{
			$oSubscription = \Stripe\Subscription::retrieve($subscriptionID);
			$oSubscription->coupon = NULL; // <= It's very important, We will get rid of Free Forever Coupon From this plan
			$oSubscription->save();
			PaymentModel::updatePaymentStatus('active', $paymentID);
			return true;
		}catch (\Exception $oE){
			return false;
		}
	}

	private function maybeReactivatePlan(){
		$this->newPaymentID = PaymentModel::getLastSuspendedByPlan($this->newPlanID, $this->userID);

		if ( empty($this->newPaymentID) ){
			return false;
		}

		$subscriptionID = PaymentMetaModel::get($this->newPaymentID, wilokeListingToolsRepository()->get('payment:stripeSubscriptionID'));

		if ( empty($subscriptionID) ){
			return false;
		}

		return $this->reactivate($subscriptionID, $this->newPaymentID);
	}

	private function changedPlan(){
		/*
		 * UserPlanController@changeUserPlan
		 */
		do_action('wiloke-listing-tools/on-changed-user-plan', array(
			'userID'        => User::getCurrentUserID(),
			'paymentID'     => $this->newPaymentID,
			'oldPaymentID'  => $this->currentPaymentID,
			'oldPlanID'     => $this->currentPlanID,
			'listingType'   => $this->listingType,
			'planID'        => $this->newPlanID,
			'gateway'       => 'stripe',
			'billingType'   => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring')
		));
	}

	public function execute(){
		$this->setApiContext();
		$isPassedSuspended = $this->suspendCurrentPlan();
		if ( !$isPassedSuspended ){
			return array(
				'success' => false,
				'msg'     => esc_html__('We could not suspend the current plan', 'wiloke-listing-tools')
			);
		}

		$isReactivated = $this->maybeReactivatePlan();

		// If we could not renew the plan, We will create new one
		if ( !$isReactivated ){
			new Billable(array(
				'gateway'     => $this->gateway,
				'planID'      => $this->newPlanID,
				'listingType' => $this->listingType
			));

			$aReceiptInfo = array(
				'planID'                => $this->newPlanID,
				'userID'                => User::getCurrentUserID(),
				'couponCode'            => '',
				'isNonRecurringPayment' => false
			);

			if ( !empty($this->token) ){
				$aReceiptInfo['aRequested'] = array(
					'token' => $this->token,
					'email' => $this->email,
				);
			}
			$oReceipt = new Receipt($aReceiptInfo);
			$oReceipt->setupPlan();

			$oPaymentMethod = new StripeRecurringPaymentMethod();

			/*
			 * Set sessions that needed for change plan
			 *
			 * @var newPlanID
			 * @var listingType
			 * @var currentPlanID
			 */
			$this->setSessions();
			$oCheckout = new Checkout();
			$aCheckAcceptPaymentStatus = $oCheckout->begin($oReceipt, $oPaymentMethod);

			if ( $aCheckAcceptPaymentStatus['status'] == 'success' ){
				$this->newPaymentID = $aCheckAcceptPaymentStatus['paymentID'];
				$this->changedPlan();
				return array(
					'status'    => 'success',
					'msg'       => esc_html__('Congratulations! Your plan has been updated successfully.', 'wiloke-listing-tools')
				);
			}else{
				$status = $this->reactivate($this->currentAgreementID, $this->currentPaymentID);
				if ( !$status ){
					return array(
						'status' => 'error',
						'msg'    => esc_html__('We could not upgrade to the new plan. We changed the current plan to Suspend status. Please log into your Stripe and reactivate it manually.', 'wiloke-listing-tools')
					);
				}else{
					return array(
						'status' => 'error',
						'suspendedOldPlan' => true,
						'msg'    => esc_html__('We could not upgrade to the new plan.', 'wiloke-listing-tools')
					);
				}
			}
		}else{
			$this->changedPlan();
			return array(
				'status' => 'success',
				'msg'    => sprintf(esc_html__('Congratulations! The %s has been reactivated successfully.', 'wiloke-listing-tools'), get_the_title($this->newPlanID))
			);
		}
	}
}