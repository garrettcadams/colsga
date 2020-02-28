<?php

namespace WilokeListingTools\Controllers;

use Facebook\Facebook;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\UserModel;
use WilokeListingTools\Register\WilokeSubmissionConfiguration;

class RegisterLoginController extends Controller
{
    protected static $canRegister = null;
    private static $fbMetaKey = 'facebook_user_id';
    use InsertImg;

    public function __construct()
    {
        add_action('wilcity/header/after-menu', [$this, 'printRegisterLoginButton'], 20);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wilcity/footer/vue-popup-wrapper', [$this, 'printFooterCode']);
        add_filter('logout_redirect', [$this, 'modifyLogoutRedirectUrl'], 10);

        //Ajax
        add_action('wp_ajax_nopriv_wilcity_login', [$this, 'handleLogin']);
        add_action('wp_ajax_nopriv_wilcity_register', [$this, 'handleRegister']);
        add_action('wp_ajax_nopriv_wilcity_reset_password', [$this, 'resetPassword']);
        add_action('wp_ajax_wilcity_agree_become_to_author', [$this, 'handleBecomeAnAuthorSubmission']);

        add_action('show_admin_bar', [$this, 'hideAdminBar']);
//		add_action('init', array($this, 'handleRegister'));
        add_action('wilcity/print-need-to-verify-account-message', [$this, 'printNeedToVerifyAccount']);
        add_action('wp_ajax_nopriv_wilcity_send_retrieve_password', [$this, 'sendRetrievePassword']);
        add_action('wp_ajax_nopriv_wilcity_update_password', [$this, 'updatePassword']);
        add_filter('lostpassword_redirect', [$this, 'modifyLostPasswordRedirect'], 10, 2);
        add_action('wiloke/claim/approved', [$this, 'addClaimerToWilokeSubmissionGroup']);
        add_action('wiloke/claim/approved', [$this, 'autoSwitchConfirmationToApproved']);
        add_filter('wilcity-login-with-social/after_login_redirect_to',
            [$this, 'afterLoggedInWithSocialWillRedirectTo'], 10, 2);

        add_action('wp_ajax_wilcity_delete_account', [$this, 'deleteAccount']);

        // Custom Login
        add_filter('register_url', [$this, 'modifyRegisterURL'], 999);
        add_filter('lostpassword_url', [$this, 'modifyLostPasswordURL'], 9999);
        add_filter('wp_loaded', [$this, 'handleLoginRegisterOnCustomLoginPage']);
    }

    private function autoLogin($userID)
    {
        wp_set_current_user($userID);
        wp_set_auth_cookie($userID, false, is_ssl());
    }

    private function handleLoginPHP($aData)
    {
        $aValidation = apply_filters('wilcity/filter/wiloke-listing-tools/validate-before-login',
            ['status' => 'success'], $aData);

        if ($aValidation['status'] != 'success') {
            return ['status' => 'error', 'msg' => $aValidation['msg']];
        }

        $oErrors = wp_signon([
            'user_login'    => $aData['user_login'],
            'user_password' => $aData['user_password'],
            'remember'      => isset($aData['rememberme'])
        ], is_ssl());

        if (is_wp_error($oErrors)) {
            return ['status' => 'error', 'msg' => $oErrors->get_error_message()];
        }

        return ['status' => 'success'];
    }

    private function handleRegisterPHP($aData)
    {
        $this->middleware(['canRegister'], []);

        if (\WilokeThemeOptions::isEnable('toggle_privacy_policy', false)) {
            if (!isset($aData['isAgreeToPrivacyPolicy']) || $aData['isAgreeToPrivacyPolicy'] != 'yes') {
                return ['status' => 'error',
                        'msg'    => esc_html__('In order to register an account, You need to agree to our privacy policy',
                            'wiloke-listing-tools')
                ];
            }
        }

        if (\WilokeThemeOptions::isEnable('toggle_terms_and_conditionals', false)) {
            if (!isset($aData['isAgreeToTermsAndConditionals']) || $_POST['isAgreeToTermsAndConditionals'] != 'yes') {
                return ['status' => 'error',
                        'msg'    => esc_html__('In order to register an account, You need to agree to our term conditionals',
                            'wiloke-listing-tools')
                ];
            }
        }

        if (!is_email($aData['user_email'])) {
            return ['status' => 'error', 'msg' => esc_html__('Invalid Email', 'wiloke-listing-tools')];
        }

        if (!apply_filters('wilcity/filter/wiloke-listing-tools/validate-password', !empty($aData['user_password']),
            $aData['user_password'])
        ) {
            return ['status' => 'error', 'msg' => esc_html__('The password is required', 'wiloke-listing-tools')];
        }

        $aData['username'] = $aData['user_login'];
        $aData['password'] = $aData['user_password'];
        $aData['email']    = $aData['user_email'];

        $aValidation = apply_filters('wilcity/filter/wiloke-listing-tools/validate-before-insert-account',
            ['status' => 'success'], $aData);

        if ($aValidation['status'] !== 'success') {
            return ['status' => 'error',
                    'msg'    => isset($aValidation['msg']) ? $aValidation['msg'] : esc_html__('Something went wrong',
                        'wiloke-listing-tools')
            ];
        }

        return UserModel::createNewAccount($aData, false);
    }

    public function handleLoginRegisterOnCustomLoginPage()
    {
        if (is_page_template('templates/custom-login.php')) {
            return false;
        }

        if (!isset($_POST['action']) || empty($_POST['action'])) {
            return false;
        }

        switch ($_POST['action']) {
            case 'register':
                $aData = $_POST;
                if (isset($_POST['user_login']) && is_string($_POST['user_login'])) {
                    $aData['user_login'] = $_POST['user_login'];
                }

                if (isset($_POST['user_email']) && is_string($_POST['user_email'])) {
                    $aData['user_email'] = wp_unslash($_POST['user_email']);
                }

                if (isset($_POST['user_password']) && is_string($_POST['user_password'])) {
                    $aData['user_password'] = $_POST['user_password'];
                }

                $aResponse = $this->handleRegisterPHP($aData);

                if ($aResponse['status'] == 'success') {
                    $this->autoLogin($aResponse['userID']);
                    do_action('wilcity/after/created-account', $aResponse['userID'], $aData['user_login'],
                        \WilokeThemeOptions::isEnable('toggle_confirmation'));

                    wp_safe_redirect(esc_url($_POST['redirect_to']));
                    exit();
                } else if ($aResponse['status'] == 'error') {
                    Session::setSession('register_error', $aResponse['msg']);
                }
                break;
            case 'rp':
                $aResponse = $this->sendRetrievePassword();
                Session::setSession('rp_status', maybe_serialize($aResponse));
                break;
            case 'login':
                $aData = $_POST;
                if (isset($_POST['user_login']) && is_string($_POST['user_login'])) {
                    $aData['user_login'] = $_POST['user_login'];
                }

                if (isset($_POST['user_password']) && is_string($_POST['user_password'])) {
                    $aData['user_password'] = $_POST['user_password'];
                }

                if (isset($_POST['rememberme']) && is_string($_POST['rememberme'])) {
                    $aData['rememberme'] = $_POST['rememberme'];
                }

                if (isset($_POST['redirect_to']) && is_string($_POST['redirect_to'])) {
                    $aData['redirect_to'] = $_POST['redirect_to'];
                }

                $aResponse = $this->handleLoginPHP($aData);

                if ($aResponse['status'] == 'error') {
                    Session::setSession('login_error', $aResponse['msg']);
                } else {
                    wp_safe_redirect($aData['redirect_to']);
                    exit();
                }

                break;
        }
    }

    public function modifyRegisterURL($registerPageURL)
    {
        if (GetSettings::getCustomLoginPage()) {
            return add_query_arg(
                [
                    'action' => 'register'
                ],
                GetSettings::getCustomLoginPage()
            );
        }

        return $registerPageURL;
    }

    private function updateUserData($userID, $aData, $isFocus = false)
    {
        $aUserData = GetSettings::getUserData($userID);

        foreach ($aData as $key => $val) {
            if (!$isFocus) {
                if (empty($aUserData[$key])) {
                    SetSettings::setUserMeta($userID, $key, $val);
                }
            } else {
                SetSettings::setUserMeta($userID, $key, $val);
            }
        }
    }

    public function addClassToLoginButton()
    {
        if (is_page_template('templates/custom-login-page.php')) {
            return 'wil-btn mb-20 wil-btn--gradient wil-btn--md wil-btn--round wil-btn--block';
        }

        return '';
    }

    public function deleteAccount()
    {
        $aThemeOptions = \Wiloke::getThemeOptions();
        if ((!isset($aThemeOptions['toggle_allow_customer_delete_account']) || $aThemeOptions['toggle_allow_customer_delete_account'] == 'disable') || !User::isUserLoggedIn()) {
            wp_send_json_error([
                'msg' => esc_html__('You do not have permission to access this page', 'wiloke-listing-tools')
            ]);
        }

        $oUser = new \WP_User(User::getCurrentUserID());

        if (!isset($_POST['current_password']) || empty($_POST['current_password']) || !wp_check_password($_POST['current_password'],
                $oUser->data->user_pass, $oUser->ID)
        ) {
            wp_send_json_error([
                'msg' => esc_html__('Invalid confirm password.', 'wiloke-listing-tools')
            ]);
        }

        $aPosts = get_posts([
            'numberposts' => -1,
            'post_type'   => 'any',
            'author'      => $oUser->ID
        ]);

        if (!empty($aPosts)) {
            foreach ($aPosts as $oPost) {
                wp_delete_post($oPost->ID, true);
            };
        }
        wp_delete_user($oUser->ID);

        wp_send_json_success([
            'msg' => esc_html__('Your account was successfully deleted. We are sorry to see you go!',
                'wiloke-listing-tools')
        ]);
    }

    public function afterLoggedInWithSocialWillRedirectTo($redirectTo, $isFirstTimeLoggedIn)
    {
        $aThemeOptions = \Wiloke::getThemeOptions(true);
        if ($isFirstTimeLoggedIn) {
            $redirectTo = isset($aThemeOptions['created_account_redirect_to']) && !empty($aThemeOptions['created_account_redirect_to']) && $aThemeOptions['created_account_redirect_to'] != 'self_page' ? urlencode(get_permalink($aThemeOptions['created_account_redirect_to'])) : 'self';
        } else {
            $redirectTo = isset($aThemeOptions['login_redirect_type']) && !empty($aThemeOptions['login_redirect_type']) && $aThemeOptions['login_redirect_type'] !== 'self_page' ? urlencode(get_permalink($aThemeOptions['login_redirect_to'])) : 'self';
        }

        return $redirectTo;
    }

    private function getUserBy($aUser)
    {

        // if the user is logged in, pass curent user
        if (is_user_logged_in()) {
            return wp_get_current_user();
        }

        $user_data = get_user_by('email', $aUser['email']);

        if (!$user_data) {
            $users = get_users(
                [
                    'meta_key'    => self::$fbMetaKey,
                    'meta_value'  => $aUser['fb_user_id'],
                    'number'      => 1,
                    'count_total' => false
                ]
            );
            if (is_array($users)) {
                $user_data = reset($users);
            }
        }

        return $user_data;
    }

    public static function canRegister()
    {
        if (self::$canRegister !== null) {
            return self::$canRegister;
        }

        self::$canRegister = GetSettings::userCanRegister();

        return self::$canRegister;
    }

    public function addClaimerToWilokeSubmissionGroup($claimerID)
    {
        $user_meta  = get_userdata($claimerID);
        $aUserRoles = $user_meta->roles;
        if (in_array('subscriber', $aUserRoles)) {
            UserModel::addSubmissionRole($claimerID);
        }
    }

    public function autoSwitchConfirmationToApproved($claimerID)
    {
        SetSettings::setUserMeta($claimerID, 'confirmed', true);
    }

    public function modifyLostPasswordURL($url)
    {
        global $wiloke;
        if (\WilokeThemeOptions::isEnable('toggle_custom_login_page')) {
            return add_query_arg(
                [
                    'action' => 'rp'
                ],
                get_permalink($wiloke->aThemeOptions['custom_login_page'])
            );
        }

        return $url;
    }

    public function modifyLostPasswordRedirect($url)
    {
        global $wiloke;
        if (isset($wiloke->aThemeOptions['reset_password_page']) && !empty($wiloke->aThemeOptions['reset_password_page'])) {
            if (get_post_status($wiloke->aThemeOptions['reset_password_page']) == 'publish') {
                return get_permalink($wiloke->aThemeOptions['reset_password_page']);
            }
        }

        return $url;
    }

    public function updatePassword()
    {
        if (!isset($_POST['newPassword'])) {
            wp_send_json_error(esc_html__('Please enter your new password', 'wiloke-listing-tools'));
        }
        $aCheckResetPWStatus = check_password_reset_key($_POST['rpKey'], $_POST['user_login']);

        if (is_wp_error($aCheckResetPWStatus) || !$aCheckResetPWStatus) {
            wp_send_json_error(esc_html__('The reset key has been expired', 'wiloke-listing-tools'));
        }

        $oUser = get_user_by('login', sanitize_text_field($_POST['user_login']));

        if (is_wp_error($oUser) || empty($oUser)) {
            wp_send_json_error(esc_html__('This username does not exist.', 'wiloke-listing-tools'));
        }

        reset_password($oUser, $_POST['newPassword']);

        SetSettings::setUserMeta($oUser->ID, 'confirmed', true);
        wp_send_json_success(esc_html__('Congratulations! The new password has been updated successfully. Please click on Login button to Log into the website',
            'wiloke-listing-tools'));
    }

    public function sendRetrievePassword()
    {
        $errors = new \WP_Error();
        $isAjax = wp_doing_ajax();

        if (empty($_POST['user_login']) || !is_string($_POST['user_login'])) {
            $errors->add('empty_username',
                __('<strong>ERROR</strong>: Enter a username or email address.', 'wiloke-listing-tools'));
        } elseif (strpos($_POST['user_login'], '@')) {
            $user_data = get_user_by('email', trim(wp_unslash($_POST['user_login'])));
            if (empty($user_data)) {
                $errors->add('invalid_email',
                    __('<strong>ERROR</strong>: There is no user registered with that email address.',
                        'wiloke-listing-tools'));
            }
        } else {
            $login     = trim($_POST['user_login']);
            $user_data = get_user_by('login', $login);
        }

        do_action('lostpassword_post', $errors);

        if ($errors->get_error_code()) {
            if ($isAjax) {
                wp_send_json_error($errors->get_error_message());
            } else {
                return [
                    'status' => 'error',
                    'msg'    => $errors->get_error_message()
                ];
            }
        }

        if (!$user_data) {
            $errors->add('invalidcombo',
                __('<strong>ERROR</strong>: Invalid username or email.', 'wiloke-listing-tools'));
            if ($isAjax) {
                wp_send_json_error($errors->get_error_message());
            } else {
                return [
                    'status' => 'error',
                    'msg'    => $errors->get_error_message()
                ];
            }
        }

        // Redefining user_login ensures we return the right case in the email.
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        $key        = get_password_reset_key($user_data);

        if (is_wp_error($key)) {
            $msg = esc_html__('Oops! We could not generate reset key. Please contact the administrator to report this issue',
                'wiloke-listing-tools');
            if ($isAjax) {
                wp_send_json_error($msg);
            } else {
                return [
                    'status' => 'error',
                    'msg'    => $msg
                ];
            }
        }

        if (is_multisite()) {
            $site_name = get_network()->site_name;
        } else {
            $site_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        }

        $aThemeOptions = \Wiloke::getThemeOptions(true);
        if (!isset($aThemeOptions['reset_password_page']) || empty($aThemeOptions['reset_password_page']) || get_post_status($aThemeOptions['reset_password_page']) != 'publish') {
            $resetPasswordURL = network_site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($user_login),
                'login');
        } else {
            $resetPasswordURL = get_permalink($aThemeOptions['reset_password_page']);
            $resetPasswordURL = add_query_arg(
                [
                    'action' => 'rp',
                    'key'    => $key,
                    'login'  => rawurlencode($user_login)
                ],
                $resetPasswordURL
            );
        }

        $message = __('Someone has requested a password reset for the following account:',
                'wiloke-listing-tools')."\r\n\r\n";
        /* translators: %s: site name */
        $message .= sprintf(__('Site Name: %s', 'wiloke-listing-tools'), $site_name)."\r\n\r\n";
        /* translators: %s: user login */
        $message .= sprintf(__('Username: %s'), $user_login)."\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.',
                'wiloke-listing-tools')."\r\n\r\n";
        $message .= __('To reset your password, visit the following address:', 'wiloke-listing-tools')."\r\n\r\n";
        $message .= '<'.$resetPasswordURL.">\r\n";

        /* translators: Password reset email subject. %s: Site name */
        $title   = sprintf(__('[%s] Password Reset', 'wiloke-listing-tools'), $site_name);
        $title   = apply_filters('retrieve_password_title', $title, $user_login, $user_data);
        $message = apply_filters('retrieve_password_message', $message, $key, $user_login, $user_data);

        if ($message && !wp_mail($user_email, wp_specialchars_decode($title), $message)) {
            $msg = __('The email could not be sent.')."<br />\n".__('Possible reason: your host may have disabled the mail() function.',
                    'wiloke-listing-tools');

            if ($isAjax) {
                wp_send_json_error($msg);
            } else {
                return [
                    'status' => 'error',
                    'msg'    => $msg
                ];
            }
        }

        $msg = esc_html__('We sent an email to you with a link to get back into your account. Please check your mailbox and click on the reset link.',
            'wiloke-listing-tools');

        if ($isAjax) {
            wp_send_json_success($msg);
        } else {
            return [
                'status' => 'success',
                'msg'    => $msg
            ];
        }
    }

    public function printNeedToVerifyAccount()
    {
        \WilokeMessage::message(
            [
                'msg'        => __('We have sent an email with a confirmation link to your email address. In order to complete the sign-up process, please click the confirmation link.
If you do not receive a confirmation email, please check your spam folder. Also, please verify that you entered a valid email address in our sign-up form. <a href="#" class="wil-js-send-confirmation-code">Resend confirmation code</a>',
                    'wiloke-listing-tools'),
                'status'     => 'danger',
                'msgIcon'    => 'la la-bullhorn',
                'hasMsgIcon' => true
            ]
        );
    }

    public function verifyConfirmation()
    {
        if (!isset($_REQUEST['confirm_account'])) {
            return false;
        }
    }

    public function handleBecomeAnAuthorSubmission()
    {
        $this->middleware(['iAgreeToPrivacyPolicy', 'iAgreeToTerms'], [
            'agreeToTerms'         => $_POST['agreeToTerms'],
            'agreeToPrivacyPolicy' => $_POST['agreeToPrivacyPolicy']
        ]);

        if (User::canSubmitListing()) {
            wp_send_json_success();
        }

        UserModel::addSubmissionRole(get_current_user_id());

        do_action('wilcity/became-an-author', get_current_user_id());
        wp_send_json_success();
    }

    public function hideAdminBar($status)
    {
        if (!current_user_can('edit_theme_options')) {
            return false;
        }

        return $status;
    }

    public function modifyLogoutRedirectUrl($logout_url)
    {
        return apply_filters('wilcity/wiloke-listing-tools/filter/logout-redirect', home_url('/'));
    }

    public function resetPassword()
    {
        do_action('wilcity/before/register', $_POST);
        if (empty($_POST['username'])) {
            wp_send_json_error([
                'msg' => esc_html__('Please provide your username or email address.', 'wiloke-listing-tools')
            ]);
        } else if (strpos($_POST['username'], '@')) {
            $email     = trim($_POST['username']);
            $oUserData = get_user_by('email', $email);
            if (empty($oUserData)) {
                wp_send_json_error([
                    'msg' => esc_html__('Sorry, We found no account matched this email.', 'wiloke-listing-tools')
                ]);
            }

        } else {
            $login     = trim($_POST['username']);
            $oUserData = get_user_by('login', $login);

            if (empty($oUserData)) {
                wp_send_json_error([
                    'msg' => esc_html__('Sorry, We found no account matched this username.', 'wiloke-listing-tools')
                ]);
            }
        }

        $userEmail = $oUserData->user_email;
        $userLogin = $oUserData->user_login;

        $key = get_password_reset_key($oUserData);

        if (is_wp_error($key)) {
            return $key;
        }

        $aThemeOptions = \Wiloke::getThemeOptions(true);
        if (isset($aThemeOptions['reset_password_page']) && !empty($aThemeOptions['reset_password_page']) && get_post_status($aThemeOptions['reset_password_page']) == 'publish') {
            $resetURL = get_permalink($aThemeOptions['reset_password_page']);
            $resetURL = add_query_arg(
                [
                    'key'    => $key,
                    'login'  => rawurlencode($userLogin),
                    'action' => 'rp'
                ],
                $resetURL
            );
        } else {
            $resetURL = network_site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($userLogin), 'login');
        }

        $message = esc_html__('Someone has requested a password reset for the following account:',
                'wiloke-listing-tools')."\r\n\r\n";
        $message .= network_home_url('/')."\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $userLogin)."\r\n\r\n";
        $message .= esc_html__('If this was a mistake, just ignore this email and nothing will happen.',
                'wiloke-listing-tools')."\r\n\r\n";
        $message .= esc_html__('To reset your password, visit the following address:',
                'wiloke-listing-tools')."\r\n\r\n";
        $message .= '<'.$resetURL.">\r\n";

        if (is_multisite()) {
            $blogname = get_network()->site_name;
        } else {
            /*
             * The blogname option is escaped with esc_html on the way into the database
             * in sanitize_option we want to reverse this for the plain text arena of emails.
             */
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        }

        /* translators: Password reset email subject. 1: Site name */
        $title = sprintf(__('[%s] Password Reset', 'wiloke-listing-tools'), $blogname);
        if ($message && !wp_mail($userEmail, wp_specialchars_decode($title), $message)) {
            wp_send_json_error(
                [
                    'msg' => __('The email could not be sent.',
                            'wiloke-listing-tools')."<br />\n".__('Possible reason: your host may have disabled the mail() function.',
                            'wiloke-listing-tools')
                ]
            );
        }
        $aParseMail  = explode('@', $userEmail);
        $mailDomain  = end($aParseMail);
        $totalLength = count($aParseMail[0]);

        if ($totalLength > 5) {
            $truncateIndex = 4;
        } else {
            $truncateIndex = $totalLength - 2;
        }

        $escapeEmail = substr($aParseMail[0], 0, $truncateIndex).'***'.'@'.$mailDomain;
        wp_send_json_success(
            [
                'msg'             => sprintf(esc_html__('We just mailed a reset link to %s. Please check your mail box / spam box and click on that link.',
                    'wiloke-listing-tools'), $escapeEmail),
                'isFocusHideForm' => true
            ]
        );
    }

    public function handleRegister()
    {
        $this->middleware(['canRegister', 'validateGoogleReCaptcha'], $_POST);

        do_action('wilcity/before/register', $_POST);

        $aThemeOptions = \Wiloke::getThemeOptions();
        if (!isset($aThemeOptions['toggle_register']) || $aThemeOptions['toggle_register'] == 'disable') {
            wp_send_json_error([
                'msg' => esc_html__('Sorry, this feature is temporarily disabled due to maintenance. Please check back soon.',
                    'wiloke-listing-tools')
            ]);
        }

        if ($_POST['isAgreeToPrivacyPolicy'] == 'no' || $_POST['isAgreeToTermsAndConditionals'] == 'no') {
            wp_send_json_error([
                'msg' => esc_html__('ERROR: Sorry, To create an account on our site, you have to agree to our team conditionals and our privacy policy.',
                    'wiloke-listing-tools')
            ]);
        }

        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
            wp_send_json_error([
                'msg' => esc_html__('ERROR: Please complete all required fields.', 'wiloke-listing-tools')
            ]);
        }

        if (!is_email($_POST['email'])) {
            wp_send_json_error([
                'msg' => esc_html__('ERROR: Invalid email address.', 'wiloke-listing-tools')
            ]);
        }

        if (email_exists($_POST['email'])) {
            wp_send_json_error([
                'msg' => esc_html__('ERROR: An account with this email already exists on the website.',
                    'wiloke-listing-tools')
            ]);
        }

        if (username_exists($_POST['username'])) {
            wp_send_json_error([
                'msg' => esc_html__('ERROR: Sorry, The username is not available. Please with another username.',
                    'wiloke-listing-tools')
            ]);
        }

        if (preg_match('/\s/', $_POST['username'], $aSpace)) {
            wp_send_json_error([
                'msg' => esc_html__('Please do not use space in the username.', 'wiloke-listing-tools')
            ]);
        }

        $aStatus = UserModel::createNewAccount($_POST);

        if ($aStatus['status'] == 'error') {
            wp_send_json_error([
                'msg' => esc_html__('ERROR: Something went wrong', 'wiloke-listing-tools')
            ]);
        }

        if ($aStatus['status'] == 'success' && !$aStatus['isNeedConfirm']) {
            $successMsg = esc_html__('Congratulations! Your account has been created successfully.',
                'wiloke-listing-tools');
        } else {
            $successMsg = $aStatus['msg'];
        }

        $ssl = is_ssl() ? true : false;
        wp_signon([
            'user_login'    => $_POST['email'],
            'user_password' => $_POST['password'],
            'remember'      => false
        ], $ssl);

        $redirectTo = isset($aThemeOptions['created_account_redirect_to']) ? urlencode(get_permalink($aThemeOptions['created_account_redirect_to'])) : 'self';

        do_action('wilcity/after/created-account', $aStatus['userID'], $_POST['username'], $aStatus['isNeedConfirm']);

        wp_send_json_success([
            'redirectTo' => $redirectTo,
            'msg'        => $successMsg
        ]);
    }

    public function handleLogin()
    {
        $this->middleware(['validateGoogleReCaptcha'], $_POST);

        $aData = [
            'user_login'    => $_POST['username'],
            'user_password' => $_POST['password'],
            'remember'      => isset($_POST['isRemember']) && $_POST['isRemember'] == 'yes'
        ];
        do_action('wilcity/before/login', $aData);

        $oUser = wp_signon($aData, is_ssl());

        if (is_wp_error($oUser)) {
            wp_send_json_error([
                'msg' => esc_html__('ERROR: Invalid username or password', 'wiloke-listing-tools')
            ]);
        }

        $aThemeOption = \Wiloke::getThemeOptions();

        wp_send_json_success([
            'msg'        => sprintf(esc_html__('Hi %s! Nice to see you back.', 'wiloke-listing-tools'),
                $_POST['username']),
            'redirectTo' => isset($aThemeOption['login_redirect_type']) && $aThemeOption['login_redirect_type'] == 'specify_page' ? urlencode(get_permalink($aThemeOption['login_redirect_to'])) : 'self'
        ]);
    }

    public function printFooterCode()
    {
        if (is_user_logged_in()) {
            return false;
        }
        ?>
        <login-register-popup></login-register-popup>
        <?php
    }

    public function enqueueScripts()
    {
        if (is_user_logged_in() || !class_exists('WilokeThemeOptions')) {
            return false;
        }
        global $wiloke;

        wp_localize_script('jquery-migrate', strtoupper(WILCITY_WHITE_LABEL).'_REGISTER_LOGIN', [
            'toggleRegister'             => \WilokeThemeOptions::isEnable('toggle_register'),
            'togglePrivacyPolicy'        => \WilokeThemeOptions::isEnable('toggle_privacy_policy'),
            'privacyPolicyDesc'          => $wiloke->aThemeOptions['privacy_policy_desc'],
            'toggleTermsAndConditionals' => \WilokeThemeOptions::isEnable('toggle_terms_and_conditionals'),
            'termsAndConditionals'       => $wiloke->aThemeOptions['terms_and_conditionals_desc']
        ]);
    }

    public function printRegisterLoginButton()
    {
        if (is_user_logged_in()) {
            return '';
        }
        ?>
        <div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix',
            'wilcity-login-register-controller')); ?>" class="header_login__1sQ6w">
            <div class="header_btnGroup__3L61P">
                <?php if (\WilokeThemeOptions::isEnable('toggle_custom_login_page')) : ?>
                    <?php 

                        $url_login = $url = GetSettings::getCustomLoginPage();
                        
                        if( \WilokeThemeOptions::getOptionDetail('login_redirect_type') == "self_page" ) {
                            global $wp;  
                            $url_login = add_query_arg( array(
                                'redirect_to' => home_url( add_query_arg( array($_GET), $wp->request) )
                            ), $url );
                        }

                    ?>
                    <a id="wilcity-login-btn" href="<?php echo $url_login ?>"
                       class="wil-btn wil-btn--primary2 wil-btn--round wil-btn--xs"><?php esc_html_e('Login',
                            'wiloke-listing-tools'); ?></a>
                    <?php if (self::canRegister()) : ?>
                        <a id="wilcity-register-btn" href="<?php echo add_query_arg(['action' => 'register'], $url) ?>"
                           class="wil-btn wil-btn--secondary wil-btn--round wil-btn--xs"><?php esc_html_e('Register',
                                'wiloke-listing-tools'); ?></a>
                    <?php endif; ?>
                <?php else: ?>
                    <login-btn btn-name="<?php esc_html_e('Login', 'wiloke-listing-tools'); ?>"></login-btn>
                    <?php if (self::canRegister()) : ?>
                        <register-btn
                                btn-name="<?php esc_html_e('Register', 'wiloke-listing-tools'); ?>"></register-btn>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
