<?php
namespace WilokeListingTools\Models;

use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Helpers\WooCommerce;


class RemainingItems{
	protected $userID;
	protected $paymentID;
	protected $planID;
	protected $billingType;
	protected $createdAt;
	protected $nextBillingDateGMT;
	protected $gateway;
	protected $planType;
	protected $duration;
	protected $availabilityListings;
	protected $aPlanSettings;

	public function __get( $name ) {
		if ( property_exists($this, $name) ){
			return $this->{$name};
		}

		return false;
	}

	public function setUserID($userID){
		$this->userID = $userID;
		return $this;
	}

	public function setGateway($gateway){
		$this->gateway = $gateway;
		return $this;
	}

	public function setPaymentID($paymentID){
		$this->paymentID = $paymentID;
		return $this;
	}

	public function setPlanID($planID){
		$this->planID = $planID;
		return $this;
	}

	public function setBillingType($billingType){
		$this->billingType = $billingType;
		return $this;
	}

	public function getBillingType(){
		if ( empty($this->paymentID) ){
			return false;
		}

		$this->billingType = !empty($this->billingType) ? $this->billingType : PaymentModel::getField($this->paymentID, 'billingType');
		return $this;
	}

	public function getPlanID(){
		return $this->planID;
	}

	public function getPaymentID(){
		return $this->paymentID;
	}

	public function getUserID(){
		return $this->userID;
	}

	public function getPlanType(){
		if ( empty($this->planID) ){
			return false;
		}

		$this->planType = get_post_type($this->planID);
		return $this;
	}

	public function getCreatedAt(){
		if ( empty($this->paymentID) ){
			return false;
		}

		$this->createdAt = PaymentModel::getField($this->paymentID, 'createdAt');
		return $this;
	}

	/*
	 * Get Plan duration.
	 */
	public function getDuration(){
		$productID = WooCommerce::getWooCommerceAliasByPlanID($this->planID);

		if ( !empty($productID) ){
			if ( WooCommerce::isSubscriptionProduct($productID) ){
				$trialLength = 0;
				if ( PaymentMetaModel::get($this->paymentID, wilokeListingToolsRepository()->get('addlisting:isUsingTrial')) ){
					$trialLength = \WC_Subscriptions_Product::get_trial_length( $productID );
					$trialPeriod = \WC_Subscriptions_Product::get_trial_period( $productID );
					$this->duration = WooCommerce::convertPeriodToDays($trialLength, $trialPeriod);
				}

				if ( !empty($trialLength) ){
					return $this->duration;
				}

				$regularLength = \WC_Subscriptions_Product::get_interval( $productID );
				$regularPeriod = \WC_Subscriptions_Product::get_period( $productID );
				$this->duration = WooCommerce::convertPeriodToDays($regularLength, $regularPeriod);

				return $this->duration;
			}
		}

		$aPlanSettings = GetSettings::getPlanSettings($this->planID);

		if ( PaymentMetaModel::get($this->paymentID, wilokeListingToolsRepository()->get('addlisting:isUsingTrial')) ){
			$this->duration = $aPlanSettings['trial_period'];
		}else{
			$this->duration = $aPlanSettings['regular_period'];
		}
		return $this->duration;
	}

	public function getAvailabilityListings(){
		$this->availabilityListings = $this->aPlanSettings['availability_items'];
		return $this->availabilityListings;
	}

	public function getNextBillingDateGMT(){
		if ( empty($this->paymentID) ){
			return false;
		}

		$this->nextBillingDateGMT = PaymentMetaModel::get($this->paymentID, wilokeListingToolsRepository()->get('addlisting:nextBillingDateGMT'));
		return $this->nextBillingDateGMT;
	}

	public function setNextBillingDateGMT($nextBillingDateGMT){
		$this->nextBillingDateGMT = is_numeric($nextBillingDateGMT) ? $nextBillingDateGMT : strtotime($nextBillingDateGMT);
		return $this;
	}

	public function getRemainingItems(){
		$this->aPlanSettings = GetSettings::getPlanSettings($this->planID);
		$this->getAvailabilityListings();

		if ( empty($this->availabilityListings) ){
			return 1000000000000;
		}

		if ( !GetWilokeSubmission::isNonRecurringPayment($this->billingType) ){
			$now = Time::timestampUTCNow();
			$this->getNextBillingDateGMT();

			if ( $now > $this->nextBillingDateGMT ){
				return 0;
			}

			$totalUsed = PlanRelationshipModel::getUsedRecurringPlan($this);
		}else{
			$totalUsed = PlanRelationshipModel::getUsedNonRecurringPlan($this);
		}

		return abs($this->availabilityListings) - $totalUsed;
	}
}
