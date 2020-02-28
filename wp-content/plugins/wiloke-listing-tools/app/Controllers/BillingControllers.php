<?php

namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\InvoiceModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\UserModel;

class BillingControllers extends Controller
{
    public $limit = 4;

    public function __construct()
    {
        add_action('wp_ajax_wilcity_fetch_my_billings', [$this, 'fetchBillings']);
        add_action('wp_ajax_wilcity_fetch_my_billing_details', [$this, 'fetchBillingDetails']);
        add_action('wp_ajax_wilcity_fetch_my_plan', [$this, 'fetchMyPlan']);
        add_action('wp_ajax_wilcity_post_type_plans', [$this, 'fetchPostTypePlans']);
        add_action('wp_ajax_wilcity_fetch_billing_type', [$this, 'getBillingType']);
    }

    public function getBillingType()
    {
        wp_send_json_success([
            'type' => GetWilokeSubmission::getBillingType()
        ]);
    }

    public function fetchPostTypePlans()
    {
        $aGateways = GetWilokeSubmission::getGatewaysWithName(true);
        $noGateway = esc_html__('There are Payment Gateways', 'wiloke-listing-tools');

        if (empty($aGateways)) {
            wp_send_json_error([
                'msg' => $noGateway
            ]);
        }

        if (!isset($_POST['postType']) || empty($_POST['postType'])) {
            wp_send_json_error([
                'msg' => esc_html__('The post type is required', 'wiloke-listing-tools')
            ]);
        }

        $aPlanIDs = GetWilokeSubmission::getAddListingPlans($_POST['postType'].'_plans');
        if (empty($aPlanIDs)) {
            wp_send_json_error([
                'msg' => esc_html__('We found no plans in this listing type.', 'wiloke-listing-tools')
            ]);
        }

        $query = new \WP_Query(
            [
                'post_type'   => 'listing_plan',
                'post_status' => 'publish',
                'post__in'    => $aPlanIDs,
                'orderby'     => 'post__in'
            ]
        );

        if (!$query->have_posts()) {
            wp_send_json_error([
                'msg' => esc_html__('We found no plans in this listing type.', 'wiloke-listing-tools')
            ]);
        }

        $aPlans = [];
        while ($query->have_posts()) { $query->the_post();
            global $post;
            $aPlanSettings = GetSettings::getPlanSettings($post->ID);
            $aPlans[] = [
                'postTitle' => get_the_title($post->ID),
                'content'   => $post->post_content,
                'ID'        => $post->ID,
                'price'     => \WILCITY_SC\SCHelpers::renderPlanPrice($aPlanSettings['regular_price'])
            ];
        }
        wp_reset_postdata();

        $aGatewayOptions    = [];
        $isUsingWooCommerce = false;
        if ($_POST['currentGateway'] == 'free') {
            foreach ($aGateways as $gateway => $name) {
                if ($isUsingWooCommerce) {
                    continue;
                }

                if ($gateway == 'woocommerce') {
                    $isUsingWooCommerce = true;
                } else {
                    $aGatewayOptions[] = [
                        'name'  => $name,
                        'value' => $gateway
                    ];
                }
            }
        }

        wp_send_json_success([
            'aPlans'             => $aPlans,
            'aGateways'          => $aGatewayOptions,
            'isUsingWooCommerce' => $isUsingWooCommerce
        ]);
    }

    public function fetchMyPlan()
    {
        $aRawUserPlans = UserModel::getAllPlans(User::getCurrentUserID());

        if (empty($aRawUserPlans)) {
            wp_send_json_error([
                'msg' => esc_html__('You have not used any plan.', 'wiloke-listing-tools')
            ]);
        }

        $aUserPlans = [];
        $order      = 0;
        foreach ($aRawUserPlans as $aPlans) {
            foreach ($aPlans as $planID => $aPlan) {
                $aUserPlans[$order] = $aPlan;
                $planTitle          = get_the_title($planID);
                $planTitle          = empty($planTitle) ? PaymentMetaModel::get($aPlan['paymentID'],
                    'planName') : $planTitle;

                $aUserPlans[$order]['planName'] = !empty($planTitle) ? $planTitle : esc_html__('This plan might have been deleted.',
                    'wiloke-listing-tools');
                $aUserPlans[$order]['planID']   = $planID;
                if (GetWilokeSubmission::isNonRecurringPayment($aPlan['billingType'])) {
                    $aUserPlans[$order]['nextBillingDate'] = esc_html__('No', 'wiloke-listing-tools');
                } else {
                    if (empty($aUserPlans[$order]['nextBillingDateGMT'])) {
                        $aUserPlans[$order]['nextBillingDate'] = esc_html__('Updating', 'wiloke-listing-tools');
                    } else {
                        $aUserPlans[$order]['nextBillingDate'] = date_i18n(get_option('date_format'),
                            $aUserPlans[$order]['nextBillingDateGMT']);
                    }
                }
                if (!isset($aPlan['postType'])) {
                    $aUserPlans[$order]['postType'] = '';
                } else {
                    $aUserPlans[$order]['postTypeName'] = GetSettings::getPostTypeField('name',
                        $aUserPlans[$order]['postType']);
                }
                $aUserPlans[$order]['isNonRecurringPayment'] = GetWilokeSubmission::isNonRecurringPayment($aPlan['billingType']) ? 'yes' : 'no';
                $order++;
            }
        }

        wp_send_json_success($aUserPlans);
    }

    public function fetchBillingDetails()
    {
        $aResult = InvoiceModel::getInvoiceDetails($_POST['invoiceID']);
        if (empty($aResult)) {
            wp_send_json_error([
                'msg' => esc_html__('This plan might have been deleted', 'wiloke-listing-tools')
            ]);
        }

        wp_send_json_success($aResult);
    }

    public function fetchBillings()
    {
        $offset = (abs($_POST['page']) - 1) * $this->limit;

        $aInvoices = InvoiceModel::getMyInvoices($this->limit, $offset);
        if (empty($aInvoices)) {
            if ($_POST['page'] > 1) {
                wp_send_json_error([
                    'reachedMaximum' => 'yes'
                ]);
            } else {
                wp_send_json_error(['msg' => esc_html__('There are no invoices', 'wiloke-listing-tools')]);
            }
        }
        wp_send_json_success($aInvoices);
    }
}
