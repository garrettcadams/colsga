<?php

namespace WILCITY_APP\Controllers;

if (!class_exists('\WC_Checkout')) {
    return false;
}

class WooCommercePrepareCheckout extends \WC_Checkout
{
    private $aBillingFields;

    protected function updateCustomerShippingInfo()
    {
        $customer_id = apply_filters('woocommerce_checkout_customer_id', get_current_user_id());
    }

    public function processCustomer($aData)
    {
        $this->process_customer($aData);
    }

    /**
     * @param $billingCountry
     * @param $oWPError \WP_Error
     */
    private function validateBillingCountry($billingCountry, $oWPError)
    {
        $billingCountry    = empty($billingCountry) ? WC()->countries->get_base_country() : $billingCountry;
        $allowed_countries = WC()->countries->get_allowed_countries();
        if (!array_key_exists($billingCountry, $allowed_countries)) {
            $oWPError->add('validation', esc_html__('Invalid country code', 'wilcity-mobile-app'));
        }
    }

    public function validateShippingForm($aPostedData)
    {
        add_filter('woocommerce_checkout_fields', function ($aFields) {
            $this->aBillingFields = $aFields['billing'];
            unset($aFields['billing']);

            return $aFields;
        });

        if (!isset($aPostedData['shipping_country'])) {
            return [
                'status' => 'error',
                'msg'    => esc_html__('The shipping country is required', 'wilcity-mobile-app')
            ];
        }

        $aPostedData['ship_to_different_address'] = true;

        $oWPError    = new \WP_Error();
        $oWCCheckout = new \WC_Checkout();

        $oWCCheckout->validate_posted_data($aPostedData, $oWPError);
        $this->validateBillingCountry($aPostedData['shipping_country'], $oWPError);

        $aStatus = $this->responseValidation($oWPError);
        if ($aStatus['status'] === 'error') {
            return $aStatus;
        }

        $this->processCustomer($aPostedData);

        add_filter('woocommerce_checkout_fields', function ($aFields) {
            $aFields['billing'] = $this->aBillingFields;

            return $aFields;
        });

        return [
            'status' => 'success'
        ];
    }

    /**
     * @param $oWPError \WP_Error
     *
     * @return array
     */
    private function responseValidation($oWPError)
    {
        $aErrors = $oWPError->get_error_messages();
        if (count($aErrors) > 0) {
            $msg = '';
            foreach ($oWPError->get_error_messages() as $errMsg) {
                $msg .= ' <br />'.$errMsg;
            }

            return [
                'status' => 'error',
                'msg'    => $msg
            ];
        }

        return [
            'status' => 'success'
        ];
    }

    public function validateBillingForm($aPostedData)
    {
        $oWPError                                 = new \WP_Error();
        $oWCCheckout                              = new \WC_Checkout();
        $aPostedData['ship_to_different_address'] = false;

        $oWCCheckout->validate_posted_data($aPostedData, $oWPError);
        $this->validateBillingCountry($aPostedData['billing_country'], $oWPError);

        $aStatus = $this->responseValidation($oWPError);

        if ($aStatus['status'] === 'error') {
            return $aStatus;
        }

        $this->processCustomer($aPostedData);

        return [
            'status' => 'success'
        ];
    }
}
