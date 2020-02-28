<?php

namespace WILCITY_APP\Controllers\WooCommerce;

use WILCITY_APP\Controllers\WooCommerceController;
use WilokeListingTools\Framework\Helpers\Time;

class DokanWithdrawnController extends WooCommerceController
{
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/withdrawn', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'getWithdrawn']
                ]
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/withdrawn/status', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'getWithdrawStatus']
                ]
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/withdrawn/pending', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'getPendingRequest']
                ]
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/withdrawn/approved', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'getApprovedRequest']
                ]
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/withdrawn/cancelled', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'getCancelledRequest']
                ]
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/withdrawn/request', [
                [
                    'methods'  => \WP_REST_Server::READABLE,
                    'callback' => [$this, 'getWithdrawForm']
                ],
                [
                    'methods'  => \WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'makeAWithDrawn'],
                    'args'     => [
                        'amount' => [
                            'required'    => true,
                            'type'        => 'float',
                            'description' => wilcityAppGetLanguageFiles('amountRequired')
                        ],
                        'method' => [
                            'required'    => true,
                            'type'        => 'string',
                            'description' => wilcityAppGetLanguageFiles('methodRequired')
                        ]
                    ]
                ]
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/dokan/withdrawn/request/(?P<id>\d+)', [
                [
                    'methods'  => \WP_REST_Server::DELETABLE,
                    'callback' => [$this, 'cancelledWithdrawn']
                ]
            ]);
            
        });
    }
    
    protected function verifyDokanPermission()
    {
        if (!current_user_can('dokan_manage_withdraw')) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('denyAccessAction')
            ];
        }
        
        return true;
    }
    
    public function cancelledWithdrawn(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        
        if (!current_user_can('dokan_manage_withdraw')) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('denyAccessAction')
            ];
        }
        
        $oDokanWithdraw = new \Dokan_Withdraw();
        $id             = absint($oRequest->get_param('id'));
        
        $oDokanWithdraw->update_status($id, $this->userID, 2);
        
        return [
            'status' => 'success',
            'msg'    => wilcityAppGetLanguageFiles('requestCancelled')
        ];
    }
    
    public function getWithdrawStatus(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        
        $verifyDokanPermission = $this->verifyDokanPermission();
        if ($verifyDokanPermission !== true) {
            return $verifyDokanPermission;
        }
        
        $aCountRequests = dokan_get_withdraw_count($this->userID);
        
        return [
            'status'   => 'success',
            'aResults' => [
                [
                    'name'     => wilcityAppGetLanguageFiles('pendingRequests'),
                    'endpoint' => 'dokan/withdrawn/pending',
                    'total'    => $aCountRequests['pending']
                ],
                [
                    'name'     => wilcityAppGetLanguageFiles('approvedRequests'),
                    'endpoint' => 'dokan/withdrawn/approved',
                    'total'    => $aCountRequests['completed']
                ],
                [
                    'name'     => wilcityAppGetLanguageFiles('cancelledRequests'),
                    'endpoint' => 'dokan/withdrawn/cancelled',
                    'total'    => $aCountRequests['cancelled']
                ]
            ]
        ];
    }
    
    public function makeAWithDrawn(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        
        $verifyDokanPermission = $this->verifyDokanPermission();
        if ($verifyDokanPermission !== true) {
            return $verifyDokanPermission;
        }
        
        $amount      = $oRequest->get_param('amount');
        $method      = $oRequest->get_param('method');
        $aRawMethods = array_intersect(dokan_get_seller_active_withdraw_methods(), dokan_withdraw_get_active_methods());
        
        $oDokanWithdraw = new \Dokan_Withdraw();
        
        $limit           = $oDokanWithdraw->get_withdraw_limit();
        $balance         = round(dokan_get_seller_balance($this->userID, false), 2);
        $withdraw_amount = (float)$amount;
        
        if ($withdraw_amount > $balance) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('underBalance')
            ];
        } elseif ($withdraw_amount < $limit) {
            return [
                'status' => 'error',
                'msg'    => sprintf(wilcityAppGetLanguageFiles('minimumWithDrawnRequired'),
                    $oDokanWithdraw->get_withdraw_limit())
            ];
        }
        
        if (false === array_search($method, $aRawMethods)) {
            return [
                'status' => 'error',
                'msg'    => sprintf(wilcityAppGetLanguageFiles('noSupportedGateway'), $method)
            ];
        }
        
        $aDataInfo = [
            'user_id' => $this->userID,
            'amount'  => $amount,
            'status'  => 0,
            'method'  => $method,
            'ip'      => dokan_get_client_ip(),
            'notes'   => '',
        ];
        
        $status = $oDokanWithdraw->insert_withdraw($aDataInfo);
        do_action('dokan_after_withdraw_request', new \WP_User($this->userID), $amount, $method);
        
        if ($status) {
            return [
                'status' => 'success',
                'msg'    => wilcityAppGetLanguageFiles('submittedWithdrawn')
            ];
        }
        
        return [
            'status' => 'error',
            'msg'    => wilcityAppGetLanguageFiles('somethingWentWrong')
        ];
    }
    
    public function getWithdrawForm(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        
        $oDokanWithdrawn = new \Dokan_Withdraw();
        
        if ($oDokanWithdrawn->has_pending_request($this->userID)) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('mustApprovedPreviousRequestFirst')
            ];
        } else if (!$oDokanWithdrawn->has_withdraw_balance($this->userID)) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('outOfBalance')
            ];
        }
        
        $balance = $oDokanWithdrawn->get_user_balance($this->userID);
        
        if ($balance < 0) {
            return [
                'status' => 'error',
                'msg'    => sprintf(wilcityAppGetLanguageFiles('alreadyWithdrawn'), wc_price($balance)),
            ];
        }
        
        $aRawMethods = array_intersect(dokan_get_seller_active_withdraw_methods(), dokan_withdraw_get_active_methods());
        if (empty($aRawMethods)) {
            return [
                'status' => 'error',
                'msg'    => wilcityAppGetLanguageFiles('noWithdrawnMethod')
            ];
        }
        
        $aPaymentMethods = [];
        $defaultPayment  = '';
        foreach ($aRawMethods as $order => $method) {
            $selected = false;
            if ($order == 0) {
                $selected       = true;
                $defaultPayment = $method;
            }
            $aPaymentMethods[] = [
                'name'     => $method,
                'id'       => $method,
                'selected' => $selected
            ];
        }
        $withdraw_limit = dokan_get_option('withdraw_limit', 'dokan_withdraw', -1);
        $threshold      = dokan_get_option('withdraw_date_limit', 'dokan_withdraw', -1);
        
        return [
            'status'   => 'success',
            'oResults' => [
                'heading' => wilcityAppGetLanguageFiles('withdrawnRequest'),
                'aFields' => [
                    [
                        'type'           => 'text',
                        'name'           => 'amount',
                        'label'          => wilcityAppGetLanguageFiles('Amount'),
                        'required'       => true,
                        'validationType' => 'amount',
                        'value'          => ''
                    ],
                    [
                        'type'           => 'select',
                        'name'           => 'method',
                        'label'          => wilcityAppGetLanguageFiles('method'),
                        'required'       => true,
                        'validationType' => 'method',
                        'value'          => $defaultPayment,
                        'options'        => $aPaymentMethods
                    ]
                ],
                'oInfo'   => [
                    'balance'          => [
                        'name'  => wilcityAppGetLanguageFiles('currentBalance'),
                        'value' => wc_price($balance)
                    ],
                    'minimumWithDrawn' => [
                        'name'  => wilcityAppGetLanguageFiles('minimumWithdrawn'),
                        'value' => $withdraw_limit
                    ],
                    'threshold'        => [
                        'name'  => wilcityAppGetLanguageFiles('withdrawThreshold'),
                        'value' => $threshold
                    ]
                ]
            ]
        ];
        
    }
    
    private function getRequest($userID, $status, \WP_REST_Request $oRequest)
    {
        $oDokanWithdrawn = new \Dokan_Withdraw();
        $limit           = $oRequest->get_param('count');
        $limit           = empty($limit) ? 10 : abs($limit);
        $offset          = $oRequest->get_param('page');
        $offset          = empty($offset) ? 0 : $offset - 1;
        
        $aRequests = $oDokanWithdrawn->get_withdraw_requests($userID, $status, $limit, $offset);
        if (empty($aRequests)) {
            return [
                'status' => 'error',
                'msg'    => esc_html__('Sorry, no transactions were found!', 'wilcity-mobile-app')
            ];
        }
        
        foreach ($aRequests as $key => $oRequest) {
            $aRequests[$key]->amountHtml = wc_price($oRequest->amount);
            $aRequests[$key]->date       = Time::toDateFormat($oRequest->date);
            
            switch ($oRequest->status) {
                case '0':
                    $aRequests[$key]->status = wilcityAppGetLanguageFiles('pending');
                    break;
                case '1':
                    $aRequests[$key]->status = wilcityAppGetLanguageFiles('approved');
                    break;
                case '2':
                    $aRequests[$key]->status = wilcityAppGetLanguageFiles('cancelled');
                    break;
            }
        }
        
        return [
            'status'   => 'success',
            'oResults' => $aRequests
        ];
    }
    
    public function getPendingRequest(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        
        return $this->getRequest($this->userID, 0, $oRequest);
    }
    
    public function getApprovedRequest(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        
        return $this->getRequest($this->userID, 1, $oRequest);
    }
    
    public function getCancelledRequest(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        
        return $this->getRequest($this->userID, 2, $oRequest);
    }
    
    public function getWithdrawn(\WP_REST_Request $oRequest)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        $balance        = dokan_get_seller_balance($this->userID, true);
        $withdraw_limit = dokan_get_option('withdraw_limit', 'dokan_withdraw', -1);
        $threshold      = dokan_get_option('withdraw_date_limit', 'dokan_withdraw', -1);
        
        $aLatestActivity = $this->getRequest($this->userID, 0, $oRequest);
        if ($aLatestActivity['status'] == 'error') {
            $aLatestActivity = $this->getRequest($this->userID, 1, $oRequest);
            if ($aLatestActivity['status'] == 'error') {
                $aLatestActivity = $this->getRequest($this->userID, 2, $oRequest);
            }
        }
        
        if ($aLatestActivity['status'] == 'error') {
            $aLatestActivity['msg'] = wilcityAppGetLanguageFiles('noRequest');
        }
        
        $aResponse = [
            'oInfo'           => [
                'balance'          => [
                    'name'  => wilcityAppGetLanguageFiles('currentBalance'),
                    'value' => $balance
                ],
                'minimumWithDrawn' => [
                    'name'  => wilcityAppGetLanguageFiles('minimumWithdrawn'),
                    'value' => $withdraw_limit
                ],
                'threshold'        => [
                    'name'  => wilcityAppGetLanguageFiles('withdrawThreshold'),
                    'value' => $threshold
                ]
            ],
            'oLatestRequest'  => [
                'heading'         => wilcityAppGetLanguageFiles('yourActivity'),
                'oLatestRequests' => $aLatestActivity
            ],
            'oMakeARequest'   => [
                'name'     => wilcityAppGetLanguageFiles('request'),
                'endpoint' => 'withdrawn/request'
            ],
            'oWithdrawStatus' => [
                'endpoint' => 'withdrawn/status'
            ]
        ];
        
        return [
            'status'   => 'success',
            'oResults' => $aResponse
        ];
    }
}
