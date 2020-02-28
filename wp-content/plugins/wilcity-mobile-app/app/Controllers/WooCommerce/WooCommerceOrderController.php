<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use Automattic\WooCommerce\Client;
use WILCITY_APP\Controllers\WooCommerceController;
use WilokeListingTools\Framework\Store\Session;

class WooCommerceOrderController extends WooCommerceController
{
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/orders', [
                [
                    'methods'  => 'POST',
                    'callback' => [$this, 'createOrder']
                ],
                [
                    'methods'  => 'GET',
                    'callback' => [$this, 'getOrders']
                ],
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/test-orders', [
                [
                    'methods'  => 'POST',
                    'callback' => [$this, 'testCreateOrder']
                ]
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/orders/(?P<orderId>\d+)', [
                [
                    'methods'  => 'POST',
                    'callback' => [$this, 'updateOrder']
                ],
                [
                    'methods'  => 'GET',
                    'callback' => [$this, 'getOrder']
                ],
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/test-booking/(?P<productID>\d+)', [
//                [
//                    'methods'  => 'POST',
//                    'callback' => [$this, 'updateOrder']
//                ],
                [
                    'methods'  => 'GET',
                    'callback' => [$this, 'getBookingURL']
                ],
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/wc/orders/(?P<orderId>\d+)/status', [
                'methods'  => 'GET',
                'callback' => [$this, 'getOrderStatus']
            ]);
        });
        add_filter('init', [$this, 'autoAddChosenToGateway']);
        add_filter('wilcity/wilcity-mobile-app/dashboard-navigator', [$this, 'addWooCommerceOrderToMenu'], 10, 1);
        add_action('woocommerce_thankyou', [$this, 'clearWebviewSession']);
    }
    
    public function clearWebviewSession()
    {
        Session::destroySession('isWebview');
    }
    
    public function getBookingURL(\WP_REST_Request $oRequest)
    {
        $productID = $oRequest->get_param('productID');
        
        return [
            'status'     => 'success',
            'productURL' => get_permalink($productID)
        ];
    }
    
    public function testCreateOrder()
    {
        $this->auth();
        
        $data = [
            'customer_id'          => 1,
            'payment_method'       => 'paypal',
            'payment_method_title' => 'PayPal',
            'set_paid'             => false,
            'billing'              => [
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'address_1'  => '969 Market',
                'address_2'  => '',
                'city'       => 'San Francisco',
                'state'      => 'CA',
                'postcode'   => '94103',
                'country'    => 'US',
                'email'      => 'john.doe@example.com',
                'phone'      => '(555) 555-5555'
            ],
            'shipping'             => [
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'address_1'  => '969 Market',
                'address_2'  => '',
                'city'       => 'San Francisco',
                'state'      => 'CA',
                'postcode'   => '94103',
                'country'    => 'US'
            ],
            'line_items'           => [
                [
                    'product_id' => 6863,
                    'quantity'   => 1
                ]
            ]
        ];
        try {
            $oOrder = $this->oWooCommerce->post('orders', $data);
        } catch (\Exception $oE) {
            echo $oE->getMessage();
            die;
        }
        
        echo $oOrder->id;
        die;
    }
    
    public function getOrder(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        $oToken->getUserID();
        
        $orderID = $oRequest->get_param('orderId');
        $oOrder  = wc_get_order($orderID);
        
        if (empty($oOrder) || is_wp_error($oOrder)) {
            return [
                'status' => 'error',
                'msg'    => 'orderDoesNotExists'
            ];
        }
        
        if ($oOrder->get_customer_id() != $this->userID && $this->dokanIsOrderByMyCustomer($this->userID, $oOrder)) {
            return [
                'status' => 'error',
                'msg'    => 'denyAccessOrder'
            ];
        }
        
        $aBillingInfo = [];
        if (!empty($oOrder->get_formatted_billing_full_name())) {
            $aBillingInfo[] = $oOrder->get_formatted_billing_full_name();
        }
        
        if (!empty($oOrder->get_billing_address_1())) {
            $aBillingInfo[] = $oOrder->get_billing_address_1();
        }
        
        if (!empty($oOrder->get_billing_address_2())) {
            $aBillingInfo[] = $oOrder->get_billing_address_2();
        }
        
        if (!empty($oOrder->get_billing_state())) {
            $aBillingInfo[] = $oOrder->get_billing_state();
        }
        
        if (!empty($oOrder->get_billing_email())) {
            $aBillingInfo[] = $oOrder->get_billing_email();
        }
        
        if (!empty($oOrder->get_billing_phone())) {
            $aBillingInfo[] = $oOrder->get_billing_phone();
        }
        
        $aShippingInfo = [];
        if (!empty($oOrder->get_formatted_shipping_full_name())) {
            $aShippingInfo[] = $oOrder->get_formatted_shipping_full_name();
        }
        
        if (!empty($oOrder->get_shipping_address_1())) {
            $aShippingInfo[] = $oOrder->get_shipping_address_1();
        }
        
        if (!empty($oOrder->get_shipping_address_2())) {
            $aShippingInfo[] = $oOrder->get_shipping_address_2();
        }
        
        $aActions    = [];
        $aRawActions = array_keys(wc_get_account_orders_actions($oOrder->get_id()));
        
        foreach ($aRawActions as $action) {
            switch ($action) {
                case 'cancel':
                    $aActions[$action] = [
                        'endpoint' => 'orders/'.$oOrder->get_id(),
                        'method'   => 'post',
                        'params'   => [
                            'status' => 'cancelled'
                        ]
                    ];
                    break;
                case 'pay':
                    $aActions[$action] = [
                        'endpoint' => 'orders/'.$oOrder->get_id(),
                        'method'   => 'post'
                    ];
                    break;
            }
        }
        
        /**
         * hooked WooCommerceBookingController@addBookingItemsToOrderResponse
         */
        return [
            'status' => 'success',
            'oOrder' => apply_filters('wilcity/wilcity-mobile-app/filter/get-order', [
                'id'            => $oOrder->get_id(),
                'createdAt'     => wc_format_datetime($oOrder->get_date_created()),
                'subTotal'      => $oOrder->get_subtotal_to_display(),
                'shipping'      => $oOrder->get_shipping_to_display(),
                'paymentMethod' => $oOrder->get_payment_method_title(),
                'total'         => $oOrder->get_formatted_order_total(),
                'aBillingInfo'  => $aBillingInfo,
                'aShippingInfo' => !empty($aShippingInfo) ? $aShippingInfo : [],
                'oActions'      => $aActions
            ])
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
        
        $aParams           = $oRequest->get_params();
        $aArgs['page']     = isset($aParams['page']) && !empty($aParams['page']) ? abs($aParams['page']) : 1;
        $aArgs['per_page'] = isset($aParams['count']) && !empty($aParams['count']) ? abs($aParams['count']) : 10;
        if (isset($aParams['status'])) {
            $aArgs['status'] = str_replace('wc-', '', $aParams['status']);
        }
        
        $aArgs['customer'] = $this->userID;
        
        if (isset($aParams['s']) && !empty($aParams['s'])) {
            $aArgs['search'] = trim($aParams['s']);
        }
        
        $aRawOrders = $this->oWooCommerce->get('orders', $aArgs);
        $totalOrder = wc_get_customer_order_count($this->userID);
        
        if (empty($aRawOrders)) {
            return [
                'status' => 'error',
                'msg'    => 'noOrder'
            ];
        }
        
        $aOrders = [];
        foreach ($aRawOrders as $oOrder) {
            $aOrders[] = $this->getShortOrderSkeleton($oOrder);
        }
        
        return $this->retrieveOrdersFormat($aOrders, $totalOrder);
    }
    
    public function addWooCommerceOrderToMenu($aNavigation)
    {
        $aOrderMenu = array_filter($aNavigation, function ($aItem) {
            return ($aItem['endpoint'] == 'wc/orders');
        });
    
        if (empty($aOrderMenu)) {
            $aNavigation[] = [
                'name'     => esc_html__('Shop Order', 'wilcity-mobile-app'),
                'icon'     => 'la la-check-circle',
                'endpoint' => 'wc/orders'
            ];
        }
        
        return $aNavigation;
    }
    
    public function autoAddChosenToGateway()
    {
        if (isset($_REQUEST['pay_for_order']) && !empty($_REQUEST['pay_for_order'])) {
            $orderID = wc_get_order_id_by_order_key($_REQUEST['key']);
            if (!empty($orderID)) {
                $paymentMethod      = get_post_meta($orderID, '_payment_method', true);
                $aAvailableGateways = \WC()->payment_gateways()->get_available_payment_gateways();
                if (isset($aAvailableGateways[$paymentMethod])) {
                    $aAvailableGateways[$paymentMethod]->set_current();
                }
            }
        }
    }
    
    protected function parseOrderData(\WP_REST_Request $oRequest, $isUpdate = false)
    {
        $aRawData = $oRequest->get_params();
        $aData    = [];
        
        if (!$isUpdate) {
            $aRequires   = [
                'payment_method',
                'payment_method_title',
                'line_items'
            ];
            $requiredMsg = wilcityAppGetLanguageFiles('requiredMsgWithPlaceHolder');
            foreach ($aRequires as $param) {
                if (!isset($aRawData[$param]) || empty($aRawData[$param])) {
                    return [
                        'status' => 'error',
                        'msg'    => str_replace('%s', $param, $requiredMsg)
                    ];
                }
                $aData[$param] = $aRawData[$param];
            }
            $aData['set_paid'] = false;
        }
        
        $aCheckoutFields = array_map(function ($aField) {
            return str_replace('billing_', '', $aField['name']);
        }, $this->getCheckoutFormField());
        
        foreach ($aCheckoutFields as $field) {
            $aData['billing'][$field] = $this->getBillingField('billing_'.$field);
        }
        
        if ($this->getBillingField('shipping_to_different_address')) {
            foreach ($aCheckoutFields as $field) {
                $aData['shipping'][$field] = $this->getBillingField('shipping_'.$field);
            }
        }
        
        return [
            'status' => 'success',
            'aData'  => $aData
        ];
    }
    
    public function createOrder(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        $this->auth();
        $aParsedData = $this->parseOrderData($oRequest);
        
        if ($aParsedData['status'] == 'error') {
            return $aParsedData;
        }
        
        $aData                = $aParsedData['aData'];
        $aData['customer_id'] = $oToken->userID;
        
        return [
            'status' => 'success',
            'oOrder' => $this->oWooCommerce->post('orders', $aData)
        ];
    }
    
    public function updateOrder(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        $this->auth();
        $orderID = $oRequest->get_param('orderId');
        $oOrder  = wc_get_order($orderID);
        
        if ($oOrder->get_customer_id() != $this->userID) {
            return [
                'status' => 'error',
                'msg'    => 'denyAccessOrder'
            ];
        }
        
        $newOrderStatus = $oRequest->get_param('status');
        if ($newOrderStatus != 'cancelled') {
            $aParsedData = $this->parseOrderData($oRequest, true);
            if ($aParsedData['status'] == 'error') {
                return $aParsedData;
            }
            $aNewData = $aParsedData['aData'];
            $msg      = 'orderAdded';
        } else {
            $aNewData['status'] = $newOrderStatus;
            $msg                = 'orderCancelled';
        }
        
        try {
            $status = $this->oWooCommerce->put('orders/'.$orderID, $aNewData);
            if ($status) {
                return [
                    'status' => 'success',
                    'msg'    => $msg,
                    'oOrder' => $this->oWooCommerce->get('orders/'.$orderID)
                ];
            } else {
                return [
                    'status' => 'error',
                    'oOrder' => 'errorWentOrderCancelled'
                ];
            }
        } catch (\Exception $oException) {
            return [
                'status' => 'error',
                'oOrder' => $oException->getMessage()
            ];
        }
        
    }
    
    /**
     * @param \WP_REST_Request $oRequest
     *
     * @return array
     */
    public function getOrderStatus(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        $oToken->getUserID();
        
        $orderId = $oRequest->get_param('orderId');
        $oOrder  = wc_get_order($orderId);
        
        if (empty($orderId)) {
            return [
                'status' => 'error',
                'msg'    => 'orderDoesNotExists'
            ];
        }
        
        if ($oOrder->get_customer_id() != $this->userID) {
            return [
                'status' => 'error',
                'msg'    => 403
            ];
        }
        
        $aOrderData = $oOrder->get_data();
        
        $msg = '<p>'.sprintf(esc_html__('Hi %s,', 'wilcity-mobile-app'), esc_html(
                $oOrder->get_billing_first_name())).'</p>';
        switch ($aOrderData['status']) {
            case 'processing':
                $msg .= '<p>'.sprintf(wilcityAppGetLanguageFiles('orderBeingProcessed'), esc_html($oOrder->get_order_number())).'</p>';
                break;
            case 'completed':
                $msg .= '<p>'.sprintf(wilcityAppGetLanguageFiles('orderCompleted'), esc_html($oOrder->get_order_number
                    ())).'</p>';
                break;
            case 'on-hold':
                $msg .= '<p>'.wilcityAppGetLanguageFiles('orderOnHold').'</p>';
                break;
            case 'pending':
                $msg .= '<p>'.sprintf(wilcityAppGetLanguageFiles('orderPending'), $orderId).'</p>';
                break;
            case 'cancelled':
                $msg .= '<p>'.wilcityAppGetLanguageFiles('orderCancelled').'</p>';
                break;
            case 'failed':
                $msg .= '<p>'.sprintf(wilcityAppGetLanguageFiles('orderFailed'), esc_html($oOrder->get_order_number()),
                        esc_html($oOrder->get_formatted_billing_full_name())).'</p>';
                break;
        }
        
        return [
            'status' => 'success',
            'msg'    => $msg,
            'order'  => [
                'status' => $aOrderData['status'],
                'id'     => $orderId
            ]
        ];
    }
}
