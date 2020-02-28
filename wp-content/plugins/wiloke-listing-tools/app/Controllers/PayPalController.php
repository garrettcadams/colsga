<?php
namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Payment\PayPal\PayPalAuthentication;
use WilokeListingTools\Framework\Payment\PayPal\PayPalChangePlan;
use WilokeListingTools\Framework\Payment\PayPal\PayPalExecuteRecurringPayment;
use WilokeListingTools\Framework\Payment\PayPal\PayPalRecurringPaymentMethod;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Payment\Billable;
use WilokeListingTools\Framework\Payment\PayPal\PayPalExecuteNonRecurringPayment;
use WilokeListingTools\Framework\Payment\PayPal\PayPalNonRecurringPaymentMethod;
use WilokeListingTools\Framework\Payment\PayPal\Webhook;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Payment\Checkout;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class PayPalController extends Controller {
	use PayPalAuthentication;
	public $gateway = 'paypal';
	protected $planID;

	public function __construct() {
		add_action('init', array($this, 'listenEvents'), 1);
		add_action('init', array($this, 'paymentExecution'), 1);
		add_action('wp_ajax_wiloke_submission_pay_with_paypal', array($this, 'preparePayment'));
		add_action('wp_ajax_nopriv_wiloke_submission_pay_with_paypal', array($this, 'preparePayment'));
		add_action('wiloke_submission/purchase-event-plan-with-paypal', array($this, 'buyEventPlan'));
		add_action('wp_ajax_wiloke_change_plan_via_paypal', array($this, 'changePlan'));
	}

	public function changePlan(){
		if ( !isset($_POST['newPlanID']) || !isset($_POST['currentPlanID']) || !isset($_POST['paymentID']) || !isset($_POST['postType']) ){
			wp_send_json_error(array(
				'msg' => esc_html__('ERROR: The new plan, current plan, post type and payment ID are required', 'wiloke-listing-tools')
			));
		}

		$userID = get_current_user_id();
		$this->middleware(['isMyPaymentSession'], array(
			'paymentID' => abs($_POST['paymentID']),
			'userID'    => $userID
		));

		$oPayPalChangePlan = new PayPalChangePlan($userID, $_POST['paymentID'], $_POST['newPlanID'], $_POST['currentPlanID'], $_POST['postType']);
		$aStatus = $oPayPalChangePlan->execute();

		if ( $aStatus['status'] == 'success' ){
			wp_send_json_success($aStatus);
		}else{
			if ( isset($aStatus['suspendedOldPlan']) && $aStatus['suspendedOldPlan'] ){
				do_action('wilcity/wiloke-listing-tools/downgrade-to-free-plan', $_POST, $this);
			}
			wp_send_json_error($aStatus);
		}
	}

	public function listenEvents(){
		if ( !isset($_REQUEST['wiloke-submission-listener']) || ($_REQUEST['wiloke-submission-listener'] != $this->gateway) ){
			return false;
		}
		new Webhook();
	}

	public function paymentExecution(){
		if ( !isset($_REQUEST['billingType']) || !$this->isMatchedToken() ){
			return false;
		}

		$this->planID = Session::getSession(wilokeListingToolsRepository()->get('payment:storePlanID'));
		$planType = Session::getSession(wilokeListingToolsRepository()->get('payment:planType'));
		$listingID = '';
		$listingType = '';
		if ( empty($planType) ){
			$listingType = Session::getSession(wilokeListingToolsRepository()->get('payment:listingType'));
			if ( empty($listingType) ){
				$listingID = Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'));
				$listingType = !empty($listingID) ? get_post_type($listingID) : '';
			}
		}

		$paymentID = PaymentMetaModel::getPaymentIDByMetaValue($_GET['token'], wilokeListingToolsRepository()->get('addlisting:tokenSessionIDRelationship'));

		new Billable(
			array(
				'gateway'       => $this->gateway,
				'planID'        => $this->planID,
				'listingType'   => $listingType,
				'listingID'     => $listingID,
				'planType'      => empty($planType) ? '' : $planType,
				'category'      => PaymentModel::getField('packageType', $paymentID)
			)
		);

		if ( !GetWilokeSubmission::isNonRecurringPayment($_REQUEST['billingType']) ){
			$oPayPalMethod = new PayPalExecuteRecurringPayment();
		}else{
			$oPayPalMethod = new PayPalExecuteNonRecurringPayment();
		}

		if ( !$oPayPalMethod ){
			return false;
		}

		$oPayPalMethod->executePayment();
	}

	public static function setupReceiptDirectly($aData){
		$isNonRecurring = true;

		$aDefault = array(
			'userID'                => User::getCurrentUserID(),
			'isNonRecurringPayment' => $aData['billingType'] == wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring'),
			'category'              => Session::getSession(wilokeListingToolsRepository()->get('payment:category'))
		);

		$oReceipt = new Receipt(array_merge($aDefault, $aData));
		$oReceipt->setupPriceDirectly();

		if ( $isNonRecurring ){
			$oPayPalMethod = new PayPalNonRecurringPaymentMethod();
		}else{
			$oPayPalMethod = new PayPalRecurringPaymentMethod();
		}

		$oCheckout = new Checkout();
		$aCheckAcceptPaymentStatus = $oCheckout->begin($oReceipt, $oPayPalMethod);
		return $aCheckAcceptPaymentStatus;
	}

	public function preparePayment($aData=array()){
		$aData = empty($aData) ? $_POST : $aData;
		$this->planID = Session::getSession(wilokeListingToolsRepository()->get('payment:storePlanID'));
		$listingID = Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'));

		new Billable(array(
			'gateway' => $this->gateway,
			'planID'  => $this->planID,
			'listingType' => get_post_type($listingID)
		));

		$isNonRecurring = Session::getSession(wilokeListingToolsRepository()->get('payment:focusNonRecurringPayment'), true);

		if ( !$isNonRecurring ){
			$isNonRecurring = GetWilokeSubmission::isNonRecurringPayment();
		}

		$oReceipt = new Receipt(array(
			'planID'                => $this->planID,
			'userID'                => get_current_user_id(),
			'couponCode'            => $aData['couponCode'],
			'isNonRecurringPayment' => $isNonRecurring,
			'aRequested'            => $_REQUEST
		));
		$oReceipt->setupPlan();

		if ( $isNonRecurring ){
			$oPayPalMethod = new PayPalNonRecurringPaymentMethod();
		}else{
			$oPayPalMethod = new PayPalRecurringPaymentMethod();
		}

		$oCheckout = new Checkout();
		$aCheckAcceptPaymentStatus = $oCheckout->begin($oReceipt, $oPayPalMethod);

		if ( $aCheckAcceptPaymentStatus['status'] == 'success' ){
			wp_send_json_success($aCheckAcceptPaymentStatus);
		}else{
			wp_send_json_error($aCheckAcceptPaymentStatus);
		}
	}

	public function buyEventPlan(){
		// Authentication
		new Billable(array(
			'gateway'   => $this->gateway,
			'aPlanIDs'  => $_POST['eventPlanID']
		));

		$aData = $_POST['aData'];
		$aData['planID'] = $_POST['eventPlanID'];

		$instReceipt = new Receipt($aData);

		$oPayPalMethod = new PayPalNonRecurringPaymentMethod();
		$oCheckout = new Checkout();
		$aPaymentStatus = $oCheckout->begin($instReceipt, $oPayPalMethod);

		if ( $aPaymentStatus['status'] == 'success' ){
			wp_send_json_success($aPaymentStatus);
		}else{
			wp_send_json_error($aPaymentStatus);
		}
	}
}
