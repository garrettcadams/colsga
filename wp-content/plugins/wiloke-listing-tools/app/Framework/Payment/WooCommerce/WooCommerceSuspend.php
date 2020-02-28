<?php

namespace WilokeListingTools\Framework\Payment\WooCommerce;


use WilokeListingTools\Framework\Helpers\WooCommerce;
use WilokeListingTools\Framework\Payment\SuspendInterface;
use WilokeListingTools\Models\PaymentModel;

class WooCommerceSuspend implements SuspendInterface {
	private $currentOrderID;
	private $subscriptionID;
	private $aSubscription;

	/*
	 * This step is required, You have to set this value at the first step
	 *
	 * @since 1.1.7.3
	 */
	public function setCurrentOrderID($orderID){
		$this->currentOrderID = $orderID;
		return $this;
	}

	/**
	 * We will need to get latest subscription, and this subscription will be switched to suspend status after that
	 *
	 * @since 1.1.7.3
	 */
	private function getLatestSubscriptionID(){
		$this->aSubscription = wcs_get_subscriptions_for_order( $this->currentOrderID, array( 'order_type' => array( 'parent','renewal' ), 'orderby' => 'order_id', 'order' => 'DESC',  'subscriptions_per_page'=>1 ));
		if ( !empty($this->aSubscription) ){
			$aKey = array_keys($this->aSubscription);
			$this->subscriptionID = $aKey[0];

			return $this->subscriptionID;
		}

		return false;
	}

	/*
	 * Finally, We will change subscription id to suspend status
	 *
	 * @since 1.1.7.3
	 */
	public function suspend(){
		if ( empty($this->currentOrderID) ){
			return array('status' => 'error', 'msg' => esc_html__('The current order id is required.', 'wiloke-listing-tools'));
		}

		if ( !$this->getLatestSubscriptionID() ){
			return array('status' => 'error', 'msg' => esc_html__('We could not found any subscription under this order id', 'wiloke-listing-tools'));
		}

		if ( WooCommerce::isActivateSubscription($this->subscriptionID) ){
			$oSubscription = new \WC_Subscription($this->subscriptionID);
			try{
				$oSubscription->update_status('on-hold', esc_html__('Customer Changed Plan', 'wiloke-listing-tools'), true);
				return array('status' => 'success');
			}catch (\Exception $oE){
				return array('status' => 'error', 'msg' => $oE->getMessage());
			}
		}else{
			return array('status' => 'success');
		}
	}
}