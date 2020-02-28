<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use WILCITY_APP\Controllers\WooCommerceController;

class DokanGlobalController extends WooCommerceController
{
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/sub-menus', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'getDokanSubMenus']
                ]
            ]);
        });
    }
    
    public function getDokanSubMenus()
    {
        return [
            'status'   => 'success',
            'oResults' => [
                [
                    'name'     => wilcityAppGetLanguageFiles('statistic'),
                    'icon'     => 'la la-line-chart',
                    'endpoint' => 'dokan/statistic'
                ],
                [
                    'name'     => wilcityAppGetLanguageFiles('products'),
                    'icon'     => 'la la-shopping-cart',
                    'endpoint' => 'dokan/products'
                ],
                [
                    'name'     => wilcityAppGetLanguageFiles('orders'),
                    'icon'     => 'la la-check-circle',
                    'endpoint' => 'dokan/orders'
                ],
                [
                    'name'     => wilcityAppGetLanguageFiles('withdraw'),
                    'icon'     => 'la la-money',
                    'endpoint' => 'dokan/withdraw'
                ]
            ]
        ];
    }
}
