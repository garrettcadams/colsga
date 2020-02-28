<?php
namespace WilokeListingTools\Framework\Payment\PayPal;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Exception\PayPalConnectionException;

use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Framework\Payment\PaymentMethodInterface;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class PayPalNonRecurringPaymentMethod implements PaymentMethodInterface{
	use PayPalGenerateUrls;
	use PayPalConfiguration;

	protected $storeTokenPlanSession;
	protected $oReceipt;
	protected $thankyouUrl = null;
	protected $cancelUrl = null;
	public $token = null;
	protected $aPayPayConfiguration;
	protected $instPayPalConfiguration;

	public function getBillingType() {
		return wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring');
	}

	private function parseTokenFromApprovalUrl($approvalUrl){
		$aParseData = explode('token=', $approvalUrl);
		$this->token = trim($aParseData[1]);
	}

	private function getApprovalUrl(){
		// Create new payer and method
		$payer = new Payer();
		$payer->setPaymentMethod('paypal');

		$instPlan = new Item();
		$instPlan->setName($this->oReceipt->aPlan['planName'])
		         ->setCurrency($this->aConfiguration['currency_code'])
		         ->setQuantity(1)
		         ->setPrice($this->oReceipt->subTotal);
		$aItems[] = $instPlan;
		if ( isset($this->oReceipt->aCouponInfo['discountPrice']) && !empty($this->oReceipt->aCouponInfo['discountPrice']) ){
			// Maybe discount
			$discountPrice = '-' . $this->oReceipt->aCouponInfo['discountPrice'];
			settype($discountPrice, 'string');

			$insDiscount = new Item();
			$insDiscount->setName($this->oReceipt->aCouponInfo['planName'])
			      ->setCurrency($this->aConfiguration['currency_code'])
			      ->setQuantity(1)
			      ->setPrice($discountPrice);
			$aItems[] = $insDiscount;
		}
		$instItemList = new ItemList();
		$instItemList->setItems($aItems);

		$instDetails = new Details();
		$instDetails->setSubtotal($this->oReceipt->subTotal - $this->oReceipt->aCouponInfo['discountPrice']);
		$instAmount = new Amount();
		$instAmount->setCurrency($this->aConfiguration['currency_code'])
		       ->setTotal($this->oReceipt->subTotal - $this->oReceipt->aCouponInfo['discountPrice'])
		       ->setDetails($instDetails);

		// Set transaction object
		$transaction = new Transaction();
		$transaction->setItemList($instItemList)
					->setAmount($instAmount)
					->setInvoiceNumber(uniqid())
		            ->setDescription($this->paymentDescription);
		// Set redirect urls
		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl($this->thankyouUrl)
		             ->setCancelUrl($this->cancelUrl);

		// Create the full payment object
		$payment = new Payment();
		$payment->setIntent('sale')
		        ->setPayer($payer)
		        ->setRedirectUrls($redirectUrls)
		        ->setTransactions(array($transaction));
		// Create payment with valid API context

		try {
			$payment->create($this->oApiContext);
			// Get PayPal redirect URL and redirect user
			$approvalUrl = $payment->getApprovalLink();
			$this->parseTokenFromApprovalUrl($approvalUrl);

			// Insert wiloke_submission_transaction and wiloke_submission_paypal_nonrecurring_payment before redirecting to PayPal
			$paymentID = PaymentModel::setPaymentHistory($this, $this->oReceipt);

			if ( empty($paymentID) ){
				Message::error(esc_html__('Could not insert Payment History', 'wiloke-listing-tools'));
			}

			if ( empty($paymentID) ){
				return array(
					'status' => 'error',
					'msg'    => esc_html__('We could not create this session', 'wiloke-listing-tools')
				);
			}else{
				$status = $this->storeTokenAndPlanId($paymentID);
				if ( !$status ){
					return array(
						'status' => 'error',
						'msg'    => esc_html__('We could not save Token, Plan Relationship.', 'wiloke-listing-tools')
					);
				}
				/*
				 * @hooked EventController@setPlanRelationshipBeforePayment
				 */
				$aResponse = array(
					'paymentID'     => $paymentID,
					'planID'        => $this->oReceipt->planID,
					'gateway'       => $this->gateway
				);

//				$category = Session::getSession(wilokeListingToolsRepository()->get('payment:category'));

				do_action('wiloke-listing-tools/before-payment-process/'.$this->oReceipt->getPackageType(), $aResponse);
				do_action('wiloke-listing-tools/before-payment-process', $aResponse);

				return array(
					'status'    => 'success',
					'msg'       => esc_html__('Got Approval url', 'wiloke-listing-tools'),
					'redirectTo'=> $approvalUrl,
					'paymentID' => $paymentID
				);
			}
		} catch (PayPalConnectionException $ex) {
			return array(
				'code'   => $ex->getCode(),
				'status' => 'error',
				'msg'    => $ex->getMessage()
			);
		} catch (\Exception $ex) {
			return array(
				'status' => 'error',
				'msg'    => $ex->getMessage()
			);
		}
	}

	protected function setup(){
		$this->cancelUrl();
		$this->thankyouUrl();
		$this->setupConfiguration();
	}

	public function proceedPayment(Receipt $oReceipt){
		$this->oReceipt = $oReceipt;
		$this->setup();
		$aResult = $this->getApprovalUrl();

		return $aResult;
	}
}