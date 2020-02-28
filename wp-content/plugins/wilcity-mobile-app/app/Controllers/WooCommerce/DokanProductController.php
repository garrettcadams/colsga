<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use WILCITY_APP\Controllers\WooCommerceController;

class DokanProductController extends WooCommerceController
{
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/products', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'getProducts']
                ]
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/products/(?P<id>\d+)', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'deleteProduct']
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
    
    public function getProducts(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        $this->auth();
        
        $postsPerPage = !empty($oRequest->get_param('postsPerPage')) ? $oRequest->get_param('postsPerPage') : 10;
        $paged        = !empty($oRequest->get_param('page')) ? $oRequest->get_param('page') : 1;
        $order        = !empty($oRequest->get_param('order')) ? $oRequest->get_param('order') : 'DESC';
        $orderBy      = !empty($oRequest->get_param('orderby')) ? $oRequest->get_param('orderby') : 'date';
        
        $aArgs = wp_parse_args(
            [
                'post_type'   => 'product',
                'post_status' => $oRequest->get_param('postStatus') ? $oRequest->get_param('postStatus') : 'any',
                'per_page'    => $postsPerPage,
                'page'        => $paged,
                'order'       => strtolower($order),
                'orderby'     => strtolower($orderBy)
            ],
            [
                'per_page' => 10,
                'page'     => 1,
                'order'    => 'desc',
                'orderby'  => 'date',
                'author'   => $this->userID
            ]
        );
        
        $aProducts = [];
        $query     = new \WP_Query($aArgs);
        
        if (!$query->have_posts()) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('noOrder')
            ];
        }
        
        while ($query->have_posts()) {
            $query->the_post();
            $product                = wc_get_product($query->post->ID);
            $aProduct               = $this->productSkeleton($product, $query->post);
            $aProduct['postStatus'] = $query->post->post_status;
            $aProducts[]            = $aProduct;
        }
        
        wp_reset_postdata();
        
        return $this->retrieveProductsFormat($aProducts, $query->max_num_pages);
    }
}
