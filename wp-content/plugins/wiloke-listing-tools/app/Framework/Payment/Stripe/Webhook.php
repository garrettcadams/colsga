<?php

namespace WilokeListingTools\Framework\Payment\Stripe;


use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Models\InvoiceModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class Webhook {
	use StripeConfiguration;
	private $insertedInvoice = false;

	public function __construct() {
		$this->listener();
	}

	public function listener() {
		$this->setApiContext();
		$rawdata = file_get_contents( 'php://input' );
		if ( empty( $rawdata ) ) {
			return '';
		}
		$oEventInfo = json_decode($rawdata);

		FileSystem::logPayment('stripe.log', $rawdata);

		switch ( $oEventInfo->type ) {
			case 'charge.failed':
				break;
			case 'charge.refunded':
				$chargeID = $oEventInfo->data->object->id;
				$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($chargeID, wilokeListingToolsRepository()->get('payment:stripeChargedID'));

				if ( empty($paymentID) ){
					return false;
				}

				PaymentModel::updatePaymentStatus('refunded', $paymentID);

				do_action('wilcity/stripe/insert-invoice', $paymentID, array(
					'currency'  => $oEventInfo->data->object->currency,
					'subTotal'  => -GetWilokeSubmission::convertStripePrice($oEventInfo->data->object->amount_refunded),
					'discount'  => 0,
					'tax'       => 0,
					'total'     => -GetWilokeSubmission::convertStripePrice($oEventInfo->data->object->amount_refunded),
				));

				/*
				 * @hooked: PostController:moveAllPostsToTrash
				 */
				do_action('wiloke-listing-tools/payment-refunded', array(
					'paymentID' => $paymentID,
					'gateway'   => 'stripe'
				));
				break;
			case 'customer.subscription.created':
				$subscriptionID = $oEventInfo->data->object->id;
				$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($subscriptionID, wilokeListingToolsRepository()->get('payment:stripeSubscriptionID'));
				if ( empty($paymentID) ){
					return false;
				}

				$gmtNow = current_time('timestamp', true);
				if ( $gmtNow > $oEventInfo->data->object->trial_end ){
					return false;
				}

				/*
				 * @UserPlanController@updateNextBillingDateGMT 10
				 * @PaymentMetaController@updateNextBillingDateGMT 10
				 * @PostController@migrateToPublish 20
				 */
				do_action('wiloke-listing-tools/payment-renewed', array(
					'nextBillingDateGMT' => $oEventInfo->data->object->trial_end,
					'paymentID'          => $paymentID,
					'gateway'            => 'stripe',
					'billingType'        => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring')
				));
				break;
			case 'invoice.payment_succeeded':
				$subscriptionID = $oEventInfo->data->object->subscription;
				$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($subscriptionID, wilokeListingToolsRepository()->get('payment:stripeSubscriptionID'));

				if ( empty($paymentID) ){
					return false;
				}

				do_action('wilcity/stripe/insert-invoice', $paymentID, array(
					'subTotal'  => GetWilokeSubmission::convertStripePrice($oEventInfo->data->object->subtotal),
					'total'     => GetWilokeSubmission::convertStripePrice($oEventInfo->data->object->total),
					'currency'  => $oEventInfo->data->object->currency,
					'discount'  => 0,
					'tax'       => !empty($oEventInfo->data->object->tax) ? GetWilokeSubmission::convertStripePrice
                    ($oEventInfo->data->object->tax) : 0,
                    'token'     => $oEventInfo->id
				));

				PaymentMetaModel::delete($paymentID, wilokeListingToolsRepository()->get('addlisting:isUsingTrial'));

				/*
				 * @UserPlanController@updateNextBillingDateGMT 10
				 * @PaymentMetaController@updateNextBillingDateGMT 10
				 * @PostController@migrateToPublish 20
				 */
				do_action('wiloke-listing-tools/payment-renewed', array(
					'nextBillingDateGMT' => $oEventInfo->data->object->period_end,
					'paymentID'          => $paymentID,
					'gateway'            => 'stripe',
					'billingType'        => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring')
				));
				break;
			case 'customer.subscription.trial_will_end':
				$subscriptionID = $oEventInfo->data->object->id;
				$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($subscriptionID, wilokeListingToolsRepository()->get('payment:stripeSubscriptionID'));

				if ( empty($paymentID) ){
					return false;
				}

				/*
				 * @UserPlanController@updateNextBillingDateGMT
				 */
				do_action('wiloke-listing-tools/payment-renewed', array(
					'nextBillingDateGMT' => $oEventInfo->data->object->trial_end,
					'paymentID'          => $paymentID,
					'gateway'            => 'stripe',
					'billingType'        => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring')
				));
				break;
			case 'invoice.payment_failed':
				$subscriptionID = $oEventInfo->data->object->subscription;
				$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($subscriptionID, wilokeListingToolsRepository()->get('payment:stripeSubscriptionID'));

				if ( empty($paymentID) ){
					return false;
				}

				do_action('wilcity/stripe/invoice/payment-failed', $paymentID, array(
					'subTotal'  => GetWilokeSubmission::convertStripePrice($oEventInfo->data->object->subtotal),
					'total'     => GetWilokeSubmission::convertStripePrice($oEventInfo->data->object->total),
					'currency'  => $oEventInfo->data->object->currency,
					'discount'  => 0,
					'tax'       => !empty($oEventInfo->data->object->tax) ? GetWilokeSubmission::convertStripePrice($oEventInfo->data->object->tax) : 0
				));
				break;
			case 'customer.subscription.deleted':
				$subscriptionID = $oEventInfo->data->object->id;
				$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($subscriptionID, wilokeListingToolsRepository()->get('payment:stripeSubscriptionID'));

				if ( empty($paymentID) ){
					return false;
				}

				PaymentModel::updatePaymentStatus('cancelled', $paymentID);
				/*
				 * @PaymentMetaController:updateNextBillingDateGMT
				 * @PaymentMetaController:moveAllPostsToExpiry
				 */
				do_action('wiloke-listing-tools/payment-cancelled', array(
					'paymentID' => $paymentID,
					'gateway'   => 'stripe'
				));
				break;
		}
	}
}
