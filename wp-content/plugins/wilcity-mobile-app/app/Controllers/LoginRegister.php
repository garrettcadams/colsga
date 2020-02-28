<?php

namespace WILCITY_APP\Controllers;

use ReallySimpleJWT\Token;
use WilokeListingTools\Controllers\DashboardController;
use WilokeListingTools\Controllers\RegisterLoginController;
use WilokeListingTools\Controllers\SearchFormController;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use ReallySimpleJWT\TokenBuilder;
use WilokeListingTools\Models\UserModel;

class LoginRegister
{
    use VerifyToken;
    use JsonSkeleton;
    use BuildToken;
    use ParsePost;
    
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/auth', [
                'methods'  => 'POST',
                'callback' => [$this, 'authentication'],
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', 'wc/temp-auth', [
                'methods'  => 'POST',
                'callback' => [$this, 'temporaryAuthentication']
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/signup', [
                'methods'  => 'POST',
                'callback' => [$this, 'signUp'],
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/fb-signin', [
                'methods'  => 'POST',
                'callback' => [$this, 'fbSingIn'],
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/update-password', [
                'methods'  => 'POST',
                'callback' => [$this, 'updatePassword'],
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/is-token-living', [
                'methods'  => 'GET',
                'callback' => [$this, 'isTokenLiving'],
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/get-signup-fields', [
                'methods'  => 'GET',
                'callback' => [$this, 'getSingupFields'],
            ]);
        });
        
        add_action('after_password_reset', [$this, 'afterPasswordReset'], 10);
        add_action('wilcity/user/after_reset_password', [$this, 'afterPasswordReset'], 10);
        add_action('init', [$this, 'loginWithURLToken']);
    }
    
    private function redirectToPayForOrder($orderID)
    {
        $oOrder = wc_get_order($orderID);
        if (is_wp_error($oOrder) || empty($oOrder)) {
            return false;
        }
        
        $aActions = wc_get_account_orders_actions($oOrder);
        wp_safe_redirect(add_query_arg(
            [
                'iswebview' => 'yes'
            ],
            $aActions['pay']['url']
        ));
        exit;
    }
    
    public function loginWithURLToken()
    {
        if (is_admin() || !isset($_GET['orderID']) || empty($_GET['orderID'])) {
            return false;
        }
        
        if (!isset($_GET['token']) || empty($_GET['token'])) {
            return false;
        }
        
        $status = $this->verifyTemporaryToken(trim($_GET['token']));
        if (!$status) {
            return false;
        }
        
        if (is_user_logged_in()) {
            $this->redirectToPayForOrder($_GET['orderID']);
        } else {
            $this->getUserID();
            wp_set_auth_cookie(abs($this->userID), false, true);
            $this->redirectToPayForOrder($_GET['orderID']);
        }
    }
    
    public function firebaseListenUserStatusAnchor()
    {
        if ($userID = Session::getSession(wilokeListingToolsRepository()->get('user:firebaseTriggerCheckUserStatus'),
            true)
        ) {
            $status = is_user_logged_in() ? 'login' : 'logout';
            ?>
            <div id="wilcity-firebase-trigger-update-user-status">
                <firebase-update-user-status email="<?php echo esc_attr(User::getField('user_email', $userID)); ?>"
                                             password="<?php echo esc_attr(User::getField('user_pass')); ?>"
                                             user-id="<?php echo esc_attr($userID); ?>"
                                             status="<?php echo esc_attr($status); ?>"></firebase-update-user-status>
            </div>
            <?php
        }
    }
    
    public function getSingupFields()
    {
        $aThemeOptions = \Wiloke::getThemeOptions(true);
        
        return [
            'status'  => 'success',
            'oFields' => [
                [
                    'type'           => 'text',
                    'key'            => 'username',
                    'label'          => 'username',
                    'required'       => true,
                    'validationType' => 'username'
                ],
                [
                    'type'           => 'text',
                    'key'            => 'email',
                    'label'          => 'email',
                    'required'       => true,
                    'validationType' => 'email'
                ],
                [
                    'type'           => 'password',
                    'key'            => 'password',
                    'label'          => 'password',
                    'required'       => true,
                    'validationType' => 'password'
                ],
                [
                    'type'           => 'checkbox2',
                    'key'            => 'isAgreeToPrivacyPolicy',
                    'label'          => isset($aThemeOptions['mobile_policy_label']) ?
                        $aThemeOptions['mobile_policy_label'] : 'Agree To our Policy Privacy',
                    'required'       => true,
                    'link'           => get_permalink($aThemeOptions['mobile_policy_page']),
                    'validationType' => 'agreeToPolicy'
                ],
                [
                    'type'           => 'checkbox2',
                    'key'            => 'isAgreeToTermsAndConditionals',
                    'label'          => isset($aThemeOptions['mobile_term_label']) ?
                        $aThemeOptions['mobile_term_label'] : 'Agree To our Terms and Conditional',
                    'required'       => true,
                    'link'           => get_permalink($aThemeOptions['mobile_term_page']),
                    'validationType' => 'agreeToTerms'
                ]
            ]
        ];
    }
    
    public function fbSingIn()
    {
        $oToken = $this->verifyPermanentToken();
        if ($oToken) {
            return [
                'status' => 'error',
                'msg'    => 'youAreLoggedInAlready'
            ];
        }
        
        $aData = $this->parsePost();
        $aData = wp_parse_args($aData, [
            'fbUserID'    => '',
            'accessToken' => ''
        ]);
        
        /*
         * FacebookLoginController@loginWithFacebookViaApp
         */
        $aStatus = apply_filters('wilcity/wilcity-mobile-app/filter/fb-login', $aData['fbUserID'],
            $aData['accessToken']);
        
        //        $oLogin = $user_data = get_user_by('email', 'contact.wiloke@gmail.com');
        //        $aStatus = array(
        //            'status' => 'success',
        //            'userID' => $oLogin->ID
        //        );
        
        if ($aStatus['status'] == 'error') {
            unset($aStatus['userID']);
            
            return $aStatus;
        }
        
        $token = $this->buildPermanentLoginToken(new \WP_User($aStatus['userID']));
        
        return [
            'status'    => 'success',
            'msg'       => $aStatus['msg'],
            'token'     => $token,
            'oUserInfo' => [
                'userID'      => $aStatus['userID'],
                'displayName' => GetSettings::getUserMeta($aStatus['userID'], 'display_name'),
                'avatar'      => User::getAvatar($aStatus['userID'])
            ]
        ];
    }
    
    public function signUp()
    {
        $oToken = $this->verifyPermanentToken();
        if ($oToken) {
            return [
                'status' => 'error',
                'msg'    => 'youAreLoggedInAlready'
            ];
        }
        
        $aData = $this->parsePost();
        $aData = wp_parse_args($aData, [
            'email'                         => '',
            'username'                      => '',
            'password'                      => '',
            'isAgreeToPrivacyPolicy'        => false,
            'isAgreeToTermsAndConditionals' => false
        ]);
        
        do_action('wilcity/before/register', $aData);
        
        if (!RegisterLoginController::canRegister()) {
            return [
                'status' => 'error',
                'msg'    => 'disabledLogin'
            ];
        }
        
        if (!$aData['isAgreeToPrivacyPolicy'] || !$aData['isAgreeToTermsAndConditionals']) {
            return [
                'status' => 'error',
                'msg'    => 'needAgreeToTerm'
            ];
        }
        
        if (empty($aData['username']) || empty($aData['email']) || empty($aData['password'])) {
            return [
                'status' => 'error',
                'msg'    => 'needCompleteAllRequiredFields'
            ];
        }
        
        if (!is_email($aData['email'])) {
            return [
                'status' => 'error',
                'msg'    => 'invalidEmail'
            ];
        }
        
        if (email_exists($aData['email'])) {
            return [
                'status' => 'error',
                'msg'    => 'emailExists'
            ];
        }
        
        if (username_exists($aData['username'])) {
            return [
                'status' => 'error',
                'return' => 'usernameExists'
            ];
        }
        
        $aStatus = UserModel::createNewAccount($aData);
        if ($aStatus['status'] == 'error') {
            return [
                'status' => 'error',
                'return' => 'couldNotCreateAccount'
            ];
        }
        
        if ($aStatus['status'] == 'success' && !$aStatus['isNeedConfirm']) {
            $successMsg = 'createdAccountSuccessfully';
        } else {
            $successMsg = $aStatus['msg'];
        }
        
        $token = $this->buildPermanentLoginToken(new \WP_User($aStatus['userID']));
        
        return [
            'status'    => 'success',
            'msg'       => $successMsg,
            'token'     => $token,
            'oUserInfo' => [
                'userID'      => $aStatus['userID'],
                'displayName' => GetSettings::getUserMeta($aStatus['userID'], 'display_name'),
                'avatar'      => User::getAvatar($aStatus['userID'])
            ]
        ];
    }
    
    public function isTokenLiving()
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        return [
            'status' => 'success'
        ];
    }
    
    public function updatePassword()
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $oToken->getUserID();
        
        $aData = $this->parsePost();
        
        if (isset($aData['new_password']) && !empty($aData['new_password'])) {
            wp_set_password($aData['new_password'], $oToken->userID);
            $oUser = new \WP_User($this->userID);
            do_action('wilcity/user/after_reset_password', $oUser);
            
            return [
                'status' => 'success'
            ];
        }
        
        return [
            'status' => 'error'
        ];
    }
    
    public function afterPasswordReset($oUser)
    {
        $this->buildToken($oUser, '+1 seconds');
    }
    
    public function temporaryAuthentication(\WP_REST_Request $oRequest)
    {
        $oValidate = $this->verifyPermanentToken();
        if ($oValidate !== false) {
            $oUser     = new \WP_User($this->userID);
            $tempToken = $this->buildTemporaryLoginToken($oUser);
            
            return [
                'status'      => 'success',
                'checkoutURL' => add_query_arg(
                    [
                        'token'          => $tempToken,
                        'orderID'        => $oRequest->get_param('orderID'),
                        'payment_method' => $oRequest->get_param('payment_method')
                    ],
                    home_url()
                )
            ];
        }
        
        return [
            'status' => 'error',
            'msg'    => 'Invalid Token'
        ];
    }
    
    public function authentication()
    {
        $oValidate = $this->verifyPermanentToken();
        if ($oValidate !== false) {
            return [
                'status' => 'loggedIn'
            ];
        }
        
        $aData  = $this->parsePost();
        $aError = [
            'status' => 'error',
            'msg'    => 'invalidUserNameOrPassword'
        ];
        
        if (empty($aData)) {
            return $aError;
        }
        
        if (!isset($aData['username']) || !isset($aData['password']) || empty($aData['username']) ||
            empty($aData['password'])
        ) {
            return [
                'status' => 'error',
                'msg'    => 'invalidUserNameOrPassword'
            ];
        }
        $oUser = wp_authenticate($aData['username'], $aData['password']);
        
        if (is_wp_error($oUser)) {
            return [
                'status' => 'error',
                'msg'    => 'invalidUserNameOrPassword'
            ];
        }
        
        if (strpos($aData['username'], '@') !== false) {
            $oUser = get_user_by('email', $aData['username']);
        } else {
            $oUser = get_user_by('login', $aData['username']);
        }
        
        if (empty($oUser) || is_wp_error($oUser)) {
            return [
                'status' => 'error',
                'msg'    => 'invalidUserNameOrPassword'
            ];
        }
    
        $token = GetSettings::getUserMeta($oUser->ID, 'app_token');
        if (empty($token) || !$this->verifyTemporaryToken($token)) {
            $token = $this->buildPermanentLoginToken($oUser);
    
            if (is_array($token)) {
                return $token;
            }
        }
        
        return [
            'status'    => 'loggedIn',
            'token'     => $token,
            'oUserInfo' => [
                'userID'      => $oUser->ID,
                'displayName' => GetSettings::getUserMeta($oUser->ID, 'display_name'),
                'userName'    => $oUser->user_login,
                'avatar'      => User::getAvatar($oUser->ID),
                'position'    => User::getPosition($oUser->ID),
                'coverImg'    => User::getCoverImage($oUser->ID)
            ],
            'oUserNav'  => array_values(DashboardController::getNavigation($oUser->ID))
        ];
    }
}
