<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use WILCITY_APP\Controllers\WooCommerceController;
use WILCITY_APP\Controllers\WooCommercePrepareCheckout;

class WooCommerceCheckoutController extends WooCommerceController
{
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/billing-fields', [
                [
                    'methods'  => 'POST',
                    'callback' => [$this, 'updateBillingFields']
                ],
                [
                    'methods'  => 'GET',
                    'callback' => [$this, 'getBillingFields']
                ]
            ]);
        });

        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/shipping-fields', [
                [
                    'methods'  => 'POST',
                    'callback' => [$this, 'updateShippingFields']
                ],
                [
                    'methods'  => 'GET',
                    'callback' => [$this, 'getShippingFields']
                ]
            ]);
        });
    }

    public function updateBillingFields(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }

        if ($oRequest->get_param('post_man')) {
            $aData = $oRequest->get_params();
        } else {
            $aData = $oRequest->get_param('data');
        }

        $oWCCheckout = new WooCommercePrepareCheckout($aData);

        return $oWCCheckout->validateBillingForm($aData);
    }

    public function updateShippingFields(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }

        if ($oRequest->get_param('post_man') === true) {
            $aData = $oRequest->get_params();
        } else {
            $aData = $oRequest->get_param('data');
        }

        $oWCCheckout = new WooCommercePrepareCheckout($aData);

        return $oWCCheckout->validateShippingForm($aData);
    }
}
