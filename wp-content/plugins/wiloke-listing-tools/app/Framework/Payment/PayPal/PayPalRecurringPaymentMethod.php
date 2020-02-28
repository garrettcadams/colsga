<?php
namespace WilokeListingTools\Framework\Payment\PayPal;

use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Payment\Coupon;
use WilokeListingTools\Framework\Payment\PaymentMethodInterface;
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Exception\PayPalConfigurationException;
use PayPal\Exception\PayPalInvalidCredentialException;
use PayPal\Exception\PayPalMissingCredentialException;

use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\ShippingAddress;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;


class PayPalRecurringPaymentMethod implements PaymentMethodInterface{
	use PayPalGenerateUrls;
	use PayPalConfiguration;

	protected $storeTokenPlanSession;
	protected $oReceipt;
	protected $thankyouUrl = null;
	protected $cancelUrl = null;
	public $token = null;
	private $planID = null;
	private $initTotal;

	public function getBillingType() {
		return wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring');
	}

	protected function setup(){
		$this->cancelUrl();
		$this->thankyouUrl();
		$this->setupConfiguration();
	}

	protected function parseTokenFromApprovalUrl($approvalUrl){
		$aParseData = explode('token=', $approvalUrl);
		$this->token = trim($aParseData[1]);
	}

	protected function createPlan(){
		// Create a new billing plan
		$plan = new Plan();

		$plan->setName($this->oReceipt->aPlan['planName'])
		     ->setDescription($this->paymentDescription)
		     ->setType('INFINITE');
		$this->initTotal = $this->oReceipt->aPlan['total'];

		// Set billing plan definitions
		$aPaymentDefinitions = array();
		$paymentDefinition = new PaymentDefinition();
		$paymentDefinition->setName($this->oReceipt->aPlan['planName'])
		                  ->setType('REGULAR')
		                  ->setFrequency('DAY')
		                  ->setFrequencyInterval($this->oReceipt->aPlan['regularPeriod'])
		                  ->setAmount(new Currency(array('value' => $this->oReceipt->aPlan['total'], 'currency' => $this->aConfiguration['currency_code'])));
		// Trial
		if ( !empty($this->oReceipt->aPlan['trialPeriod']) ){
			$paymentTrialDefinition = new PaymentDefinition();
			$paymentTrialDefinition->setName(esc_html__('Trial', 'wiloke-listing-tools') . ' ' . $this->oReceipt->aPlan['planName'])
			                       ->setType('TRIAL')
			                       ->setFrequency('DAY')
			                       ->setFrequencyInterval($this->oReceipt->aPlan['trialPeriod'])
			                       ->setCycles(1)
			                       ->setAmount(new Currency(array('value' => 0, 'currency' => $this->aConfiguration['currency_code'])));
			Session::setSession(wilokeListingToolsRepository()->get('addlisting:storeIsTrial'), true);
			$aPaymentDefinitions[] = $paymentTrialDefinition;
		}

		// Set merchant preferences
		$merchantPreferences = new MerchantPreferences();
		$merchantPreferences->setReturnUrl($this->thankyouUrl)
		                    ->setCancelUrl($this->cancelUrl)
		                    ->setAutoBillAmount('yes')
		                    ->setInitialFailAmountAction('CANCEL')
		                    ->setMaxFailAttempts($this->maxFailedPayments);

		if ( !empty($this->aConfiguration['initial_fee']) ){
			$merchantPreferences->setSetupFee(new Currency(array('value' => $this->aConfiguration['initial_fee'], 'currency' => $this->aConfiguration['currency_code'])));
		}
		$aPaymentDefinitions[] = $paymentDefinition;

		$plan->setPaymentDefinitions($aPaymentDefinitions);
		$plan->setMerchantPreferences($merchantPreferences);

		//create plan
		try {
			$createdPlan = $plan->create($this->oApiContext);

			try {
				$patch = new Patch();
				$value = new PayPalModel('{"state":"ACTIVE"}');
				$patch->setOp('replace')
				      ->setPath('/')
				      ->setValue($value);
				$patchRequest = new PatchRequest();
				$patchRequest->addPatch($patch);
				$createdPlan->update($patchRequest, $this->oApiContext);
				$plan = Plan::get($createdPlan->getId(), $this->oApiContext);

				// Output plan id
				$this->planID = $plan->getId();

				return array(
					'status' => 'success',
					'msg'    => '',
					'planID' => $plan->getId()
				);
			} catch (PayPalConnectionException $ex) {
				return array(
					'code'   => $ex->getCode(),
					'status' => 'error',
					'msg'    => $ex->getMessage()
				);
			} catch (\Exception $ex) {
				return array(
					'status' => 'error',
					'msg'    => $ex->getMessage()
				);
			}
		} catch (PayPalConnectionException $ex) {
			return array(
				'code'   => $ex->getCode(),
				'status' => 'error',
				'msg'    => $ex->getMessage()
			);
		} catch (\Exception $ex) {
			return array(
				'status' => 'error',
				'msg'    => $ex->getMessage()
			);
		}
	}

	protected function createBillingAgreement(){
		// Create new agreement
		$instAgreement = new Agreement();
		$instAgreement->setName($this->paymentDescription)
		              ->setDescription($this->paymentDescription)
		              ->setStartDate(Time::iso8601StartDate());
		// Set plan id
		$plan = new Plan();
		$plan->setId($this->planID);
		$instAgreement->setPlan($plan);

		// Add payer type
		$payer = new Payer();
		$payer->setPaymentMethod('paypal');
		$instAgreement->setPayer($payer);
		// Adding shipping details
//		if ( $instShippingAddress = $this->setShippingAddress() ){
//			$instAgreement->setShippingAddress($instShippingAddress);
//		}

		try {
			// Create agreement
			$instAgreement = $instAgreement->create($this->oApiContext);
			// Extract approval URL to redirect user

			$approvalUrl = $instAgreement->getApprovalLink();
			$this->parseTokenFromApprovalUrl($approvalUrl);

			// Insert wiloke_submission_transaction and wiloke_submission_paypal_recurring_payment before redirecting to PayPal
			$paymentID = PaymentModel::setPaymentHistory($this, $this->oReceipt);

			if ( empty($paymentID) ){
				Message::error(esc_html__('Could not insert Payment History', 'wiloke-listing-tools'));
			}

			if ( empty($paymentID) ){
				return array(
					'status' => 'error',
					'msg'    => esc_html__('We could not create this session', 'wiloke-listing-tools')
				);
			}else{
				$this->storeTokenPlanSession = wilokeListingToolsRepository()->get('payment:paypalTokenAndStoreData');
				$status = $this->storeTokenAndPlanId($paymentID);
				if ( !$status ){
					return array(
						'status' => 'error',
						'msg'    => esc_html__('We could not save Token, Plan Relationship.', 'wiloke-listing-tools')
					);
				}

				/*
				 * @hooked EventController@setPlanRelationshipBeforePayment
				 */
				$aResponse = array(
					'paymentID' => $paymentID,
					'planID'    => $this->oReceipt->planID,
					'gateway'   => $this->gateway
				);

				do_action('wiloke-listing-tools/before-payment-process/'.$this->oReceipt->getPackageType(), $aResponse);
				do_action('wiloke-listing-tools/before-payment-process', $aResponse);

				return array(
					'status'    => 'success',
					'paymentID' => $paymentID,
					'msg'       => esc_html__('Got Approval url', 'wiloke-listing-tools'),
					'next'      => $instAgreement->getApprovalLink(),
					'redirectTo'=> $instAgreement->getApprovalLink()
				);
			}
		} catch (PayPalConnectionException $ex) {
			return array(
				'code'   => $ex->getCode(),
				'status' => 'error',
				'msg'    => $ex->getMessage()
			);
		} catch (PayPalInvalidCredentialException $ex){
			return array(
				'code'   => $ex->getCode(),
				'status' => 'error',
				'msg'    => $ex->errorMessage()
			);
		} catch (\Exception $ex) {
			return array(
				'status' => 'error',
				'msg'    => $ex->getMessage()
			);
		}
	}

	public function proceedPayment(Receipt $oReceipt){
		$this->oReceipt = $oReceipt;
		$this->setup();
		$aResult = $this->createPlan();
		if ( $aResult['status'] == 'success' ){
			$aResult = $this->createBillingAgreement();
			return $aResult;
		}else{
			return $aResult;
		}
	}
}
