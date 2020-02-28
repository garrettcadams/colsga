<?php
namespace WilokeListgoFunctionality\Framework\Routing;


use WilokeListgoFunctionality\Framework\Helpers\DebugStatus;
use WilokeListgoFunctionality\Framework\Helpers\GenerateUrl;
use WilokeListgoFunctionality\Framework\Helpers\GetSettings;
use WilokeListgoFunctionality\Model\PlanRelationshipModel;
use WilokeListgoFunctionality\Framework\Payment\PaymentConfiguration;
use WilokeListgoFunctionality\Model\UserModel;

class RouteAfterItemInserted {
	protected $isFreeAddItem;
	protected $planID;
	protected $planType;
	protected $userID;
	protected $postID;
	protected $sessionID;
	protected $isCheckout = false;
	protected $thankyouUrl = null;
	protected $checkoutUrl = null;

	/**
	 * Set Plan ID
	 *
	 * @param number $planID
	 * @return object
	 */
	public function setPostID($postID){
		$this->postID = abs($postID);
		return $this;
	}

	/**
	 * Set Plan Mode: Free Add Item or Not
	 *
	 * @param boolean $isFreeMode
	 * @return object
	 */
	public function isFreeAddItem($isFreeMode){
		$this->isFreeAddItem = $isFreeMode;
		return $this;
	}

	/**
	 * Set Plan ID
	 *
	 * @param number $planID
	 * @return object
	 */
	public function setPlanID($planID){
		$this->planID = abs($planID);
		$this->setPlanType();
		return $this;
	}

	/**
	 * Set Session ID
	 *
	 * @param number $sessionID
	 * @return object
	 */
	public function getSessionID($sessionID){
		$this->sessionID = abs($sessionID);
		return $this;
	}

	/**
	 * Set Plan Type
	 *
	 * @return object
	 */
	public function setPlanType(){
		$this->planType = get_post_field('post_type', $this->planID);
		return $this;
	}

	/**
	 * Set Plan ID
	 *
	 * @param number $userID
	 * @return object
	 */
	public function setUserID($userID){
		$this->userID = abs($userID);
		return $this;
	}

	/**
	 * You can modify thankyou url
	 *
	 * @param string $url
	 * @return object
	 */
	public function setThankyouUrl($url){
		$this->thankyouUrl = esc_url($url);
		return $this;
	}

	/**
	 * You can modify checkout url
	 *
	 * @param string $url
	 * @return object
	 */
	public function setCheckoutUrl($url){
		$this->checkoutUrl = esc_url($url);
		return $this;
	}

	public function getThankyouUrl(){
		if ( !empty($this->thankyouUrl) ){
			return $this->thankyouUrl;
		}

		$aWilokeSubmissionSettings = PaymentConfiguration::get();
		$this->thankyouUrl = get_permalink($aWilokeSubmissionSettings['thankyou']);
		return $this->thankyouUrl;
	}

	/**
	 * Response the result
	 *
	 * @param bool $isSuccess
	 * @param string $url
	 * @return object $aVal
	 */
	public function buildResult($isSuccess=true, $url, $msg=''){
		return (object)array(
			'success'       => $isSuccess,
			'redirectTo'    => $url,
			'isCheckout'    => $this->isCheckout,
			'msg'           => $msg
		);
	}

	public function getCheckoutUrl(){
		if ( !empty($this->checkoutUrl) ){
			return $this->checkoutUrl;
		}

		$aWilokeSubmissionSettings = PaymentConfiguration::get();

		$this->checkoutUrl = GenerateUrl::url(
			get_permalink($aWilokeSubmissionSettings['checkout']),
			array(
				'package_id' => $this->planID
			)
		);
		return $this->checkoutUrl;
	}

	/**
	 * Determining where we should redirect to
	 *
	 * @return string $url
	 */
	public function response(){

		if ( DebugStatus::status('WILOKE_SUBMISSION_THROUGH_ALL') ){
			$this->isCheckout = true;
			return $this->buildResult(true, $this->getCheckoutUrl());
		}

		if ( $this->isFreeAddItem ){
			return $this->buildResult(true, $this->getThankyouUrl());
		}

		$postStatus = get_post_status($this->postID);

		if ( DebugStatus::status('WILOKE_TURNON_VERIFY_CLAIM') ){
			$aClaimInfo = GetSettings::getPostMeta($this->postID, 'listing_claim');

			if ( $aClaimInfo['status'] == 'claimed' ){
				$belongToPlanID = GetSettings::getPostMeta($this->postID, wilokeRepository('app:belongsToPlanID'));
				$aUserPlanIDs 	= UserModel::getDetailPlan($belongToPlanID);
				
				// $aPlanSettings = GetSettings::getPostMeta($belongToPlanID, $this->planType);
				if ( !empty($aUserPlanIDs) ){
					return $this->buildResult(true, $this->getThankyouUrl());
				}
			}
		}else{
			if ( $postStatus == 'pending' || $postStatus == 'publish' || $postStatus == 'renew' ){
				return $this->buildResult(true, $this->getThankyouUrl());
			}
		}

		$aPlanSettings = GetSettings::getPostMeta($this->planID, $this->planType);

		if ( empty($aPlanSettings['number_of_posts']) && empty($aPlanSettings['price']) ){
			return $this->buildResult(true, $this->getThankyouUrl());
		}

		$availabilityItems = abs($aPlanSettings['number_of_posts']);
		if ( empty($aPlanSettings['price']) ){
			$countUsedItems = PlanRelationshipModel::countItemsUsedByUserIDAndPlanID($this->userID, $this->planID);

			if ( ($countUsedItems - $availabilityItems) < 0 ){
				return $this->buildResult(true, '', esc_html__('Whoops! You have already reached the number of free items. Please purchase a paid plan to continue adding', 'wiloke-submission' ));
			}else{
				return $this->buildResult(true, $this->getThankyouUrl());
			}
		}

		$aUserPlan = UserModel::getDetailPlan($this->planID);

		if ( empty($aUserPlan) ){
			$this->isCheckout = true;
			return $this->buildResult(true, $this->getCheckoutUrl());
		}

		return $this->buildResult(true, $this->getThankyouUrl());
	}
}