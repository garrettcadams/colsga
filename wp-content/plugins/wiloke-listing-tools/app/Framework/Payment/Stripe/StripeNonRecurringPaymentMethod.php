<?php
namespace WilokeListingTools\Framework\Payment\Stripe;

use Stripe\Coupon;
use Stripe\Customer;
use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Framework\Payment\PaymentMethodInterface;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Models\InvoiceModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\UserModel;

class StripeNonRecurringPaymentMethod implements PaymentMethodInterface {
	use StripeConfiguration;
	protected $storeTokenPlanSession;
	protected $aPaymentInfo;
	protected $oCharge;
	protected $relationshipID;
	protected $userID;
	protected $paymentID;
	protected $oReceipt;

	public function getBillingType() {
		return wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring');
	}

	protected function setup(){
		$this->token    = $this->oReceipt->aInfo['token'];
		$this->userID   = get_current_user_id();
		$this->setApiContext();
	}

	private function insertingNewSession(){
		$this->paymentID = PaymentModel::setPaymentHistory($this, $this->oReceipt);
	}

	public function proceedPayment(Receipt $oReceipt){
		$this->setup();
		$this->oReceipt = $oReceipt;

		//if ( empty($this->customerID) ){
			try{
				$oCustomer = Customer::create(array(
					'email'  => $this->oReceipt->aRequested['email'],
					'source' => $this->oReceipt->aRequested['token']
				));

				UserModel::setStripeID($oCustomer->id, $this->userID);
			}catch (\Exception $oE){
				return array(
					'status' => 'error',
					'msg'    => $oE->getMessage()
				);
			}
//		}else{
//			try{
//				$oCustomer = Customer::retrieve($this->customerID);
//			}catch (\Exception $oE){
//				UserModel::deleteStripeID($this->userID);
//				return array(
//					'status' => 'error',
//					'msg'    => esc_html__('The customer ID was deleted. Please try to refresh browser to create another ID', 'wiloke-listing-tools')
//				);
//			}
//		}

		return $this->charge($oCustomer);
	}

	protected function isCouponExists(){
		try{
			Coupon::retrieve($this->oReceipt->aCouponInfo['slug']);
			return true;
		}catch (\Exception $oException){
			return false;
		}
	}

	protected function charge($oCustomer){
		try {
			$this->insertingNewSession();

			if ( empty($this->paymentID) ){
				Message::error(esc_html__('Could not insert Payment History', 'wiloke-listing-tools'));
			}

			if ( isset($this->oReceipt->aCouponInfo['discountPrice']) && !empty($this->oReceipt->aCouponInfo['discountPrice']) ){
				$amount = $this->oReceipt->subTotal - $this->oReceipt->aCouponInfo['discountPrice'];
				$amount = floatval($amount);
				$discountPrice = -$this->oReceipt->aCouponInfo['discountPrice'];
			}else{
				$amount = $this->oReceipt->total;
				$discountPrice = 0;
			}

			/*
			 * @hooked EventController@setPlanRelationshipBeforePayment
			 */
			do_action('wiloke-listing-tools/before-payment-process', array(
				'paymentID' => $this->paymentID,
				'planID'    => $this->oReceipt->planID,
				'gateway'   => $this->gateway
			));

			$oCharge = \Stripe\Charge::create(array(
				'amount'        => $this->oApiContext->zeroDecimal*$amount,
				'currency'      => $this->aConfiguration['currency_code'],
				'description'   => $this->oReceipt->aPlan['planName'],
				'customer'      => $oCustomer->id
			));

			PaymentMetaModel::set($this->paymentID, wilokeListingToolsRepository()->get('payment:paymentInfo'), $oCharge->__toArray());
			PaymentMetaModel::set($this->paymentID, wilokeListingToolsRepository()->get('payment:stripeChargedID'), $oCharge->id);

			/*
			 * @PaymentStatusController:updatePaymentStatus 5
			 * @PlanRelationshipController:update 5
			 * @UserPlanController:setUserPlan 10
			 * @ClaimListingController:paidClaimSuccessfully 10 // Paid Claim
			*/
//			$category = Session::getSession(wilokeListingToolsRepository()->get('payment:category'));

			$category = Session::getSession(wilokeListingToolsRepository()->get('payment:category'), true);
			$category = empty($category) ? 'dadadzzdadad' : $category;
			$aResponse = apply_filters('wiloke-listing-tools/framework/payment/response', array(
				'status'        => 'succeeded',
				'gateway'       => $this->gateway,
				'billingType'   => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring'),
				'paymentID'     => $this->paymentID,
				'planID'        => isset($this->oReceipt->aPlan['ID']) ? $this->oReceipt->aPlan['ID'] : '',
				'planRelationshipID' => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionRelationshipStore'), true),
				'postID'        => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'), true),
				'userID'        => get_current_user_id(),
				'claimID'       => Session::getSession(wilokeListingToolsRepository()->get('claim:sessionClaimID'), true),
				'category'      => $category
			));

			do_action('wiloke-listing-tools/payment-succeeded/'.$category, $aResponse);
			do_action('wiloke-listing-tools/payment-succeeded/'.$this->oReceipt->getPackageType(), $aResponse);
			do_action('wiloke-listing-tools/payment-succeeded', $aResponse);

			if ( $this->oReceipt->aCouponInfo['discountPrice'] != '00.00' ){
				$this->oReceipt->aCouponInfo['discountPrice'] = -$this->oReceipt->aCouponInfo['discountPrice'];
			}

			do_action('wilcity/stripe/insert-invoice', $this->paymentID, array(
				'subTotal'      => $this->oReceipt->subTotal,
				'total'         => $amount,
				'currency'      => $this->aConfiguration['currency_code'],
				'discount'      => $discountPrice,
				'tax'           => $this->oReceipt->tax
			));

			/*
			 * We will delete all sessions here
			 */
			do_action('wiloke-submission/payment-succeeded-and-updated-everything');

			return array(
				'status'    => 'success',
				'paymentID' => $this->paymentID,
				'msg'       => esc_html__('Congratulations! Your payment has been succeeded', 'wiloke-listing-tools'),
				'thankyou' => apply_filters('wilcity/wiloke-listing-tools/stripe/successfully', GetWilokeSubmission::getField('thankyou', true), $aResponse)
			);

		} catch (\Exception $ex) {
			/*
			 * @PaymentStatusController:updatePaymentStatus 5
			 * @PostController:rollupListingToPreviousStatus 10
			*/
			do_action('wiloke-listing-tools/payment-failed', array(
				'status'    => 'failed',
				'paymentID' => $this->paymentID,
				'postID'        => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'), true),
			));

			FileSystem::filePutContents('stripe-error.log', json_encode(array(
				'paymentID' => $this->paymentID,
				'date'      => current_time('timestamp', true),
				'msg'       => $ex->getMessage()
			)));

			return array(
				'status' => 'error',
				'msg'    => $ex->getMessage()
			);
		}
	}
}