<?php
namespace WilokeListingTools\Framework\Payment\PayPal;


use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Payment\AbstractSuspend;
use WilokeListingTools\Framework\Payment\Billable;
use WilokeListingTools\Framework\Payment\Checkout;
use WilokeListingTools\Framework\Payment\PayPal\PayPalConfiguration;
use WilokeListingTools\Framework\Payment\PayPal\PayPalRecurringPaymentMethod;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Register\WilokeSubmission;

class PayPalChangePlan extends AbstractSuspend{
	use PayPalConfiguration;

	protected $userID;
	protected $newPaymentID;
	private $currentAgreementID;
	private $description = '';

	public function __construct($userID, $currentPaymentID, $newPlanID, $currentPlanID, $listingType) {
		$this->userID = $userID;
		$this->currentPaymentID = $currentPaymentID;
		$this->newPlanID = $newPlanID;
		$this->listingType = $listingType;
		$this->currentPlanID = $currentPlanID;
	}

	private function reactivate($agreementID, $paymentID){
		try {
			$insAgreementStateDescriptor = new AgreementStateDescriptor();
			$insAgreementStateDescriptor->setNote($this->description);

			$instAgreement = Agreement::get($agreementID, $this->oApiContext);
			$instAgreement->reActivate($insAgreementStateDescriptor, $this->oApiContext);
			PaymentModel::updatePaymentStatus('active', $paymentID);
			return true;
		} catch (\Exception $ex) {
			return false;
		}
	}

	private function suspendCurrentPlan(){
		$this->setPaymentID($this->currentPaymentID);
		return $this->suspend();
	}

	private function maybeReactivatePlan(){
		$this->newPaymentID = PaymentModel::getLastSuspendedByPlan($this->newPlanID, $this->userID);
		if ( empty($this->newPaymentID) ){
			return false;
		}

		$agreementID = PaymentMetaModel::get($this->newPaymentID, wilokeListingToolsRepository()->get('addlisting:paypalAgreementID'));

		if ( empty($agreementID) ){
			return false;
		}

		return $this->reactivate($agreementID, $this->newPaymentID);
	}

	public function execute(){
		$this->setupConfiguration();
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

			$oReceipt = new Receipt(array(
				'planID'                => $this->newPlanID,
				'userID'                => User::getCurrentUserID(),
				'couponCode'            => '',
				'isNonRecurringPayment' => false
			));
			$oReceipt->setupPlan();

			$oPayPalMethod = new PayPalRecurringPaymentMethod();

			$oCheckout = new Checkout();
			$aCheckAcceptPaymentStatus = $oCheckout->begin($oReceipt, $oPayPalMethod);

			if ( $aCheckAcceptPaymentStatus['status'] == 'success' ){

				/*
				 * Set sessions that needed for change plan
				 *
				 * @var newPlanID
				 * @var listingType
				 * @var listingType
				 * @var currentPlanID
				 */
				$this->setSessions();
				$this->setChangePlanInfo($aCheckAcceptPaymentStatus['paymentID'], $this->gateway);
				return array(
					'status'    => 'success',
					'redirectTo'=> $aCheckAcceptPaymentStatus['redirectTo'],
					'msg'       => esc_html__('The current plan has been suspended. The new plan will be created in a few seconds.', 'wiloke-listing-tools')
				);
			}else{
				// Reactivate the current plan
				$status = $this->reactivate($this->currentAgreementID, $this->currentPaymentID);
				if ( !$status ){
					return array(
						'status' => 'error',
						'msg'    => esc_html__('We could not upgrade to the new plan. We changed the current plan to Suspend status. Please log into your PayPal and reactivate it manually.', 'wiloke-listing-tools')
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
			/*
			 * PlanRelationshipController@switchListingsBelongsToOldPaymentIDToNewPaymentID 1
			 * UserPlanController@changeUserPlan
			 */
			do_action('wiloke-listing-tools/on-changed-user-plan', array(
				'paymentID'     => $this->newPaymentID,
				'oldPlanID'     => $this->currentPlanID,
				'planID'        => $this->newPlanID,
				'listingType'   => $this->listingType,
				'gateway'       => $this->gateway,
				'billingType'   => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring')
			));


			return array(
				'status' => 'success',
				'msg'    => sprintf(esc_html__('Congratulations! The %s has been reactivated successfully.', 'wiloke-listing-tools'), get_the_title($this->newPlanID))
			);
		}
	}
}