<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use WILCITY_APP\Controllers\WooCommerceController;

class WooCommerceGatewayController extends WooCommerceController
{
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/payment-gateways', [
                'methods'  => 'GET',
                'callback' => [$this, 'getPaymentGateways']
            ]);
        });
    }

    public function getPaymentGateways()
    {
        $this->auth();

        $aRawGateways = $this->oWooCommerce->get('payment_gateways');

        $aGateways = [];
        foreach ($aRawGateways as $order => $aGateway) {
            $aGateway = get_object_vars($aGateway);
            if (!$aGateway['enabled']) {
                continue;
            }

            if (in_array($aGateway['id'], ['wc-booking-gateway', 'ppec_paypal'])) {
                continue;
            }

            unset($aGateway['_links']);

            $aGateways[] = $aGateway;
        }

        usort($aGateways, function ($aItem1, $aItem2) {
            return ($aItem1['order'] < $aItem2['order']);
        });

        return [
            'status'    => 'success',
            'oGateways' => $aGateways
        ];
    }
}
