<?php

namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\DebugStatus;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\HTML;
use WilokeListingTools\Framework\Helpers\Submission;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\UserModel;

class AddListingButtonController extends Controller
{
    private $needBecomeAnAuthor = false;

    public function __construct()
    {
        add_filter('wilcity/single-listing/add-new-listing', [$this, 'addNewListing']);
        add_filter('wilcity/single-listing/edit-listing', [$this, 'editListing'], 10, 2);
        add_action('wilcity/single-listing/wil-content', [$this, 'printEditButton'], 10, 2);
        add_action('wilcity/single-event/wil-content', [$this, 'printEditButton'], 10, 2);
        add_action('wilcity/header/after-menu', [$this, 'printAddListingButton']);
        add_filter('wilcity/submission/pricingUrl', [$this, 'generatePricingUrl'], 10, 3);
        add_action('wp_ajax_wilcity_get_edit_url', [$this, 'ajaxGetEditUrl']);
        add_action('wp_ajax_wilcity_change_plan_for_post', [$this, 'ajaxChangePlanForThisPost']);
        add_filter('wilcity/add-new-event-url', [$this, 'addNewEventUrl'], 10, 2);
        add_filter('wilcity/wiloke-submission/box-listing-type-url', [$this, 'buildBoxUrl'], 10, 2);
    }

    public function buildBoxUrl($url, $aInfo)
    {
        $aLinkArgs = [
            'listing_type' => $aInfo['key']
        ];
        $planType  = $aInfo['key'].'_plan';

        if (GetWilokeSubmission::getField('add_listing_mode') == 'free_add_listing') {
            $addListingUrl = GetWilokeSubmission::getField('addlisting', true);
            $addListingUrl = add_query_arg(
                $aLinkArgs,
                $addListingUrl
            );

            return $addListingUrl;
        } else {
            if (!DebugStatus::status('WILOKE_ALWAYS_PAY') && !GetWilokeSubmission::isNonRecurringPayment() && !empty(UserModel::getLatestUserPlan($planType)) && !UserModel::isExceededRecurringPaymentPlan($aInfo['key'].'_plan')) {
                $url    = GetWilokeSubmission::getField('addlisting', true);
                $planID = UserModel::getLatestPlanID($aInfo['key'].'_plan');
                if (!empty($planID)) {
                    $aLinkArgs['planID'] = $planID;
                }
            }
        }

        $url = add_query_arg(
            $aLinkArgs,
            $url
        );

        return $url;
    }

    public function addNewEventUrl($addListingUrl, $post)
    {
        $addListingUrl = GetWilokeSubmission::getField('package', true);

        $addListingUrl = add_query_arg(
            [
                'listing_type' => 'event',
                'parentID'     => isset($post->ID) ? $post->ID : ''
            ],
            $addListingUrl
        );

        return $addListingUrl;
    }

    public function ajaxChangePlanForThisPost()
    {
        $this->middleware(['isPublishedPost'], [
            'postID' => $_POST['postID']
        ]);

        $addListingUrl = GetWilokeSubmission::getField('package', true);

        $addListingUrl = add_query_arg(
            [
                'postID'       => $_POST['postID'],
                'listing_type' => get_post_type($_POST['postID'])
            ],
            $addListingUrl
        );

        wp_send_json_success(['url' => $addListingUrl]);
    }

    public function ajaxGetEditUrl()
    {
        $this->middleware(['isPostAuthor'], [
            'postID'        => $_POST['postID'],
            'passedIfAdmin' => true
        ]);
//		rygkliniksjaelland@gmail.com
        $postStatus = get_post_status($_POST['postID']);

        $planID = GetSettings::getPostMeta($_POST['postID'], 'belongs_to');
        if (empty($planID) || $postStatus == 'expired') {
            $addListingUrl = GetWilokeSubmission::getField('package', true);
        } else {
            $addListingUrl = GetWilokeSubmission::getField('addlisting', true);
        }

        $addListingUrl = add_query_arg(
            [
                'postID'       => $_POST['postID'],
                'planID'       => $planID,
                'listing_type' => get_post_type($_POST['postID'])
            ],
            $addListingUrl
        );

        wp_send_json_success(['url' => $addListingUrl]);
    }

    public function addNewListing($postType)
    {
        $addListingUrl = GetWilokeSubmission::getField('addlisting', true);
        $addListingUrl = add_query_arg(
            [
                'listing_type' => $postType
            ],
            $addListingUrl
        );

        return $addListingUrl;
    }

    public function editListing($nothing, $post)
    {
        $planID = GetSettings::getPostMeta($post->ID, 'belongs_to');

        if (!empty($planID) || get_post_status($planID) !== 'publish') {
            $planID = '';
        }

        if (!current_user_can('administrator') && $post->post_author !== User::getCurrentUserID()) {
            return '';
        }

        if (empty($planID)) {
            if (!GetWilokeSubmission::isFreeAddListing()) {
                $addListingUrl = GetWilokeSubmission::getField('package', true);
            } else {
                $addListingUrl = GetWilokeSubmission::getField('addlisting', true);
                $planID        = GetWilokeSubmission::getFreePlan($post->post_type);
            }
        } else {
            $addListingUrl = GetWilokeSubmission::getField('addlisting', true);
        }

        $addListingUrl = add_query_arg(
            [
                'postID'       => $post->ID,
                'planID'       => $planID,
                'listing_type' => $post->post_type
            ],
            $addListingUrl
        );

        return $addListingUrl;
    }

    public function generatePricingUrl($planID, $postID, $aAtts)
    {
        $aArgs = [
            'planID'       => $planID,
            'listing_type' => $aAtts['listing_type']
        ];
        if (!empty($postID)) {
            $aArgs['postID'] = $postID;
        }

        if (isset($aAtts['parentID'])) {
            $aArgs['parentID'] = $aAtts['parentID'];
        }

        return add_query_arg(
            $aArgs,
            GetWilokeSubmission::getField('addlisting', true)
        );
    }

    public function printAddListingButton()
    {
        $toggle              = GetWilokeSubmission::getField('toggle');
        $toggle_becomeauthor = GetWilokeSubmission::isEnable('toggle_become_an_author');
        $pageUrl             = GetWilokeSubmission::getField('become_an_author_page', true);
        if (!User::isUserLoggedIn() && $toggle != 'disable' && $toggle_becomeauthor) {
            global $wiloke;
            if (\WilokeThemeOptions::isEnable('toggle_custom_login_page', false)) {
                ?>
                <div class="header_loginItem__oVsmv wilcity-become-an-author">
                    <?php HTML::renderLink('wil-btn--secondary wil-btn--round wil-btn--xs mr-10',
                        esc_html__('Become an author', 'wiloke-listing-tools'), add_query_arg(['action' => 'register'],
                            get_permalink($wiloke->aThemeOptions['custom_login_page'])), 'la la-user'); ?>
                </div>
                <?php
            } else {
                ?>
                <div class="header_loginItem__oVsmv wilcity-become-an-author">
                    <?php HTML::renderLink('wilcity-trigger-register-button wil-btn--secondary wil-btn--round wil-btn--xs mr-10',
                        esc_html__('Become an author', 'wiloke-listing-tools'), $pageUrl, 'la la-user'); ?>
                </div>
                <?php
            }

            return '';
        }

        $toggle = apply_filters('wilcity/wiloke-listing-tools/filter/printAddListingButton/status', $toggle);

        if ($toggle == 'disable') {
            return '';
        }

        if (!User::canSubmitListing(User::getCurrentUserID(), false)) {
            if (GetWilokeSubmission::isEnable('toggle_become_an_author')) {
                $this->needBecomeAnAuthor = true;
            } else {
                return '';
            }
        }

        if ($this->needBecomeAnAuthor) { ?>
            <div class="header_loginItem__oVsmv wilcity-become-an-author">
                <?php HTML::renderLink('wil-btn--secondary wil-btn--round wil-btn--md',
                    esc_html__('Become an author', 'wiloke-listing-tools'), $pageUrl, 'la la-user'); ?>
            </div>
            <?php
        } else {
            $addListingUrl = GetWilokeSubmission::getField('package', true);
            if (empty($addListingUrl)) {
                return '';
            }

            $addListingUrl = apply_filters('wilcity/wiloke-listing-tools/addlisting/filter/addlistingurl',
                $addListingUrl);
            $aPlans        = Submission::getAddListingPostTypeKeys();

            if (count($aPlans) == 1) {
                if (GetWilokeSubmission::isFreeAddListing()) {
                    $addListingUrl = add_query_arg(
                        [
                            'listing_type' => $aPlans[0]
                        ],
                        GetWilokeSubmission::getField('addlisting', true)
                    );
                } else {
                    $addListingUrl = add_query_arg(
                        [
                            'listing_type' => $aPlans[0]
                        ],
                        $addListingUrl
                    );
                }
            }
            ?>
            <div class="header_loginItem__oVsmv header-addlisting">
                <?php HTML::renderLink('wil-addlisting-btn wil-btn wil-btn--primary2 wil-btn--round wil-btn--md',
                    esc_html__('Add Listing', 'wiloke-listing-tools'), $addListingUrl, 'la la-pencil-square'); ?>
            </div>
            <?php
        }
    }

    public function printEditButton($post, $isFocused = false)
    {
        if (!is_user_logged_in()) {
            return '';
        }

        if (DebugStatus::status('WILCITY_DISABLE_EDIT_BUTTON')) {
            return '';
        }

        if (!$isFocused && ((Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore')) != $post->ID))) {
            return '';
        }

        if ((!current_user_can('administrator') && (User::getCurrentUserID() != $post->post_author))) {
            return false;
        }

        global $post;

        $planID = Session::getSession(wilokeListingToolsRepository()->get('payment:storePlanID'));

        if (empty($planID) && $isFocused) {
            $planID = GetSettings::getPostMeta($post->ID, 'belongs_to');
        }

        $aAddListingArgs['postID'] = $post->ID;
        if (empty($planID)) {
            if (!GetWilokeSubmission::isFreeAddListing()) {
                $addListingUrl = GetWilokeSubmission::getField('package', true);
            } else {
                $addListingUrl             = GetWilokeSubmission::getField('addlisting', true);
                $planID                    = GetWilokeSubmission::getFreePlan($post->post_type);
                $aAddListingArgs['planID'] = $planID;
            }
        } else {
            $addListingUrl             = GetWilokeSubmission::getField('addlisting', true);
            $aAddListingArgs['planID'] = $planID;
        }

        if (isset($post->ID)) {
            $aAddListingArgs['listing_type'] = get_post_type($post->ID);
        }

        $addListingUrl = add_query_arg(
            $aAddListingArgs,
            $addListingUrl
        );
        $aPlanSettings = GetSettings::getPlanSettings($planID);
        if (empty($aPlanSettings['regular_price'])) {
            $btnName = __('Submit Listing', 'wiloke-listing-tools');
        } else {
            $btnName = __('Pay & Publish', 'wiloke-listing-tools');
        }
        echo '<div class="btn-group-fixed_module__3qULF pos-f-right-bottom text-right">';
        HTML::renderLink('wil-btn--secondary wil-btn--round wil-btn--md',
            esc_html__('Edit Listing', 'wiloke-listing-tools'), $addListingUrl, 'la la-edit');
        echo '<div class="mb-10"></div>';

        if ($post->post_status != 'publish' && Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore')) == $post->ID && Session::getSession(wilokeListingToolsRepository()->get('payment:storePlanID'))) {
            $checkoutUrl = GetWilokeSubmission::getField('checkout', true);
            HTML::renderLink('wil-btn--primary2 wil-btn--round wil-btn--md disable', esc_html($btnName), $checkoutUrl,
                'la la-send', 'wilcity-submit', true);
        }
        echo '</div>';
    }
}
