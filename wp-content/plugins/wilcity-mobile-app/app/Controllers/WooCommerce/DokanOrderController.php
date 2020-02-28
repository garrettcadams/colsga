<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use WILCITY_APP\Controllers\WooCommerceController;

class DokanOrderController extends WooCommerceController
{
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/orders', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'getOrders']
                ]
            ]);
        });
    }
    
    public function deleteProduct(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $productId = $oRequest->get_param('id');
        
        $oToken->getUserID();
        $this->auth();
        
        $msg = 403;
        
        if (get_post_type($productId) != 'product') {
            return [
                'status' => 'error',
                'msg'    => $msg
            ];
        }
        
        if (get_post_field('post_author', $productId) != $this->userID) {
            return [
                'status' => 'error',
                'msg'    => $msg
            ];
        }
        
        wp_delete_post($productId, true);
        
        return [
            'status' => 'error',
            'msg'    => wilcityAppGetLanguageFiles('deletedProduct')
        ];
    }
    
    public function getOrders(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        $this->auth();
        
        $postsPerPage = !empty($oRequest->get_param('postsPerPage')) ? $oRequest->get_param('postsPerPage') : 10;
        $offset       = !empty($oRequest->get_param('page')) ? $oRequest->get_param('page') - 1 : 0;
        $aRawOrders   = dokan_get_seller_orders($this->userID, 'all', null, $postsPerPage, $offset);
        
        if (empty($aRawOrders) || is_wp_error($aRawOrders)) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('noOrder')
            ];
        }
        
        $aOrderIDs  = array_map(function ($oOrder) {
            return $oOrder->order_id;
        }, $aRawOrders);
        $aRawOrders = $this->oWooCommerce->get('orders', ['include' => $aOrderIDs]);
        
        $aOrders = [];
        foreach ($aRawOrders as $oOrder) {
            $aOrders[] = $this->getShortOrderSkeleton($oOrder);
        }
        
        $oOrderCounts = dokan_count_orders($this->userID);
        $total        = $oOrderCounts->total;
        
        return $this->retrieveOrdersFormat($aOrders, $total);
    }
}
