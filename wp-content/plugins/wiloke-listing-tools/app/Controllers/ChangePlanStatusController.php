<?php
namespace WilokeListingTools\Controllers;


use WilokeListgoFunctionality\Framework\Payment\PayPal\PayPalRefundNonRecurringPayment;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Payment\PayPal\PayPalCancelRecurringPayment;
use WilokeListingTools\Framework\Payment\Stripe\StripeCancelRecurringPayment;
use WilokeListingTools\Framework\Payment\Stripe\StripeRefundNonRecurringPayment;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Models\InvoiceModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\UserModel;

class ChangePlanStatusController extends Controller {
	public function __construct() {
		add_action('wp_ajax_change_sale_status', array($this, 'changeSaleStatus'));
		add_action('wp_ajax_cancel_subscription', array($this, 'cancelSubscription'));
		add_action('wp_ajax_change_banktransfer_order_status_NonRecurringPayment', array($this, 'changeBankTransferNonRecurringPaymentStatus'));
		add_action('wp_ajax_change_banktransfer_order_status_RecurringPayment', array($this, 'changeBankTransferRecurringPaymentStatus'));
		add_action('wp_ajax_refund_sale', array($this, 'refundSale'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
	}

	public function validatePaymentID($paymentID){
		if ( empty($paymentID) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('The payment ID is required.', 'wiloke-listing-tools')
				)
			);
		}
	}

	public function isBankTransferGateway($gateway){
		if ( empty($gateway) || $gateway != 'banktransfer' ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('You can not change this gateway.', 'wiloke-listing-tools')
				)
			);
		}
	}

	public function isRefundedStatus($status){
		if ( $status == 'refunded' ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Refund is a permanent status. You can not change that.', 'wiloke-listing-tools')
				)
			);
		}
	}

	public function enqueueScripts(){
		wp_enqueue_script('plan-controller', WILOKE_LISTING_TOOL_URL . 'admin/source/js/plan-controller.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);
	}

	public function isCancelledOrRefundedStatus($status){
		if ( in_array($status, array('cancelled', 'refunded')) ){
			Message::error(esc_html__('This status is permanent, you can not change that.', 'wiloke-listing-tools'));
		}

		return true;
	}

	public function refundSale(){
		$this->middleware(['isAdministrator']);

		if ( empty($_POST['paymentID']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('The payment ID is required', 'wiloke-listing-tools')
				)
			);
		}

		if ( empty($_POST['gateway']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('The gateway is required', 'wiloke-listing-tools')
				)
			);
		}

		$currentStatus = PaymentModel::getField('status', $_POST['paymentID']);
		$this->isCancelledOrRefundedStatus($currentStatus);

		switch ($_POST['gateway']){
			case 'paypal':
				$oRefundSale = new PayPalRefundNonRecurringPayment();
				$aResponse = $oRefundSale->execute($_POST['paymentID']);
				if ( $aResponse['status'] = 'success' ){
					do_action('wiloke-listing-tools/payment-refunded', array(
						'paymentID'  => $_POST['paymentID'],
						'gateway'    => 'paypal',
						'order_status' => 'cancelled'
					));

					wp_send_json_success(
						array(
							'msg' => array(
								'order_status' => 'cancelled'
							)
						)
					);
				}else{
					wp_send_json_error(array(
						'msg' => strip_tags($aResponse['msg'])
					));
				}
				break;
			case 'stripe':
				$oCancelStripe = new StripeRefundNonRecurringPayment();
				$aResponse = $oCancelStripe->execute($_POST['paymentID']);

				if ( $aResponse['status'] = 'success' ){
					do_action('wiloke-listing-tools/payment-cancelled', array(
						'paymentID'     => $_POST['paymentID'],
						'gateway'       => 'stripe',
						'order_status'  => 'cancelled'
					));

					wp_send_json_success(
						array(
							'msg' => array(
								'order_status' => 'cancelled'
							)
						)
					);
				}else{
					wp_send_json_error(array(
						'msg' => strip_tags($aResponse['msg'])
					));
				}
				break;
			default:
				wp_send_json_error(
					array(
						'msg' => esc_html__('Wrong Payment Gateway', 'wiloke-listing-tools')
					)
				);
				break;
		}
	}

	public function cancelSubscription(){
		$this->middleware(['isAdministrator']);

		$currentStatus = PaymentModel::getField('status', $_POST['paymentID']);
		$this->isCancelledOrRefundedStatus($currentStatus);

		if ( empty($_POST['paymentID']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('The payment ID is required', 'wiloke-listing-tools')
				)
			);
		}

		if ( empty($_POST['gateway']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('The gateway is required', 'wiloke-listing-tools')
				)
			);
		}

		switch ($_POST['gateway']){
			case 'paypal':
				$oCancelPayPal = new PayPalCancelRecurringPayment();
				$aResponse = $oCancelPayPal->execute($_POST['paymentID']);

				if ( $aResponse['status'] = 'success' ){
					do_action('wiloke-listing-tools/payment-cancelled', array(
						'paymentID'  => $_POST['paymentID'],
						'gateway'    => 'paypal',
						'order_status'  => 'cancelled'
					));

					wp_send_json_success(
						array(
							'order_status'  => 'cancelled'
						)
					);
				}else{
					wp_send_json_error(array(
						'msg' => strip_tags($aResponse['msg'])
					));
				}
				break;
			case 'stripe':
				$oCancelStripe = new StripeCancelRecurringPayment();
				$aResponse = $oCancelStripe->execute($_POST['paymentID']);

				if ( $aResponse['status'] = 'success' ){
					do_action('wiloke-listing-tools/payment-cancelled', array(
						'paymentID'  => $_POST['paymentID'],
						'gateway'    => 'stripe',
						'order_status'  => 'cancelled'
					));

					wp_send_json_success(
						array(
							'order_status'  => 'cancelled'
						)
					);
				}else{
					wp_send_json_error(array(
						'msg' => strip_tags($aResponse['msg'])
					));
				}
				break;
			default:
				wp_send_json_error(
					array(
						'msg' => esc_html__('Wrong Payment Gateway', 'wiloke-listing-tools')
					)
				);
				break;
		}
	}

	public function changeSaleStatus(){
		$this->middleware(['isAdministrator']);

		if ( empty($_POST['paymentID']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('The payment ID is required', 'wiloke-listing-tools')
				)
			);
		}

		$currentStatus = PaymentModel::getField('status', $_POST['paymentID']);

		if (  in_array($currentStatus, array('cancelled', 'refunded')) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('This plan has been cancelled. You can change the status anymore.', 'wiloke-listing-tools')
				)
			);
		}

		if ( $_POST['newStatus'] == 'cancelled_and_unpublish_listing' ){
			$newStatus = 'cancelled';
		}else{
			$newStatus = $_POST['newStatus'];
		}

		PaymentModel::updatePaymentStatus($newStatus, $_POST['paymentID']);

		/*
		 * @PaymentMetaController:setNewNextBillingDateGMT 5
		 * @UserPlanController:deletePlanIfIsCancelled
		 * @UserPlanController:updateUserPlanIfSucceededOrActivate
		 * @InvoiceController:update
		 */
		do_action(
			'wiloke-listing-tools/changed-payment-status',
			array(
				'billingType' => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring'),
				'newStatus' => $_POST['newStatus'],
				'oldStatus' => $_POST['currentStatus'],
				'paymentID' => $_POST['paymentID']
			)
		);

		wp_send_json_success(array(
			'msg' => array(
				'order_status'      => $_POST['newStatus'],
				'next_billing_date' => Time::toAtom(PaymentMetaModel::getNextBillingDateGMT($_POST['paymentID']))
			)
		));
	}

	public function changeBankTransferRecurringPaymentStatus(){
		$this->middleware(['isAdministrator']);
		$this->validatePaymentID($_POST['paymentID']);
		$this->isBankTransferGateway($_POST['gateway']);

		$invoiceID = InvoiceModel::getInvoiceIDByPaymentID($_POST['paymentID']);
		$userID = PaymentModel::getField('userID', $_POST['paymentID']);
		$planID = PaymentModel::getField('planID', $_POST['paymentID']);
		$aTransactionInfo = PaymentMetaModel::get($_POST['paymentID'], wilokeListingToolsRepository()->get('payment:paymentInfo'));
		$aPlanSettings = GetSettings::getPlanSettings($aTransactionInfo['plan']['ID']);

		if ( $_POST['newStatus'] == 'active' ){
			if ( empty($invoiceID) ){
				if ( !empty($aPlanSettings['trial_period']) ){
					$plusDays = '+' . $aPlanSettings['trial_period'] . ' day';
					$isTrial = true;
				}else{
					$plusDays = '+' . $aPlanSettings['regular_period'] . 'day';
					$isTrial = false;
				}

				$nextBillingDateGMT = Time::timestampUTCNow($plusDays);
				$instUserModel = new UserModel();

				// Check User Plan is existed or not
				if ( !UserModel::isPlanExist($userID, $planID) ){
					$postType = PaymentMetaModel::getPostTypeByPlanID($planID);
					$instUserModel->setUserID($userID)
						          ->setPlanID($planID)
						          ->setNextBillingDateGMT($nextBillingDateGMT)
								  ->setPaymentID($_POST['paymentID'])
						          ->setGateway($_POST['gateway'])
						          ->setBillingType('RecurringPayment')
						          ->setPostType($postType)
								  ->setIsTrial($isTrial);
					$instUserModel->setUserPlan();
				}else{
					$instUserModel->updateNextBillingDateGMT($nextBillingDateGMT, $planID, $userID, $_POST['paymentID']);
				}

				do_action('wilcity/direct-bank-transfer/insert-invoice', $_POST['paymentID'], array(
					'currency'      => $aTransactionInfo['currency'],
					'subTotal'      => $aTransactionInfo['subTotal'],
					'discount'      => $aTransactionInfo['discount'],
					'tax'           => $aTransactionInfo['tax'],
					'total'         => $aTransactionInfo['total']
				));

				/*
				 * @ClaimListingController:paidClaimSuccessfully 10 // Paid Claim
				 */
				do_action('wiloke-listing-tools/payment-succeeded', apply_filters('wiloke-listing-tools/framework/payment/response', array(
					'paymentID'          => $_POST['paymentID'],
					'nextBillingDateGMT' => $nextBillingDateGMT,
					'gateway'            => 'banktransfer',
					'billingType'        => PaymentModel::getField('billingType', $_POST['paymentID']),
					'planID'             => $planID,
					'userID'             => $userID,
					'isTrial'            => $isTrial
				)));
			}else{
				/*
				 * @hooked PostController:migrateToPublish
				 */
				do_action('wiloke-listing-tools/subscription-reactived', array(
					'paymentID'          => $_POST['paymentID'],
					'gateway'            => 'banktransfer'
				));
			}
			PaymentModel::updatePaymentStatus($_POST['newStatus'], $_POST['paymentID']);
		}else{
			PaymentModel::updatePaymentStatus($_POST['newStatus'], $_POST['paymentID']);
			/*
			 * @PaymentMetaController:updateNextBillingDateGMT
			 * @PaymentMetaController:moveAllPostsToExpiry
			 */
			do_action('wiloke-listing-tools/payment-'.$_POST['newStatus'], array(
				'paymentID'  => $_POST['paymentID'],
				'gateway'    => 'banktransfer'
			));
		}

		$packageType = PaymentModel::getField('packageType', $_POST['paymentID']);
		do_action('wiloke-listing-tools/after-changed-payment-status/'.$packageType, array(
			'paymentID'     => $_POST['paymentID'],
			'gateway'       => 'banktransfer',
			'userID'        => PaymentModel::getField('userID', $_POST['paymentID']),
			'planID'        => PaymentModel::getField('planID', $_POST['paymentID']),
			'billingType'   => PaymentModel::getField('billingType', $_POST['paymentID']),
			'newStatus'     => $_POST['newStatus']
		));

		wp_send_json_success(
			array(
				'msg' => array(
					'order_status' => $_POST['newStatus']
				)
			)
		);
	}

	public function changeBankTransferNonRecurringPaymentStatus(){
		$this->middleware(['isAdministrator']);
		$this->validatePaymentID($_POST['paymentID']);
		$this->isBankTransferGateway($_POST['gateway']);
		$this->isRefundedStatus(PaymentModel::getField('status', $_POST['paymentID']));

		PaymentModel::updatePaymentStatus($_POST['newStatus'], $_POST['paymentID']);

		$aResponse = array(
			'paymentID'     => $_POST['paymentID'],
			'gateway'       => 'banktransfer',
			'userID'        => PaymentModel::getField('userID', $_POST['paymentID']),
			'planID'        => PaymentModel::getField('planID', $_POST['paymentID']),
			'billingType'   => PaymentModel::getField('billingType', $_POST['paymentID']),
			'newStatus'     => $_POST['newStatus']
		);

		$packageType = PaymentModel::getField('packageType', $_POST['paymentID']);
		$aResponse['status'] = $_POST['newStatus'];
		do_action('wiloke-listing-tools/payment-'.$_POST['newStatus'], $aResponse);
		do_action('wiloke-listing-tools/payment-'.$_POST['newStatus'].'/'.$packageType, $aResponse);
		do_action('wiloke-listing-tools/after-changed-payment-status/'.$packageType, $aResponse);

		$invoiceID = InvoiceModel::getInvoiceIDByPaymentID($_POST['paymentID']);
		if ( $_POST['newStatus'] == 'refunded' ){
			$aInvoice = InvoiceModel::getAll($invoiceID);
			$aInvoice['total']      = -$aInvoice['total'];
			$aInvoice['subTotal']   = -$aInvoice['subTotal'];
			$aInvoice['discount']   = 0;
			$aInvoice['tax']        = 0;

			unset($aInvoice['ID']);
			InvoiceModel::set(
				$_POST['paymentID'],
				$aInvoice
			);
		}else if ( $_POST['newStatus'] == 'succeeded' ){
			if ( empty($invoiceID) ){
				$aTransactionInfo = PaymentMetaModel::get($_POST['paymentID'], wilokeListingToolsRepository()->get('payment:paymentInfo'));

				do_action('wilcity/direct-bank-transfer/insert-invoice', $_POST['paymentID'], array(
					'currency'      => $aTransactionInfo['currency'],
					'subTotal'      => $aTransactionInfo['subTotal'],
					'discount'      => $aTransactionInfo['discount'],
					'tax'           => $aTransactionInfo['tax'],
					'total'         => $aTransactionInfo['total']
				));
			}
		}else{
			if ( !empty($invoiceID) ){
				InvoiceModel::delete($invoiceID);
			}
		}

		wp_send_json_success(
			array(
				'msg' => array(
					'order_status' => $_POST['newStatus']
				)
			)
		);
	}
}