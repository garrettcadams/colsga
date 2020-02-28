<?php
namespace WilokeListingTools\Framework\Payment\PayPal;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use PayPal\Exception\PayPalConnectionException;

use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Models\InvoiceModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class PayPalExecuteNonRecurringPayment{
	use PayPalGenerateUrls;
	use PayPalConfiguration;
	use PayPalAuthentication;

	public $payerID;
	public $token;
	private $paymentID;
	public $paypalPaymentID;
	protected $aPlan;

	public function executePayment(){
		if ( !isset($_REQUEST['paymentId']) ){
			/*
			 * @PostController:rollupListingToPreviousStatus 10
			 */
			do_action('wiloke-listing-tools/payment-return-cancel-page', array(
				'status'    => 'cancelled',
				'postID'    => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'), true),
			));
			return false;
		}
        $this->aSessionStore = Session::getSession(wilokeListingToolsRepository()->get('payment:paypalTokenAndStoreData'), false);
		/*
		 * It's an array: token presents to key and planId presents to value
		 */
		$this->paypalPaymentID  = $_REQUEST['paymentId'];
		$this->token            = $_REQUEST['token'];
		$this->payerID          = $_REQUEST['PayerID'];
		$this->aPlan            = isset($this->aSessionStore[$this->token]) ? $this->aSessionStore[$this->token] : array();

		$this->setupConfiguration();

		$instPayment = Payment::get($this->paypalPaymentID, $this->oApiContext);

		// Execute payment with payer id
		$instPaymentExecution = new PaymentExecution();
		$instPaymentExecution->setPayerId($this->payerID);

		/*
		 * Get payment ID
		 */
		$this->paymentID = PaymentMetaModel::getPaymentIDByMetaValue($this->token, wilokeListingToolsRepository()->get('payment:paypalTokenAndStoreData'));
		try {
			// Execute payment
			$oResponse = $instPayment->execute($instPaymentExecution, $this->oApiContext);
			$paymentInfo = $oResponse;

			/*
			 * @PaymentStatusController:updatePaymentStatus 5
			 * @PlanRelationshipController:update 5
			 * @UserPlanController:setUserPlan 10
			 * @PostController:setPostDuration 5
			 * @ClaimListingController:paidClaimSuccessfully 10 // Paid Claim
			 */
			$category = Session::getSession(wilokeListingToolsRepository()->get('payment:category'), true);
			$category = empty($category) ? 'dadadzzdadad' : $category;
			$aResponse = apply_filters('wiloke-listing-tools/framework/payment/response', array(
				'status'        => 'succeeded',
				'gateway'       => $this->gateway,
				'billingType'   => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring'),
				'paymentID'     => $this->paymentID,
				'planID'        => isset($this->aPlan['ID']) ? $this->aPlan['ID'] : '',
				'planRelationshipID' => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionRelationshipStore'), true),
				'postID'        => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'), true),
				'claimID'       => Session::getSession(wilokeListingToolsRepository()->get('claim:sessionClaimID'), true),
				'category'      => $category
			));

			Session::destroySession(wilokeListingToolsRepository()->get('payment:paypalTokenAndStoreData'));
			PaymentMetaModel::set($this->paymentID, wilokeListingToolsRepository()->get('payment:paymentInfo'), $paymentInfo);
			PaymentMetaModel::set($this->paymentID, wilokeListingToolsRepository()->get('payment:paypalPaymentID'), $oResponse->id);
			do_action('wiloke-listing-tools/payment-succeeded/'.$category, $aResponse);
			do_action('wiloke-listing-tools/payment-succeeded/listing_plan', $aResponse);
			do_action('wiloke-listing-tools/payment-succeeded', $aResponse);

			/*
			 * We will delete all sessions here
			 */
			do_action('wiloke-submission/payment-succeeded-and-updated-everything');
		}catch(\Exception $ex) {
			/*
			 * @PaymentStatusController:updatePaymentStatus 5
			 * @PaymentStatusController:moveToUnPaid 5
			 * @PostController:rollupListingToPreviousStatus 10
			 */
			do_action('wiloke-listing-tools/payment-failed', array(
				'status'                => 'failed',
				'paymentID'             => $this->paymentID,
				'billingType'           => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring'),
				'planID'                => isset($this->aPlan['ID']) ? $this->aPlan['ID'] : '',
				'gateway'               => $this->gateway,
				'planRelationshipID'    => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionRelationshipStore')),
				'reason'                => $ex->getMessage(),
				'postID'        => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'), true),
			));
			Session::setSession('errorPayment', sprintf(esc_html__('Unfortunately, The payment was failed. Reason: %s', 'wiloke-listing-tools'), $ex->getMessage()));

			FileSystem::filePutContents('paypal-error.log', json_encode(array(
				'paymentID' => $this->paymentID,
				'date'      => current_time('timestamp', true),
				'msg'       => $ex->getMessage()
			)));
		}
	}
}
