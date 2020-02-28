<?php
namespace WilokeListingTools\Framework\Payment\PayPal;

use PayPal\Api\Agreement;
use PayPal\Api\Payment;
use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Models\InvoiceModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use PayPal\Api\AgreementDetails;

class Webhook{
	use PayPalGenerateUrls;
	use PayPalConfiguration;

	public $gateway = 'paypal';
	protected $subTotalPrice = 0;
	protected $discountPrice = 0;
	protected $totalPrice = 0;

	public function __construct() {
		$this->listener();
	}

	protected function getPaymentDetails($paymentID){
		$this->setupConfiguration();
		try{
			$oDetails = Payment::get($paymentID, $this->oApiContext);
			foreach ($oDetails->transactions[0]->item_list->items as $oItem){
				if ( strpos($oItem->price, '-') !== false ){
					$this->discountPrice += floatval($oItem->price);
				}else{
					$this->subTotalPrice += floatval($oItem->price);
				}
			}
			$this->totalPrice = $oDetails->transactions[0]->amount->total;
			return true;
		}catch (\Exception $oException){
			return false;
		}
	}

	public function listener(){
		$rawdata = file_get_contents( 'php://input' );
		if ( empty($rawdata) ){
			return false;
		}

		$oEventInfo = json_decode($rawdata);

        $oldData = FileSystem::fileGetContents('paypal-event.log');

		FileSystem::logPayment('paypal-event.log', $rawdata . "\r\n" . $oldData);

		switch ($oEventInfo->event_type){
			case 'BILLING.SUBSCRIPTION.CREATED':
				if ( isset($oEventInfo->resource->billing_agreement_id) ){
					$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($oEventInfo->resource->billing_agreement_id, wilokeListingToolsRepository()->get('addlisting:paypalAgreementID'));
					if ( empty($paymentID) ){
						return false;
					}

					/*
					 * @PaymentMetaController:updateNextBillingDateGMT
					 */
					do_action('wiloke-listing-tools/subscription-created', array(
						'nextBillingDateGMT' => strtotime($oEventInfo->resource->next_billing_date),
						'paymentID'          => $paymentID,
						'gateway'            => 'paypal'
					));
				}
				break;
			case 'PAYMENT.SALE.COMPLETED':
				if ( isset($oEventInfo->resource->billing_agreement_id) ){
					// Recurring Payment
					$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($oEventInfo->resource->billing_agreement_id, wilokeListingToolsRepository()->get('addlisting:paypalAgreementID'));
					if ( empty($paymentID) ){
						return false;
					}

					$this->setupConfiguration();
					$oAgreementCheck = \PayPal\Api\Agreement::get($oEventInfo->resource->billing_agreement_id, $this->oApiContext);
					$oAgreementDetails = $oAgreementCheck->getAgreementDetails();
					$nextBillingDateGMT = $oAgreementDetails->getNextBillingDate();

					if ( isset($oEventInfo->resource->amount->details->subtotal) && !empty($oEventInfo->resource->amount->details->subtotal) ){
						$subTotal = floatval($oEventInfo->resource->amount->details->subtotal);
					}else{
						$subTotal = floatval($oEventInfo->resource->amount->total);
					}

					do_action('wilcity/paypal/insert-invoice', $paymentID, array(
						'currency'      => $oEventInfo->resource->amount->currency,
						'subTotal'      => $subTotal,
						'discount'      => 0,
						'tax'           => 0,
						'total'         => $oEventInfo->resource->amount->total
					));

					if ( empty($oEventInfo->resource->amount->total) || $oEventInfo->resource->amount->total == '00.00' ){
						PaymentMetaModel::delete($paymentID, wilokeListingToolsRepository()->get('addlisting:isUsingTrial'));
					}

					/*
					 * @PaymentMetaController:updateNextBillingDateGMT
					 */
					date_default_timezone_set('UTC');
					do_action('wiloke-listing-tools/payment-renewed', array(
						'paymentID'          => $paymentID,
						'nextBillingDateGMT' => strtotime($nextBillingDateGMT),
						'gateway'            => 'paypal',
						'billingType'        => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring')
					));

				}else{
					// Non-Recurring Payment
					$paypalPaymentID = $oEventInfo->resource->parent_payment;
					$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($paypalPaymentID, wilokeListingToolsRepository()->get('payment:paypalPaymentID'));

					if ( empty($paymentID) || !$this->getPaymentDetails($paypalPaymentID) ){
						return false;
					}

					do_action('wilcity/paypal/insert-invoice', $paymentID, array(
						'currency'      => $oEventInfo->resource->amount->currency,
						'subTotal'      => $this->subTotalPrice,
						'discount'      => $this->discountPrice,
						'tax'           => 0,
						'total'         => $this->totalPrice
					));
				}
				break;
			case 'PAYMENT.SALE.REFUNDED':
				$paypalPaymentID = isset($oEventInfo->resource->parent_payment) ? $oEventInfo->resource->parent_payment : '';

				if ( empty($paypalPaymentID) ){
					return false;
				}

				$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($paypalPaymentID, wilokeListingToolsRepository()->get('payment:paypalPaymentID'));

				if ( empty($paymentID) ){
					return false;
				}

				do_action('wilcity/paypal/insert-invoice', $paymentID, array(
					'currency'  => $oEventInfo->resource->refund_to_payer->currency,
					'subTotal'  => -$oEventInfo->resource->refund_to_payer->value,
					'discount'  => 0,
					'tax'       => 0,
					'total'     => -$oEventInfo->resource->refund_to_payer->value
				));

				PaymentModel::updatePaymentStatus( 'refunded', $paymentID);

				/*
				 * @hooked: PostController:moveAllPostsToTrash
				 */
				do_action('wiloke-listing-tools/payment-refunded', array(
					'gateway'   => 'paypal',
					'paymentID' => $paymentID
				));
				break;
			case 'BILLING.SUBSCRIPTION.CANCELLED':
			case 'BILLING.SUBSCRIPTION.SUSPENDED':
				$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($oEventInfo->resource->id, wilokeListingToolsRepository()->get('addlisting:paypalAgreementID'));

				if ( empty($paymentID) ){
					return false;
				}

				if ( $oEventInfo->event_type == 'BILLING.SUBSCRIPTION.CANCELLED' ){
					$status = 'cancelled';
					PaymentModel::updatePaymentStatus('cancelled', $paymentID);
				}else{
					$status = 'suspended';
					PaymentModel::updatePaymentStatus( 'suspended', $paymentID);
				}

				/*
				 * @PaymentMetaController:updateNextBillingDateGMT
				 * @PaymentMetaController:moveAllPostsToExpiry
				 */
				do_action('wiloke-listing-tools/payment-'.$status, array(
					'paymentID'  => $paymentID,
					'gateway'    => 'paypal'
				));

				break;
			case 'BILLING.SUBSCRIPTION.RE-ACTIVATED':
				$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($oEventInfo->resource->id, wilokeListingToolsRepository()->get('payment:paypalPaymentID'));

				if ( empty($paymentID) ){
					return false;
				}

				$this->setupConfiguration();

				PaymentModel::updatePaymentStatus( 'active', $paymentID);

				try{
					$oAgreement = Agreement::get($oEventInfo->resource->id, $this->oApiContext);
					/*
					 * @PaymentMetaController:updateNextBillingDateGMT
					 * @PostController:migrateToPublish
					 */
					do_action('wiloke-listing-tools/payment-renewed', array(
						'paymentID'          => $paymentID,
						'nextBillingDateGMT' => $oAgreement->agreement_details->next_billing_date,
						'gateway'            => 'paypal',
						'billingType'        => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring')
					));
				}catch (\Exception $ex){

				}

				break;
		}
	}
}
