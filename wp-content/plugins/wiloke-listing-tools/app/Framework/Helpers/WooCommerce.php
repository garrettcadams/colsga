<?php

namespace WilokeListingTools\Framework\Helpers;


use WilokeListingTools\Models\PaymentModel;

class WooCommerce {
	/*
	 * Checks a given order to see if it was used to purchase a WC_Subscription object via checkout.
	 *
	 * @return bool
	 * @since 1.2.0
	 */
	public static function isSubscription($orderID){
		if ( !function_exists('wcs_order_contains_subscription') || !wcs_order_contains_subscription($orderID) ){
			return false;
		}

		return true;
	}

	/**
	 * Get latest Subscription ID by Order ID
	 *
	 * @since 1.2.0
	 *
	 * @var $orderID WooCommerce Order ID
	 * @return int
	 */
	public static function getLatestSubscriptionIDByOrderID($orderID, $subscriptionStatus='any'){
		global $wpdb;
		$subscriptionID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID from $wpdb->posts WHERE post_parent=%d AND post_status=%s and post_type='shop_subscription' ORDER BY ID DESC LIMIT 1",
				$orderID, $subscriptionStatus
			)
		);

		return empty($subscriptionID) ? $subscriptionID : abs($subscriptionID);
	}


	/**
	 * Count total subscriptions of Order
	 *
	 * @since 1.2.0
	 *
	 * @var $orderID WooCommerce Order ID
	 * @return int
	 */
	public static function countSubscriptionsByOrderID($orderID, $subscriptionStatus='any'){
		global $wpdb;
		$subscriptionID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(ID) from $wpdb->posts WHERE post_parent=%d AND post_status=%s and post_type='shop_subscription'",
				$orderID, $subscriptionStatus
			)
		);

		return empty($subscriptionID) ? $subscriptionID : abs($subscriptionID);
	}

	/**
	 * Convert Period To day
	 *
	 * @since 1.2.0
	 *
	 * @var $length
	 * @var $period
	 *
	 * @return int
	 */
	public static function convertPeriodToDays($length, $period){
		$days_in_cycle = 0;
		switch ($period) {
			case 'week' :
				$days_in_cycle = 7 * $length;
				break;
			case 'day' :
				$days_in_cycle = $length;
				break;
			case 'month' :
				$days_in_cycle = gmdate( 't' ) * $length;
				break;
			case 'year' :
				$days_in_cycle = ( 365 + gmdate( 'L' ) ) * $length;
				break;
		}
		return abs($days_in_cycle);
	}

	/**
	 * Checks a given product id to see it's Subscription Product or not
	 *
	 * @since 1.2.0
	 * @return bool
	 */
	public static function isSubscriptionProduct($productID){
		return class_exists( 'WC_Subscriptions_Product' ) && \WC_Subscriptions_Product::is_subscription( $productID );
	}


	/**
	 * Get WooCommerce Product ID by Plan ID
	 *
	 * @since 1.2.0
	 * @return int
	 */
	public static function getWooCommerceAliasByPlanID($planID){
		$productID = GetSettings::getPostMeta($planID, 'woocommerce_association');
		return empty($productID) ? 0 : abs($productID);
	}

	/*
	 * Checks a give order to see if it is Purchase Listing Plan session or not
	 *
	 * @since 1.2.0
	 * @return bool
	 */
	public static function isPurchaseListingPlan($orderID){
		$aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($orderID);
		if ( empty($aPaymentIDs) ){
			return false;
		}

		return true;
	}

	private static function getSubscriptionIDByOrderID($order_id){
		$subscriptions_ids = wcs_get_subscriptions_for_order( 5518 );
		// We get the related subscription for this order
		foreach( $subscriptions_ids as $subscription_id => $subscription_obj ) {
			if ( $subscription_obj->order->id == $order_id ) {
				return $subscription_obj;
			}
		}
	}

	/**
	 * Get Order Status
	 *
	 * @since 1.2.0
	 * @return string
	 */
	public static function getOrderStatus($orderID){
		$oOrder = wc_get_order( $orderID );
		return $oOrder->get_status();
	}

	/**
	 * Is activating Subscription
	 *
	 * @since 1.2.0
	 * @var $subscriptionID
	 * @return bool
	 */
	public static function isActivateSubscription($subscriptionID){
		$oSubscription = new \WC_Subscription($subscriptionID);
		return $oSubscription->get_status() == 'active';
	}


	/**
	 * Check whether this order is completed or not
	 *
	 * @since 1.2.0
	 */
	public static function isCompletedOrder($orderID){
		return self::getOrderStatus($orderID) == 'completed';
	}

	/*
	 * Get trial duration day
	 *
	 * @return int
	 * @since 1.2.0
	 */
	public static function getNextBillingDate($oOrder){

	}

	/*
	 * Get trial duration day
	 *
	 * @return int
	 * @since 1.2.0
	 */
	public static function getBillingType($orderID){
		return self::isSubscription($orderID) ? wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring') : wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring');
	}
}