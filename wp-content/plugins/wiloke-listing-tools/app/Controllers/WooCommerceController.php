<?php
namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\WooCommerce;
use WilokeListingTools\Framework\Payment\Receipt;
use WilokeListingTools\Framework\Payment\WooCommerce\WooCommerceChangePlan;
use WilokeListingTools\Framework\Payment\WooCommerce\WooCommerceNonRecurringPayment;
use WilokeListingTools\Framework\Payment\WooCommerce\WooCommerceRecurringPayment;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\PlanRelationshipModel;

class WooCommerceController extends Controller
{
    public $planID;
    public $productID;
    public $orderID;
    public $oReceipt;
    protected $aPaymentIDs;
    public $gateway = 'woocommerce';
    private $excludeFromShopKey = 'exclude_from_shop';

    public function __construct()
    {
        add_action('wp_ajax_wiloke_change_plan_via_woocommerce', [$this, 'changePlan']);

        add_action('wiloke-listing-tools/before-redirecting-to-cart', [$this, 'removeProductFromCart'], 10, 1);
        add_filter('woocommerce_add_to_cart_validation', [$this, 'cleanEverythingBeforeAddProductToCart'], 0);
        add_action('woocommerce_add_to_cart', [$this, 'removeAssociatePlanItems'], 0);
        add_action('wiloke-listing-tools/payment-via-woocommerce', [$this, 'preparePayment'], 10, 2);

        add_action('woocommerce_thankyou', [$this, 'updateCategoryToOrderMeta'], 10, 1);
//		add_action( 'woocommerce_order_status_pending', array($this, 'updateCategoryToOrderMeta'), 10, 1 );

        // Change Plan
//		add_action('woocommerce_order_status_completed', array($this, 'afterNewSubscriptionPlanCreated'));

        add_action('woocommerce_checkout_order_processed', [$this, 'newOrderCreated'], 5, 2);
        add_action('woocommerce_order_status_completed', [$this, 'paymentSucceeded'], 1);
        add_action('woocommerce_order_status_failed', [$this, 'paymentFailed'], 5);
        add_action('woocommerce_order_status_on-hold', [$this, 'paymentOnHold'], 5);
        add_action('woocommerce_order_status_refunded', [$this, 'paymentRefunded'], 5);
        add_action('woocommerce_order_status_cancelled', [$this, 'paymentCancelled'], 5);
        add_action('woocommerce_order_status_processing', [$this, 'paymentProcessing'], 5);
        add_action('woocommerce_thankyou', [$this, 'autoCompleteOrder'], 10, 1);
        add_action('woocommerce_single_product_summary', [$this, 'removeGalleryOfWooBookingOnTheSidebar'], 1);
        add_action('wilcity/before-close-header-tag', [$this, 'addQuickCart']);
        add_action('wiloke-listing-tools/payment-pending', [$this, 'maybeSaveOldOrderIDIfItIsChangePlanSession']);

        /**
         * WooCommerce Subscription
         */
        add_action('woocommerce_subscription_payment_complete', [$this, 'maybeTrial'], 99, 1);

        /*
         * Exclude Add Listing Production From Shop page
         *
         * @since 1.2.0
         */
        add_action('updated_postmeta', [$this, 'addedListingProductToExcludeFromShopPage'], 10, 4);
        add_action('woocommerce_product_query', [$this, 'modifyWooQueryToExcludeShopPage'], 10);

        add_filter('wilcity/woocommerce/content-single-product/before-single-product-summary',
            [$this, 'willNotShowUpBeforeSingleProductIfIsBookingWidget']);
        add_filter('wilcity/woocommerce/content-single-product/after-single-product-summary',
            [$this, 'willNotShowUpBeforeSingleProductIfIsBookingWidget']);
        add_filter('wilcity/woocommerce/content-single-product/after-single-product',
            [$this, 'willNotShowUpBeforeSingleProductIfIsBookingWidget']);
    }

    /**
     * @param $status
     *
     * @return bool
     */
    public function willNotShowUpBeforeSingleProductIfIsBookingWidget($status)
    {
        return General::$isBookingFormOnSidebar ? false : $status;
    }

    /**
     * @param $metaID
     * @param $postID
     * @param $metaKey
     * @param $metaVal
     */
    public function addedListingProductToExcludeFromShopPage($metaID, $postID, $metaKey, $metaVal)
    {
        if ($metaKey == 'wilcity_woocommerce_association') {
            SetSettings::setPostMeta($metaVal, $this->excludeFromShopKey, 'yes');
        }
    }

    /**
     * @param $query
     *
     * @return bool
     */
    public function modifyWooQueryToExcludeShopPage($query)
    {
        if (!is_shop() && !is_product_category() && !is_product_tag()) {
            return false;
        }

        $aMetaQueries   = $query->get('meta_query');
        $aMetaQueries   = empty($aMetaQueries) ? [] : $aMetaQueries;
        $aMetaQueries[] = [
            'relation' => 'OR',
            [
                'key'     => 'wilcity_exclude_from_shop',
                'compare' => 'NOT EXISTS'
            ],
            [
                'key'     => 'wilcity_exclude_from_shop',
                'value'   => 'yes',
                'compare' => '!='
            ]
        ];

        $query->set('meta_query', $aMetaQueries);
    }

    /*
     * If this is Trial Plan, We will saved this information to Payment Meta (This is for Woo Subscription only)
     *
     * @since 1.1.7.2
     */
    public function maybeTrial(\WC_Subscription $that)
    {
        $aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($that->get_parent_id());
        if (empty($aPaymentIDs)) {
            return false;
        }

        $trial = $that->get_date('trial_end');
        if (!empty($trial)) {
            foreach ($aPaymentIDs as $aPayment) {
                PaymentMetaModel::set($aPayment['ID'], wilokeListingToolsRepository()->get('addlisting:isUsingTrial'),
                    'yes');
            }
        } else {
            foreach ($aPaymentIDs as $aPayment) {
                PaymentMetaModel::delete($aPayment['ID'],
                    wilokeListingToolsRepository()->get('addlisting:isUsingTrial'));
            }
        }
    }

    /*
     * If it's change plan session, We will save old order id to Payment Meta.
     * This step is very important, because We will upgrade Listings that belong to old order plan to new order plan
     */
    public function maybeSaveOldOrderIDIfItIsChangePlanSession($aInfo)
    {
        $oldOrderID = Session::getSession(wilokeListingToolsRepository()->get('payment:wooOldOrderID'), true);

        if (!empty($oldOrderID)) {
            PaymentMetaModel::set($aInfo['paymentID'], 'oldOrderID', $oldOrderID);
        } else {
            // If the old plan is Free Plan
            $oldPaymentID = Session::getSession(wilokeListingToolsRepository()->get('payment:oldPaymentID'), false);
            if (!empty($oldPaymentID)) {
                if (PaymentModel::getField('userID', $oldPaymentID) == User::getCurrentUserID()) {
                    PaymentMetaModel::set($aInfo['paymentID'], 'oldPaymentID', $oldPaymentID);
                }
            }
        }
    }

    /*
     * Change WooCommerce Subscription Plan
     *
     * @since 1.1.7.3
     */
    public function changePlan()
    {
        if (!isset($_POST['newPlanID']) || !isset($_POST['currentPlanID']) || !isset($_POST['paymentID']) || !isset($_POST['postType'])) {
            wp_send_json_error([
                'msg' => esc_html__('ERROR: The new plan, current plan, post type and payment ID are required',
                    'wiloke-listing-tools')
            ]);
        }

        $userID = get_current_user_id();
        $this->middleware(['isMyPaymentSession'], [
            'paymentID' => abs($_POST['paymentID']),
            'userID'    => $userID
        ]);

        $oWooCommerceChangePlan = new WooCommerceChangePlan($userID, $_POST['paymentID'], $_POST['newPlanID'],
            $_POST['currentPlanID'], $_POST['postType']);
        $aStatus                = $oWooCommerceChangePlan->execute();

        if ($aStatus['status'] == 'success') {
            wp_send_json_success($aStatus);
        } else {
            wp_send_json_error($aStatus);
        }
    }

    public function addQuickCart()
    {
        if (class_exists('woocommerce')) {
            // Is this the cart page?
            if (is_cart() || WC()->cart->get_cart_contents_count() == 0) {
                return false;
            }
            ?>
            <div class="header_cartWrap__bOA2i active widget woocommerce widget_shopping_cart">
                <div class="header_cartBtn__1gAQU">
                    <span class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                        'wilcity-total-cart-item')); ?>"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
                    <div class="header_cartIcon__18VjH">
                        <i class="la la-shopping-cart"></i>
                    </div>
                    <div class="header_product__1q6pw product-cart-js">
                        <header class="header_cartHeader__2LxzS"><h4 class="header_cartTitle__l46ln"><i
                                        class="la la-shopping-cart"></i><?php echo esc_html__('Total Items',
                                    'wiloke-listing-tools'); ?> <span
                                        class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                                            'wilcity-total-cart-item')); ?>"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
                            </h4></header>

                        <div class="widget_shopping_cart_content">
                            <?php woocommerce_mini_cart(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    public function removeGalleryOfWooBookingOnTheSidebar()
    {
        if (General::$isBookingFormOnSidebar) {
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
            remove_action('woocommerce_template_single_price', 'woocommerce_template_single_price', 15);
//			remove_action('woocommerce_single_product_summary','woocommerce_template_single_excerpt', 20);
        }
    }

    public function autoCompleteOrder($orderID)
    {
        if (!$orderID) {
            return;
        }

        $order         = wc_get_order($orderID);
        $paymentMethod = get_post_meta($orderID, '_payment_method', true);
        // No updated status for orders delivered with Bank wire, Cash on delivery and Cheque payment methods.

        if (in_array($paymentMethod, ['bacs', 'cod', 'cheque'])) {
            return;
        } elseif ($order->has_status('processing')) {
            $paymentID = PaymentModel::getPaymentIDsByWooOrderID($orderID, true);
            if (!empty($paymentID)) {
                $order->update_status('completed');
            }
        }
    }

    public function cleanEverythingBeforeAddProductToCart($cart_item_data)
    {

        if (!isset($_GET['add-to-cart']) || empty($_GET['add-to-cart']) && !is_cart()) {
            return $cart_item_data;
        }

        global $woocommerce;

        $planID = PlanRelationshipModel::getPlanIDByProductID($_GET['add-to-cart']);
        if (empty($planID)) {
            return $cart_item_data;
        }

        $woocommerce->cart->empty_cart();

        return true;
    }

    public function removeAssociatePlanItems()
    {
        global $woocommerce;
        if ($woocommerce->cart->get_cart_contents_count() == 0) {
            return false;
        }

        $productID = Session::getSession(wilokeListingToolsRepository()->get('payment:associateProductID'));
        if (empty($productID)) {
            return false;
        }

        foreach ($woocommerce->cart->get_cart() as $cartItemKey => $aCardItem) {
            $planID = PlanRelationshipModel::getPlanIDByProductID($productID);
            if (empty($planID)) {
                continue;
            }

            if ($aCardItem['product_id'] != $productID) {
                $woocommerce->cart->remove_cart_item($cartItemKey);
            }
        }
    }

    public function removeProductFromCart($productIDs)
    {
        global $woocommerce;
        foreach ($woocommerce->cart->get_cart() as $cartItemKey => $aCardItem) {
            if (is_array($productIDs)) {
                if (in_array($aCardItem['product_id'], $productIDs)) {
                    $woocommerce->cart->remove_cart_item($cartItemKey);
                }
            } else {
                if ($aCardItem['product_id'] == $productIDs) {
                    $woocommerce->cart->remove_cart_item($cartItemKey);
                }
            }
        }
    }

    public function updateStatusOfPaymentViaWooCommerce($orderID, $status)
    {
        $aSessionIDs = PaymentModel::getPaymentIDsByWooOrderID($orderID);

        if (empty($aSessionIDs)) {
            return false;
        }

        foreach ($aSessionIDs as $aSession) {
            $this->aPaymentIDs[] = $aSession['ID'];
            PaymentModel::updatePaymentStatus($status, $aSession['ID']);

            if ($status == 'pending' || $status == 'onhold') {
                PostController::migratePostsToExpiredStatus($aSession['ID']);
            } else if ($status == 'completed' || $status == 'processing') {
                PostController::migratePostsToPendingOrPublishStatus($aSession['ID']);
            } else if ($status == 'refunded' || $status == 'cancelled') {
                PostController::migratePostsToDraftStatus($aSession['ID']);
            }
        }
    }

    public function updatePaymentStatusByOrderID($orderID, $status)
    {
        $oOrder = new \WC_Order($orderID);
        $aItems = $oOrder->get_items();

        $planID = '';
        foreach ($aItems as $aItem) {
            $productID = $aItem['product_id'];
            $planID    = PlanRelationshipModel::getPlanIDByProductID($productID);

            do_action('wiloke-listing-tools/woocommerce/order-'.$status, [
                'planID'    => $planID,
                'status'    => $status,
                'gateway'   => $this->gateway,
                'productID' => $productID,
                'orderID'   => $orderID
            ]);

            $this->updateStatusOfPaymentViaWooCommerce($orderID, $status);
        }

        do_action('wiloke-listing-tools/woocommerce/after-order-'.$status, [
            'planID'  => $planID,
            'status'  => $status,
            'gateway' => $this->gateway,
            'orderID' => $orderID
        ]);
    }

    public function paymentOnHold($orderID)
    {
        $this->updatePaymentStatusByOrderID($orderID, 'onhold');
    }

    public function paymentRefunded($orderID)
    {
        $this->updatePaymentStatusByOrderID($orderID, 'refunded');
    }

    public function paymentCancelled($orderID)
    {
        $this->updatePaymentStatusByOrderID($orderID, 'cancelled');
    }

    public function paymentProcessing($orderID)
    {
        $this->updatePaymentStatusByOrderID($orderID, 'processing');
    }

    public function paymentPending($orderID)
    {
        $this->updatePaymentStatusByOrderID($orderID, 'pending');
    }

    public function paymentFailed($orderID)
    {
        $this->updatePaymentStatusByOrderID($orderID, 'failed');
    }

    public function updateCategoryToOrderMeta($orderID)
    {
        $paymentID = PaymentModel::getPaymentIDsByWooOrderID($orderID, true);
        if (empty($paymentID)) {
            return false;
        }

        $category = Session::getSession(wilokeListingToolsRepository()->get('payment:category'));

        if (!empty($category)) {
            wc_update_order_item_meta($orderID, '_wilcity_plan_category', $category);
        }
    }

    /*
     * After payment has been successfully, We will use Order ID to get needed information
     *
     * @since 1.0
     */
    public function paymentSucceeded($orderID)
    {
        $oOrder    = new \WC_Order($orderID);
        $aItems    = $oOrder->get_items();
        $planID    = '';
        $paymentID = PaymentModel::getPaymentIDsByWooOrderID($orderID, true);

        if (empty($paymentID)) {
            return false;
        }

        $category = Session::getSession(wilokeListingToolsRepository()->get('payment:category'), true);
        if (empty($category)) {
            $category = wc_get_order_item_meta($orderID, '_wilcity_plan_category', true);
        }
        $category  = empty($category) ? md5('dadadadad') : $category;
        $claimedID = Session::getSession(wilokeListingToolsRepository()->get('claim:sessionClaimID'), true);
        foreach ($aItems as $aItem) {
            $productID = $aItem['product_id'];
            $planID    = PlanRelationshipModel::getPlanIDByProductID($productID);

            $this->updateStatusOfPaymentViaWooCommerce($orderID, 'completed');
        }

        $objectID = PlanRelationshipModel::getFirstObjectIDByPaymentID($paymentID);
        /*
         * @PaymentStatusController:updateWooCommercePaymentsStatus 5
         * @UserController:setUserPlanOfUserBoughtViaWooCommerce 20
         * @PostController:migrateAllListingsBelongsToWooCommerceToPublish 20
         */
        $aInformation = [
            'status'      => 'succeeded',
            'gateway'     => $this->gateway,
            'orderID'     => $orderID,
            'oOrder'      => $oOrder,
            'userID'      => get_current_user_id(),
            'postID'      => $objectID,
            'planID'      => $planID,
            'paymentID'   => $paymentID,
            'claimID'     => $claimedID,
            'billingType' => WooCommerce::getBillingType($orderID),
            'productID'   => $productID
        ];

        if (!GetWilokeSubmission::isNonRecurringPayment($aInformation['billingType'])) {
            $isChangePlan = Session::getSession(wilokeListingToolsRepository()->get('payment:onChangedPlan'), 'yes');

            $totalSubscriptions = WooCommerce::countSubscriptionsByOrderID($orderID);
            if ($totalSubscriptions == 0) {
                $trialLength = \WC_Subscriptions_Product::get_trial_length($productID);
                if (!empty($trialLength)) {
                    $aInformation['isTrial'] = true;
                }
            }

            /*
             * @hooked: PlanRelationshipController@switchListingsBelongsToOldPaymentIDToNewPaymentID 1
             * @hooked UserPlanController@changeUserPlan 10
             */
            if ($isChangePlan) {
                $aInformation['onChangedPlan']      = 'yes';
                $aInformation['oldPlanID']          = Session::getSession(wilokeListingToolsRepository()->get('payment:oldPlanID'));
                $aInformation['listingType']        = Session::getSession(wilokeListingToolsRepository()->get('payment:listingType'));
                $aInformation['oldPaymentID']       = Session::getSession(wilokeListingToolsRepository()->get('payment:oldPaymentID'));
                $aInformation['nextBillingDateGMT'] = PaymentMetaModel::getNextBillingDateGMT($paymentID);

                /*
                 * PlanRelationshipController@switchListingsBelongsToOldPaymentIDToNewPaymentID
                 */
                do_action('wiloke-listing-tools/on-changed-user-plan', $aInformation);
            }
        }
        /*
         * @hooked: PostController@migrateAllListingsBelongsToWooCommerceToPublish 20
         */
        do_action('wiloke-listing-tools/woocommerce/after-order-succeeded', $aInformation);
        do_action('wiloke-listing-tools/woocommerce/after-order-succeeded/'.$category, $aInformation);
    }

    public function preparePayment($planID, $productID, $orderID)
    {
        if (empty($planID) || empty($productID) || empty($orderID)) {
            Message::error(esc_html__('Product ID, Order ID and Plan ID are required', 'wiloke-listing-tools'));
        }

        $this->planID    = $planID;
        $this->orderID   = $orderID;
        $this->productID = $productID;

        $isNonRecurring = Session::getSession(wilokeListingToolsRepository()->get('payment:focusNonRecurringPayment'),
            true);

        if (!$isNonRecurring) {
            $isNonRecurring = GetWilokeSubmission::isNonRecurringPayment();
        }

        $this->oReceipt = new Receipt([
            'planID'                => $this->planID,
            'productID'             => $this->productID,
            'userID'                => get_current_user_id(),
            'couponCode'            => '',
            'isNonRecurringPayment' => $isNonRecurring,
            'aRequested'            => ''
        ]);
        $this->oReceipt->setupPlan();

        if (GetWilokeSubmission::isNonRecurringPayment()) {
            $instWooPayment = new WooCommerceNonRecurringPayment();
        } else {
            $instWooPayment = new WooCommerceRecurringPayment();
        }

        $instWooPayment->setOrderID($this->orderID);
        $instWooPayment->proceedPayment($this->oReceipt);
    }

    public static function setupReceiptDirectly($aData)
    {
        $aDefault = [
            'userID'                => get_current_user_id(),
            'productID'             => $aData['productID'],
            'isNonRecurringPayment' => $aData['billingType'] == wilokeListingToolsRepository()
                    ->get('payment:billingTypes', true)
                    ->sub('nonrecurring'),
        ];

        $oReceipt = new Receipt(array_merge($aDefault, $aData));
        $oReceipt->setupPriceDirectly();

        $oPayPalMethod = new WooCommerceNonRecurringPayment();
        $oPayPalMethod->setOrderID($aData['orderID']);

        return $oPayPalMethod->proceedPayment($oReceipt);
    }

    /**
     * Inserting Order ID to Wiloke Payment Table.
     *
     * @since 1.0
     */
    public function newOrderCreated($orderID)
    {
        $oOrder = new \WC_Order($orderID);
        $aItems = $oOrder->get_items();

        foreach ($aItems as $aItem) {
            $productID = $aItem['product_id'];
            $planID    = PlanRelationshipModel::getPlanIDByProductID($productID);

            if (!empty($planID)) {
                $this->preparePayment($planID, $productID, $orderID);
            } else {
                do_action('wiloke-listing-tools/woocommerce/order-created', $aItem, $orderID);
            }
        }
    }
}
