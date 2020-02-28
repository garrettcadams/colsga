<?php

namespace WilokeListingTools\Framework\Payment\WooCommerce;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Payment\AbstractSuspend;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Models\PaymentModel;

class WooCommerceChangePlan extends AbstractSuspend{
	protected $newPaymentID;
	protected $aPlanSettings;
	protected $oReceipt;
	protected $userID;
	private $currentOrderID;
	private $gateway;

	public function __construct($userID, $currentPaymentID, $newPlanID, $currentPlanID, $listingType) {
		$this->userID = $userID;
		$this->currentPaymentID = $currentPaymentID;
		$this->newPlanID = $newPlanID;
		$this->listingType = $listingType;
		$this->currentPlanID = $currentPlanID;
		$this->productID = GetSettings::getPostMeta($this->newPlanID, 'woocommerce_association');
		$this->gateway = 'wooocmmerce';
	}

	private function getOrderIDByPaymentID(){
		$this->currentOrderID = PaymentModel::getField('wooOrderID', $this->currentPaymentID);
	}

	/**
	 * Before upgrading / downgrade to a new plan, We will switch currently subscription to on-hold (suspend) status
	 *
	 * @since 1.1.7.3
	 */
	public function execute(){
		if ( empty($this->productID) || get_post_status($this->productID) !== 'publish' || get_post_field('post_type', $this->productID) != 'product' ){
			wp_send_json_error(array(
				'msg' => esc_html__('This product is not existed or it is no longer available', 'wiloke-listing-tools')
			));
		}

		/*
		 * Set sessions that needed for change plan
		 *
		 * @var newPlanID
		 * @var listingType
		 * @var listingType
		 * @var currentPlanID
		 */
		$this->setSessions();

		/*
		 * Get WooCommerce Order ID. Empty current Order ID if it's current order id is a plan
		 */
		$this->getOrderIDByPaymentID();
		if ( !empty($this->currentOrderID) ){
			/*
			 * Required Steps
			 *
			 * 1. setCurrentOrderID (It's WooCommerce Order ID)
			 * 1. suspend
			 */
			$oInitSuspend = new WooCommerceSuspend();
			$aStatus = $oInitSuspend->setCurrentOrderID($this->currentOrderID)->suspend();

			if ( $aStatus['status'] == 'error' ){
				wp_send_json_error($aStatus);
			}
		}else{
			Session::setSession(wilokeListingToolsRepository()->get('payment:oldPaymentID'), $this->currentPaymentID);
		}

		$wooCommerceCartUrl = GetSettings::getCartUrl($this->newPlanID);
		/*
		* @hooked WooCommerceController:removeProductFromCart
		*/
		do_action('wiloke-listing-tools/before-redirecting-to-cart', $this->productID);
		Session::setSession(wilokeListingToolsRepository()->get('payment:associateProductID'), $this->productID);
		Session::setSession(wilokeListingToolsRepository()->get('payment:wooOldOrderID'), $this->currentOrderID);

		return array(
			'status'    => 'success',
			'redirectTo'=> $wooCommerceCartUrl
		);
	}
}