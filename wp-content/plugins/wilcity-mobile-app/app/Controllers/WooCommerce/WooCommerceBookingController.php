<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use WILCITY_APP\Controllers\WooCommerceController;

class WooCommerceBookingController extends WooCommerceController
{
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/bookings', [
                [
                    'methods'  => 'GET',
                    'callback' => [$this, 'getBookings']
                ]
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/bookings/(?P<id>\d+)', [
                [
                    'methods'  => 'GET',
                    'callback' => [$this, 'getBooking']
                ]
            ]);
        });
        
        add_filter('wilcity/mobile/woocommerce-booking', [$this, 'showUpProductOnSingleListing'], 10, 3);
        add_filter('wilcity/wilcity-mobile-app/dashboard-navigator', [$this, 'addWooCommerceBookingToMenu'],
            10, 1);
        add_filter('wilcity/wilcity-mobile-app/filter/get-order', [$this, 'addBookingItemsToOrderResponse']);
    }
    
    protected function getBookingFullSkeleton($post)
    {
        $oBooking  = new \WC_Booking($post->ID);
        $oProduct  = $oBooking->get_product();
        $oResource = $oBooking->get_resource();
        $label     = $oProduct && is_callable([
            $oProduct,
            'get_resource_label'
        ]) && $oProduct->get_resource_label() ? $oProduct->get_resource_label() : __('Type', 'woocommerce-bookings');
        
        $aArgs = $this->getBookingShortSkeleton($oBooking);
        if ($oResource) {
            $aArgs['oSpecification']['oResource'] = [
                'label' => $label,
                'name'  => $oResource->get_name()
            ];
        }
        
        if ($oProduct->has_persons()) {
            if ($oProduct->has_person_types()) {
                $person_types  = $oProduct->get_person_types();
                $person_counts = $oBooking->get_person_counts();
                
                if (!empty($person_types) && is_array($person_types)) {
                    foreach ($person_types as $person_type) {
                        if (empty($person_counts[$person_type->get_id()])) {
                            continue;
                        }
                        $aArgs['oSpecification']['oPerson'][] = [
                            'label'    => $person_type->get_name(),
                            'quantity' => $person_counts[$person_type->get_id()]
                        ];
                    }
                }
            } else {
                $aArgs['oSpecification']['oPerson'][] = [
                    'label'    => esc_html__('Persons', 'wilcity-mobile-app'),
                    'quantity' => array_sum($oBooking->get_person_counts())
                ];
            }
        }
        
        return $aArgs;
    }
    
    protected function getBookingShortSkeleton($post, \WC_Booking $oBooking = null)
    {
        $oBooking = !empty($oBooking) ? $oBooking : new \WC_Booking($post->ID);
        $aArgs    = [
            'id'                    => $post->ID,
            'postTitle'             => get_the_title($oBooking->get_product_id()),
            'orderID'               => $oBooking->get_order_id(),
            'productID'             => $oBooking->get_product_id(),
            'startDate'             => $oBooking->get_start_date(),
            'endDate'               => $oBooking->get_end_date(),
            'status'                => $oBooking->get_status(),
            'googleCalenderEventID' => abs($oBooking->get_google_calendar_event_id()),
            'oSpecification'        => []
        ];
        
        return $aArgs;
    }
    
    public function addBookingItemsToOrderResponse($aOrder)
    {
        $query = new \WP_Query(
            [
                'post_type'      => 'wc_booking',
                'posts_per_page' => 100,
                'post_parent'    => $aOrder['id']
            ]
        );
        
        if (!$query->have_posts()) {
            return $aOrder;
        }
        
        while ($query->have_posts()) {
            $query->the_post();
            $aOrder['aBookings'][] = $this->getBookingFullSkeleton($query->post);
        }
        
        return $aOrder;
    }
    
    public function getBooking(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        $this->auth();
        
        $oQuery = new \WP_Query(
            [
                'post_type'      => 'wc_booking',
                'posts_per_page' => 1,
                'p'              => $oRequest->get_param('id')
            ]
        );
        
        if (!$oQuery->have_posts()) {
            wp_reset_postdata();
            
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('noBooking')
            ];
        }
        
        $aBooking = [];
        while ($oQuery->have_posts()) {
            $oQuery->the_post();
            $aBooking = $this->getBookingFullSkeleton($oQuery->post);
        }
        
        return [
            'status' => 'success',
            'data'   => [
                'oBooking' => $aBooking
            ]
        ];
    }
    
    public function getBookings(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            $userID = $oRequest->get_param('userID');
        } else {
            $oToken->getUserID();
            $userID = $this->userID;
        }
        $this->auth();
        
        $aParams           = $oRequest->get_params();
        $aArgs['page']     = isset($aParams['page']) && !empty($aParams['page']) ? abs($aParams['page']) : 1;
        $aArgs['per_page'] = isset($aParams['count']) && !empty($aParams['count']) ? abs($aParams['count']) : 10;
        $aArgs['customer'] = $userID;
        if (isset($aParams['s']) && !empty($aParams['s'])) {
            $aArgs['search'] = trim($aParams['s']);
        }
        
        $oQuery = new \WP_Query(
            [
                'post_type'      => 'wc_booking',
                'posts_per_page' => $aArgs['per_page'],
                'author'         => $aArgs['customer']
            ]
        );
        
        if (!$oQuery->have_posts()) {
            wp_reset_postdata();
            
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('noBooking')
            ];
        }
        
        $totalBookings = $oQuery->max_num_pages;
        
        $aBookings = [];
        while ($oQuery->have_posts()) {
            $oQuery->the_post();
            $aBookings[] = $this->getBookingShortSkeleton($oQuery->post);
        }
        
        return [
            'status' => 'success',
            'data'   => [
                'aBookings' => $aBookings,
                'total'     => $totalBookings
            ]
        ];
    }
    
    public function addWooCommerceBookingToMenu($aNavigation)
    {
        if (!function_exists('is_woocommerce') || !function_exists('woothemes_queue_update')) {
            return $aNavigation;
        }
        
        $aOrderMenu = array_filter($aNavigation, function ($aItem) {
            return ($aItem['endpoint'] == 'wc/bookings');
        });
        
        if (empty($aOrderMenu)) {
            $aNavigation[] = [
                'name'     => wilcityAppGetLanguageFiles('bookings'),
                'icon'     => 'la la-calendar',
                'endpoint' => 'wc/bookings'
            ];
        }
        
        return $aNavigation;
    }
    
    public function showUpProductOnSingleListing($return, $productID, $aAtts)
    {
        $return = [$this->productSkeleton(wc_get_product($productID), get_post($productID))];
        
        return json_encode($return);
    }
}
