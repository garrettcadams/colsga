<?php

namespace WilokeListingTools\Framework\Payment\Stripe;

use Stripe\Customer;
use Stripe\Subscription;
use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Payment\PaymentMethodInterface;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\PlanRelationshipModel;
use WilokeListingTools\Models\UserModel;

class StripeRecurringPaymentMethod implements PaymentMethodInterface
{
    use StripeConfiguration;
    protected $aSucceeded;
    protected $storeTokenPlanSession;
    protected $paymentID;
    protected $oReceipt;
    protected $userID;

    public function getBillingType()
    {
        return wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring');
    }

    protected function setup()
    {
        $this->token  = $this->oReceipt->aInfo['token'];
        $this->userID = User::getCurrentUserID();
        $this->setApiContext();
    }

    // If a plan has been failed, we will cancel the other plans
    protected function rollup()
    {
        if ($this->aSucceeded) {
            foreach ($this->aSucceeded as $subscriptionID) {
                $oSubscription = \Stripe\Subscription::retrieve($subscriptionID);
                $oSubscription->cancel();
            }
        }
    }

    private function retrievePlan($aPlan)
    {
        try {
            $oPlanInfo = \Stripe\Plan::retrieve($aPlan['slug']);

            return $oPlanInfo;
        } catch (\Exception $oException) {
            return false;
        }
    }

    /*
     * If the plan does not exist, we will create a new plan
     */
    private function createPlan($aPlan)
    {
        $result = $this->retrievePlan($aPlan);
        if (!$result) {
            \Stripe\Plan::create([
                'currency'       => $this->aConfiguration['currency_code'],
                'interval'       => 'day',
                'product'        => [
                    'name' => $aPlan['planName'],
                ],
                'amount'         => floatval($aPlan['total'] * $this->oApiContext->zeroDecimal),
                'id'             => $aPlan['slug'],
                'interval_count' => $aPlan['regularPeriod']
            ]);
        }
    }

    private function insertingNewSession()
    {
        $this->paymentID = PaymentModel::setPaymentHistory($this, $this->oReceipt);
    }

    protected function charge($oCustomer)
    {
        try {
            $this->insertingNewSession();
            if (empty($this->paymentID)) {
                Message::error(esc_html__('Could not insert Payment History', 'wiloke-listing-tools'));
            }

            $this->createPlan($this->oReceipt->aPlan);
            $aConfiguration = [
                'customer' => $oCustomer->id,
                'items'    => [
                    [
                        'plan' => $this->oReceipt->aPlan['slug'] // post_slug
                    ]
                ]
            ];

            $isTrial = false;
            if (!empty($this->oReceipt->aPlan['trialPeriod'])) {
                $isTrial                             = true;
                $aConfiguration['trial_period_days'] = $this->oReceipt->aPlan['trialPeriod'];
            }

            /*
             * @hooked EventController@setPlanRelationshipBeforePayment
             */
            do_action('wiloke-listing-tools/before-payment-process', [
                'paymentID' => $this->paymentID,
                'planID'    => $this->oReceipt->planID,
                'gateway'   => $this->gateway
            ]);
            $oSubscription = Subscription::create($aConfiguration);

            $this->aSucceeded[] = $oSubscription->id;
            PaymentMetaModel::set($this->paymentID, wilokeListingToolsRepository()->get('payment:paymentInfo'),
                $oSubscription->__toArray());
            PaymentMetaModel::set($this->paymentID, wilokeListingToolsRepository()->get('payment:stripeSubscriptionID'),
                $oSubscription->id);

            /*
             * @PaymentStatusController:updatePaymentStatus 5
             * @PlanRelationshipController:update 5
             * @UserPlanController:setUserPlan 10
             * @PaymentMetaController:setTrial 10
             * @ClaimListingController:paidClaimSuccessfully 10 // Paid Claim
             * @PostController:migrateToPublish 30
             */
            $aResponse = apply_filters('wiloke-listing-tools/framework/payment/response', [
                'status'             => 'active',
                'gateway'            => $this->gateway,
                'billingType'        => $this->getBillingType(),
                'onChangedPlan'      => Session::getSession(wilokeListingToolsRepository()->get('payment:onChangedPlan'),
                    true),
                'listingType'        => Session::getSession(wilokeListingToolsRepository()->get('payment:listingType'),
                    true),
                'oldPlanID'          => Session::getSession(wilokeListingToolsRepository()->get('payment:oldPlanID'),
                    true),
                'paymentID'          => $this->paymentID,
                'planID'             => abs($this->oReceipt->planID),
                'isTrial'            => $isTrial,
                'planRelationshipID' => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionRelationshipStore'),
                    true),
                'postID'             => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'),
                    true),
                'nextBillingDateGMT' => isset($oSubscription->trial_end) ? $oSubscription->trial_end : $oSubscription->current_period_end,
                'userID'             => User::getCurrentUserID(),
                'claimID'            => Session::getSession(wilokeListingToolsRepository()->get('claim:sessionClaimID'),
                    true)
            ]);

            $category = Session::getSession(wilokeListingToolsRepository()->get('payment:category'), true);
            $category = empty($category) ? 'dadadzzdadad' : $category;
            do_action('wiloke-listing-tools/payment-succeeded/'.$category, $aResponse);
            do_action('wiloke-listing-tools/payment-succeeded/'.$this->oReceipt->getPackageType(), $aResponse);
            do_action('wiloke-listing-tools/payment-succeeded', $aResponse);
            do_action('wiloke-listing-tools/subscription-created', $aResponse);

            /*
             * We will delete all sessions here
             */
            do_action('wiloke-submission/payment-succeeded-and-updated-everything');

            return [
                'status'    => 'success',
                'msg'       => esc_html__('Congratulations! Your payment has been processed successfully',
                    'wiloke-listing-tools'),
                'paymentID' => $this->paymentID,
                'thankyou'  => apply_filters('wilcity/wiloke-listing-tools/stripe/successfully',
                    GetWilokeSubmission::getField('thankyou', true), $aResponse)
            ];
        } catch (\Exception $oE) {
            $this->rollup();

            /*
             * @PaymentStatusController:updatePaymentStatus 5
             * @PostController:rollupListingToPreviousStatus 10
             */
            do_action('wiloke-listing-tools/payment-failed', [
                'status'    => 'failed',
                'paymentID' => $this->paymentID,
                'postID'    => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'),
                    true),
            ]);

            FileSystem::logPayment('stripe-error.log', json_encode([
                'paymentID' => $this->paymentID,
                'date'      => current_time('timestamp', true),
                'msg'       => $oE->getMessage()
            ]));

            return [
                'status' => 'error',
                'msg'    => $oE->getMessage()
            ];
        }
    }

    public function createNewUser()
    {
        try {
            $oCustomer = Customer::create([
                'email'  => $this->oReceipt->aRequested['email'],
                'source' => $this->oReceipt->aRequested['token']
            ]);
            UserModel::setStripeID($oCustomer->id, $this->userID);

            return $oCustomer;
        } catch (\Exception $oE) {
            return [
                'status' => 'error',
                'msg'    => esc_html__('Your Stripe Customer ID has been deleted, please refresh the browser to re-create it.',
                    'wiloke-listing-tools')
            ];
        }
    }

    public function proceedPayment(Receipt $oReceipt)
    {
        $this->oReceipt = $oReceipt;
        $this->setup();

        if (empty($this->customerID)) {
            $oCustomer = $this->createNewUser();
        } else {
            try {
                $oCustomer = Customer::retrieve($this->customerID);
            } catch (\Exception $oE) {
                UserModel::deleteStripeID($this->userID);
                $oCustomer = $this->createNewUser();
            }
        }

        return $this->charge($oCustomer);
    }
}
