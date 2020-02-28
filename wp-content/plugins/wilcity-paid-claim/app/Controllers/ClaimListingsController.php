<?php

namespace WilcityPaidClaim\Controllers;


use WilcityPaidClaim\Register\RegisterClaimSubMenu;
use WilokeListingTools\Controllers\ClaimController;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Payment\Billable;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Register\WilokeSubmissionConfiguration;

class ClaimListingsController extends Controller {
	protected $postID;
	protected $planID;
	protected static $prefixClaimingKey = 'wilcity_claiming_by_';
	protected static $claimingInfoExpiration = 600;

	public function __construct() {
		add_action('wiloke-listing-tools/before-handling-claim-request', array($this, 'verifyPaidClaimListing'));
		add_filter('wilcity/claim-field-settings', array($this, 'claimSettings'), 10, 2);
		add_action('wiloke-listing-tools/payment-succeeded/paidClaim', array($this, 'paidClaimSuccessfully'), 50);
		add_action('wiloke-listing-tools/woocommerce/after-order-succeeded/paidClaim', array($this, 'paidClaimSuccessfully'), 50);

		add_action('wilcity/claim-listing/approved', array($this, 'paidClaimSuccessfully'), 50);
		add_action('wiloke-listing-tools/payment-succeeded', array($this, 'updateClaimToApprovedStatus'));
	}

	public function updateClaimToApprovedStatus($aResponse){
		if ( !isset($aResponse['packageType']) || $aResponse['packageType'] == 'promotion' ){
			return false;
		}

		$claimID = PaymentMetaModel::get($aResponse['paymentID'], 'claimID');
		if ( empty($claimID) ){
			return false;
		}

		SetSettings::setPostMeta($claimID, 'claim_status', 'approved');
		wp_update_post(array(
			'ID' => $claimID,
			'post_status' => 'publish'
		));
		PaymentMetaModel::delete($aResponse['paymentID'], 'claimID');
	}

	public function paymentFailed($aData){
		$userPaidID = PaymentModel::getField('userID', 254);
		$aClaimInfo = GetSettings::getTransient(self::$prefixClaimingKey . 1595);

		if ( $userPaidID != $aClaimInfo['claimerID'] ){
			return false;
		}
		$userPaidID = PaymentModel::getField('userID', 254);
	}

	public function verifyPaidClaimListing($aData){
		$aClaimOptions = GetSettings::getOptions(RegisterClaimSubMenu::$optionKey);
		if ( !isset($aClaimOptions['toggle']) || ($aClaimOptions['toggle'] == 'disable') ){
			return true;
		}

		$msg = esc_html__('The Claim Plan is required.', 'wilcity-paid-claim');
		if ( !isset($aData['claimPackage']) || empty($aData['claimPackage']) ){
			wp_send_json_error(
				array(
					'msg' => $msg
				)
			);
		}

		$postType = get_post_type($aData['postID']);
		$packageType = $postType . '_plans';
		$aClaimPlains = GetWilokeSubmission::getAddListingPlans($packageType);
		if ( !in_array($aData['claimPackage'], $aClaimPlains) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Oops! This plan does not exists.', 'wilcity-paid-claim')
				)
			);
		}

		return true;
	}

	/*
	 * Claiming Info
	 *
	 * @param $aInfo: $planID, $postID, $claimerID
	 */
	public function saveClaimingInfo($aInfo){
		SetSettings::setTransient(self::$prefixClaimingKey . $aInfo['postID'], $aInfo, self::$claimingInfoExpiration);
	}

	/*
	 * When a customer is claiming a listing, this listing will temporary close claim feature
	 *
	 * @return array: $claimID, $claimerID, $paymentID
	 */
	public static function claimingBy($postID){
		return GetSettings::getTransient(self::$prefixClaimingKey.$postID);
	}

	public function paidClaimSuccessfully($aResponse){
		if ( ($aResponse['status'] !== 'active' && $aResponse['status'] !== 'succeeded') || empty($aResponse['postID']) ){
			return false;
		}

		$isPaidClaim = apply_filters('wilcity/wilcity-paid-claim/filter/is-paid-claim', ClaimController::isPaidClaim());

		if ( !$isPaidClaim ){
			return false;
		}
		SetSettings::setPostMeta($aResponse['claimID'], 'claim_status', 'approved');
		wp_update_post(
			array(
				'ID' => $aResponse['claimID'],
				'post_status' => 'publish'
			)
		);

		wp_update_post(
			array(
				'ID'          => $aResponse['postID'],
				'post_author' => $aResponse['userID'],
				'post_status' => 'publish'
			)
		);

		//SetSettings::deleteTransient(self::$prefixClaimingKey . $aResponse['postID']);
	}

	public function claimSettings($aFields, $post){
		$aClaimOptions = GetSettings::getOptions(RegisterClaimSubMenu::$optionKey);

		if ( !isset($aClaimOptions['toggle']) || ($aClaimOptions['toggle'] == 'disable') ){
			$aFields = array_filter($aFields, function($aValue){
				return $aValue['key'] !== 'claimPackage';
			});
			return $aFields;
		}

		$aRawClaimPlans = GetWilokeSubmission::getAddListingPlans($post->post_type.'_plans');
		if ( empty($aRawClaimPlans) ){
			return array('noPackage'=>true);
		}

		foreach ($aRawClaimPlans as $planID){
			if ( get_post_field('post_status', $planID) !== 'publish' || get_post_type($planID) !== 'listing_plan' ){
				continue;
			}

			if ( GetSettings::getPostMeta($planID, 'exclude_from_claim_plans') == 'on' ){
				continue;
			}

			$aClaimPlans[$planID]['value']  = $planID;
			$aPlanSettings = GetSettings::getPlanSettings($planID);

			$price = GetWilokeSubmission::renderPrice($aPlanSettings['regular_price']);
			$aClaimPlans[$planID]['name']  = get_the_title($planID) . ' - ' .  $price;
		}

		if ( empty($aClaimPlans) ){
			return array('noPackage'=>true);
		}else{
			$aClaimPlans = apply_filters('wilcity/filter/claim-packages/'.$post->post_type, $aClaimPlans);
		}

		$addedClaimedPackage = false;

		foreach ($aFields as $key => $aField){
			if ( $aField['key'] == 'claimPackage' ){
				if ( !$addedClaimedPackage ){
					$addedClaimedPackage = true;
					$aFields[$key]['options'] = $aClaimPlans;
				}else{
					unset($aFields[$key]);
				}
			}
		}
		return $aFields;
	}
}