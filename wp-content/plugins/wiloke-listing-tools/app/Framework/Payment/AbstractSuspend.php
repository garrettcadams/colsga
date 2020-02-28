<?php

namespace WilokeListingTools\Framework\Payment;

use WilokeListingTools\Framework\Payment\DirectBankTransfer\DirectBankTransferSuspend;
use WilokeListingTools\Framework\Payment\PayPal\PayPalSuspendPlan;
use WilokeListingTools\Framework\Payment\Stripe\StripeSuspendPlan;
use WilokeListingTools\Framework\Payment\WooCommerce\WooCommerceSuspend;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

abstract class AbstractSuspend
{
    protected $newPlanID;
    protected $currentPlanID;
    protected $listingType;
    protected $currentPaymentID;

    protected function setSessions()
    {
        Session::setSession(wilokeListingToolsRepository()->get('payment:storePlanID'), $this->newPlanID);
        Session::setSession(wilokeListingToolsRepository()->get('payment:listingType'), $this->listingType);
        Session::setSession(wilokeListingToolsRepository()->get('payment:onChangedPlan'), 'yes');
        Session::setSession(wilokeListingToolsRepository()->get('payment:oldPlanID'), $this->currentPlanID);
        Session::setSession(wilokeListingToolsRepository()->get('payment:oldPaymentID'), $this->currentPaymentID);
    }

    protected $paymentID;

    protected function setPaymentID($paymentID)
    {
        $this->paymentID = $paymentID;
    }

    protected function setChangePlanInfo($paymentID, $gateway)
    {
        PaymentMetaModel::set($paymentID, 'change_plan_info', [
            'userID'       => User::getCurrentUserID(),
            'paymentID'    => $paymentID,
            'oldPaymentID' => $this->currentPaymentID,
            'oldPlanID'    => $this->currentPlanID,
            'planID'       => $this->newPlanID,
            'listingType'  => $this->listingType,
            'gateway'      => $gateway,
            'billingType'  => wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('recurring')
        ]);
    }

    protected function suspend()
    {
        $gateway = PaymentModel::getField('gateway', $this->paymentID);

        switch ($gateway) {
            case 'paypal':
                $instSuspend = new PayPalSuspendPlan();
                $instSuspend->setPaymentID($this->paymentID);
                break;
            case 'stripe':
                $instSuspend = new StripeSuspendPlan();
                $instSuspend->setPaymentID($this->paymentID);
                break;
            case 'banktransfer':
            case 'free':
                $instSuspend = new DirectBankTransferSuspend();
                $instSuspend->setPaymentID($this->paymentID);
                break;
            case 'woocommerce':
                $instSuspend = new WooCommerceSuspend();
                $orderID     = PaymentModel::getField('wooOrderID', $this->paymentID);
                $instSuspend->setCurrentOrderID($orderID);
                break;
        }

        if (isset($instSuspend)) {
            return $instSuspend->suspend();
        }

        return false;
    }
}
