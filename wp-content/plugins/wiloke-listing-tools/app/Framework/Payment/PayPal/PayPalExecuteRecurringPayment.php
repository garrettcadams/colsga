<?php
namespace WilokeListingTools\Framework\Payment\PayPal;

use PayPal\Exception\PayPalConnectionException;
use PayPal\Api\Agreement;

use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Models\PaymentMetaModel;

class PayPalExecuteRecurringPayment
{
    use PayPalValidations;
    use PayPalConfiguration;
    use PayPalAuthentication;
    protected $storeTokenPlanSession;
    protected $aPlan;
    protected $paymentID;
    protected $token;

    public function executePayment()
    {
        $this->aSessionStore = Session::getSession(wilokeListingToolsRepository()->get('payment:paypalTokenAndStoreData'), false);
        if (GetWilokeSubmission::isCancelPage()) {
            /*
             * @PostController:rollupListingToPreviousStatus 10
             */
            do_action('wiloke-listing-tools/payment-return-cancel-page', [
                'status' => 'cancelled',
                'postID' => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'),
                    true),
            ]);

            return false;
        }

        /*
         * It's an array: token presents to key and planId presents to value
         */
        $this->token = $_GET['token'];
        $this->aPlan = $this->aSessionStore[$this->token];
        $agreement   = new Agreement();
        $this->setupConfiguration();

        /*
         * Get Session ID
         */
        $this->paymentID = PaymentMetaModel::getPaymentIDByMetaValue($this->token,
            wilokeListingToolsRepository()->get('payment:paypalTokenAndStoreData'));

        try {
            // Execute agreement
            $oResult = $agreement->execute($this->token, $this->oApiContext);
            PaymentMetaModel::set($this->paymentID, wilokeListingToolsRepository()->get('payment:paymentInfo'),
                $oResult);
            $isTrial = Session::getSession(wilokeListingToolsRepository()->get('addlisting:storeIsTrial'), true);
            /*
             * @PaymentStatusController:updatePaymentStatus 5
             * @PlanRelationshipController:update 5
             * @PaymentMetaController:setTrial 10
             * @UserPlanController:setUserPlan 10
             * @PostController:setPostDuration 5
             * @ClaimListingController:paidClaimSuccessfully 10 // Paid Claim
             */
            $category = Session::getSession(wilokeListingToolsRepository()->get('payment:category'), true);
            $category = empty($category) ? 'dadadzzdadad' : $category;
            $listingType = Session::getSession(wilokeListingToolsRepository()->get('payment:listingType'),
                true);

            $aResponse = apply_filters('wiloke-listing-tools/framework/payment/response', [
                'status'             => 'active',
                'gateway'            => $this->gateway,
                'billingType'        => wilokeListingToolsRepository()
                    ->get('payment:billingTypes', true)
                    ->sub('recurring'),
                'listingType'        => $listingType,
                'onChangedPlan'      => Session::getSession(wilokeListingToolsRepository()->get('payment:onChangedPlan'),
                    true),
                'paymentID'          => $this->paymentID,
                'planID'             => $this->aPlan['ID'],
                'oldPlanID'          => Session::getSession(wilokeListingToolsRepository()->get('payment:oldPlanID'),
                    true),
                'isTrial'            => $isTrial,
                'nextBillingDateGMT' => $oResult->agreement_details->next_billing_date,
                'planRelationshipID' => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionRelationshipStore'),
                    true),
                'postID'             => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'),
                    true),
                'userID'             => get_current_user_id(),
                'claimID'            => Session::getSession(wilokeListingToolsRepository()->get('claim:sessionClaimID'),
                    true),
                'category'           => $category
            ]);
            PaymentMetaModel::set($this->paymentID, wilokeListingToolsRepository()->get('addlisting:paypalAgreementID'),
                $oResult->id);

            do_action('wiloke-listing-tools/payment-succeeded/'.$category, $aResponse);

            if ($listingType == 'event') {
                do_action('wiloke-listing-tools/payment-succeeded/event_plan', $aResponse);
            } else {
                do_action('wiloke-listing-tools/payment-succeeded/listing_plan', $aResponse);
            }

//            do_action('wiloke-listing-tools/payment-succeeded/listing_plan', $aResponse);
            do_action('wiloke-listing-tools/payment-succeeded', $aResponse);
            do_action('wiloke-listing-tools/subscription-created', $aResponse);

            if ($aResponse['onChangedPlan'] == 'yes') {
                do_action(
                    'wiloke-listing-tools/on-changed-user-plan',
                    PaymentMetaModel::get($this->paymentID, 'change_plan_info'),
                    $this->paymentID
                );
            }

            $aReturn = [
                'status' => 'success',
                'data'   => $oResult
            ];

            /*
             * We will delete all sessions here
             */
            do_action('wiloke-submission/payment-succeeded-and-updated-everything');

        } catch (\Exception $ex) {

            /*
             * @PaymentStatusController:updatePaymentStatus 5
             * @PaymentStatusController:moveToUnPaid 5
             * @PostController:rollupListingToPreviousStatus 10
             */
            do_action('wiloke-listing-tools/payment-failed', [
                'status'    => 'failed',
                'paymentID' => $this->paymentID,
                'postID'    => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'),
                    true),
            ]);

            $aReturn = [
                'status' => 'error',
                'data'   => $ex->getMessage()
            ];

            FileSystem::filePutContents('paypal-error.log', json_encode([
                'paymentID' => $this->paymentID,
                'date'      => current_time('timestamp', true),
                'msg'       => $ex->getMessage()
            ]));
        }

        return $aReturn;
    }
}
