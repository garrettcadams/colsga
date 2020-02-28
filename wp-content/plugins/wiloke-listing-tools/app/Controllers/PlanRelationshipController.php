<?php
namespace WilokeListingTools\Controllers;


use WilokeListingTools\AlterTable\AlterTablePlanRelationships;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Models\PlanRelationshipModel;

class PlanRelationshipController extends Controller {
	public function __construct() {
		add_action('wiloke-listing-tools/payment-succeeded/event_plan', array($this, 'update'), 5);
		add_action('wiloke-listing-tools/payment-pending/event_plan', array($this, 'update'), 5);
		add_action('wiloke-listing-tools/payment-failed/event_plan', array($this, 'delete'), 5);

		add_action('wiloke-listing-tools/payment-succeeded/listing_plan', array($this, 'update'), 5);
		add_action('wiloke-listing-tools/payment-pending/listing_plan', array($this, 'update'), 5);
		add_action('wiloke-listing-tools/payment-failed/listing_plan', array($this, 'delete'), 5);

		/*
		 * WooCommerce Subscription
		 */
		add_action('wiloke-listing-tools/on-changed-user-plan', array($this, 'switchListingsBelongsToOldPaymentIDToNewPaymentID'), 1, 1);
	}

	/**
	 * Switch Listings Relationship Belongs To New Plan
	 */
	public function switchListingsBelongsToOldPaymentIDToNewPaymentID($aInformation){
		$aRequires = array('oldPlanID'=>'The old Plan ID is required', 'planID' => 'The new Plan ID is required', 'paymentID'=>'The new Payment ID is required', 'oldPaymentID'=>'The old payment ID is required');

		foreach ($aRequires as $param => $msg){
			if ( !isset($aInformation[$param]) || empty($aInformation[$param]) ){
				throw new \Exception($msg);
			}
		}

		PlanRelationshipModel::updateNewPaymentID(
			$aInformation['paymentID'],
			$aInformation['oldPaymentID'],
			$aInformation['oldPlanID'],
			$aInformation['planID']
		);
	}

	/*
	 * Check whether this product is WooCommerce Subscription or not
	 *
	 * @return bool
	 * @since 1.2.0
	 */
	private function isWooCommerceSubscription($post){
		return class_exists('\WC_Subscriptions_Product') && $post->post_type == 'product';
	}

	/*
	 * Get Listing Plan ID by Product ID
	 *
	 * @return int
	 * @since 1.2.0
	 */
	private function getPlanIDByProductID($productID){
		global $wpdb;

		$productID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $wpdb->posts.ID FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON ($wpdb->postmeta.post_id = $wpdb->posts.ID) WHERE $wpdb->posts.post_type=%s AND $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value=%s",
				'listing_plan', 'wilcity_woocommerce_association', $productID
			)
		);

		return empty($productID) ? 0 : abs($productID);
	}

	/*
	 * Auto-update Period day and Trial Period day after Production Subscription changed
	 *
	 * @since 1.2.0
	 */
	public function woocommerceSubscriptionReflectPlanPeriodDay($productID, $oAfterPost, $oBeforePost){
		if ( !$this->isWooCommerceSubscription($oAfterPost) ){
			return false;
		}

		$listingPlanID = $this->getPlanIDByProductID($productID);
		if ( empty($listingPlanID) ){
			return false;
		}

		$periodDays = \WC_Subscriptions_Product::get_length($productID);
	}

	/*
	 * If the session is failed, we will delete this field
	 */
	public function delete($aInfo){
		if ( $aInfo['status'] !== 'failed' ){
			return false;
		}

		if ( !isset($aInfo['planRelationshipID']) || empty($aInfo['planRelationshipID']) ){
			return false;
		}

		global $wpdb;
		$tbl = $wpdb->prefix . AlterTablePlanRelationships::$tblName;

		return $wpdb->delete(
			$tbl,
			array(
				'ID' => $aInfo['planRelationshipID']
			),
			array(
				'%d'
			)
		);
	}

	/**
	 * After Payment has been completed, We should update Plan Relationship
	 *
	 * @param $aInfo: status, gateway, billingType, paymentID, planID, isTrial, planRelationshipID
	 * @return bool
	 */
	public function update($aInfo){
		if ( !in_array($aInfo['status'], array('active', 'succeeded', 'pending')) ){
			return false;
		}

		if ( !isset($aInfo['planRelationshipID']) || empty($aInfo['planRelationshipID']) ){
			return false;
		}

		global $wpdb;
		$tbl = $wpdb->prefix . AlterTablePlanRelationships::$tblName;

		return $wpdb->update(
			$tbl,
			array(
				'paymentID' => $aInfo['paymentID']
			),
			array(
				'ID' => $aInfo['planRelationshipID']
			),
			array(
				'%d'
			),
			array(
				'%d'
			)
		);

	}
}