<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use WILCITY_APP\Controllers\WooCommerceController;

class DokanStatisticController extends WooCommerceController
{
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/statistic', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'getStatistic']
                ]
            ]);
        });
    }
    
    public function getStatistic(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
    
        $oToken->getUserID();
        
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        $oToken->getUserID();
        $this->auth();
        
        $oStore      = dokan_get_vendor($this->userID);
        $oPostCounts = dokan_count_posts('product', $this->userID);
        
        return [
            'status'   => 'success',
            'oResults' => [
                'oGeneral' => [
                    'heading' => wilcityAppGetLanguageFiles('generalStatistic'),
                    'aItems'  => [
                        [
                            'name'  => wilcityAppGetLanguageFiles('totalSales'),
                            'value' => wc_price($oStore->get_total_sales())
                        ],
                        [
                            'name'  => wilcityAppGetLanguageFiles('earning'),
                            'value' => $oStore->get_earnings()
                        ],
                        [
                            'name'  => wilcityAppGetLanguageFiles('pageviews'),
                            'value' => $oStore->get_product_views()
                        ]
                    ]
                ],
                'oOrder'   => [
                    'heading' => wilcityAppGetLanguageFiles('orderStatistic'),
                    'aItems'  => [
                        [
                            'name'  => wilcityAppGetLanguageFiles('totalLabel'),
                            'value' => abs(dokan_get_seller_orders_number($this->userID))
                        ],
                        [
                            'name'  => wilcityAppGetLanguageFiles('completed'),
                            'value' => abs(dokan_get_seller_orders_number($this->userID, 'wc-completed'))
                        ],
                        [
                            'name'  => wilcityAppGetLanguageFiles('pending'),
                            'value' => abs(dokan_get_seller_orders_number($this->userID, 'wc-pending'))
                        ],
                        [
                            'name'  => wilcityAppGetLanguageFiles('processing'),
                            'value' => abs(dokan_get_seller_orders_number($this->userID, 'wc-processing'))
                        ],
                        [
                            'name'  => wilcityAppGetLanguageFiles('cancelled'),
                            'value' => abs(dokan_get_seller_orders_number($this->userID, 'wc-cancelled'))
                        ],
                        [
                            'name'  => wilcityAppGetLanguageFiles('refunded'),
                            'value' => abs(dokan_get_seller_orders_number($this->userID, 'wc-refunded'))
                        ],
                        [
                            'name'  => wilcityAppGetLanguageFiles('onHold'),
                            'value' => abs(dokan_get_seller_orders_number($this->userID, 'wc-on-hold'))
                        ]
                    ]
                ],
                'oProduct' => [
                    'heading' => 'Products',
                    'aItems'  => [
                        [
                            'name'  => wilcityAppGetLanguageFiles('totalLabel'),
                            'value' => $oPostCounts->total
                        ],
                        [
                            'name'  => wilcityAppGetLanguageFiles('live'),
                            'value' => $oPostCounts->publish
                        ],
                        [
                            'name'  => wilcityAppGetLanguageFiles('offline'),
                            'value' => $oPostCounts->draft
                        ],
                        [
                            'name'  => wilcityAppGetLanguageFiles('pendingReview'),
                            'value' => $oPostCounts->pending
                        ]
                    ]
                ]
            ]
        ];
    }
}
