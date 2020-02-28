<?php
namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Submission;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Helpers\WooCommerce as WooCommerceHelpers;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\PlanRelationshipModel;

class PostController extends Controller
{
    use SetPostDuration;
    private $expiredAt = '';
    private static $aPostTypeWillBePublished = ['event'];
    private static $expirationKey = 'post_expiry';
    private static $almostExpiredKey = 'post_almost_expiry';
    private static $durationKey = 'duration';
    public static $deleteUnpaidListing = 'delete_unpaid_listing';
    public static $fNotificationAlmostDeletePost = 'f_notice_delete_unpaid_listing';
    public static $sNotificationAlmostDeletePost = 's_notice_delete_unpaid_listing';
    public static $tNotificationAlmostDeletePost = 't_notice_delete_unpaid_listing';
    private static $updatedExpirationTime = false;
    private static $directlyUpdatedExpirationDate = false;
    private static $test = 1;

    public function __construct()
    {
        add_action('added_post_meta', [$this, 'updateExpirationViaAdmin'], 10, 4);
        add_action('updated_post_meta', [$this, 'updateExpirationViaAdmin'], 10, 4);
        add_action('updated_post_meta', [$this, 'autoSetExpirationViaAdmin'], 10, 4);
        add_action('added_post_meta', [$this, 'autoSetExpirationViaAdmin'], 10, 4);

        add_action('save_post', [$this, 'updatePostExpiration'], 999, 3);

        add_action('wiloke-listing-tools/payment-failed', [$this, 'moveAllPostsToUnPaid'], 5);
        add_action('wiloke-listing-tools/payment-pending', [$this, 'moveAllPostsToUnPaid'], 5);
        add_action('wiloke-listing-tools/payment-refunded', [$this, 'moveAllPostsToTrash'], 10);
        add_action('wiloke-listing-tools/payment-cancelled', [$this, 'moveAllPostsToUnPaid'], 10);
        add_action('wiloke-listing-tools/payment-suspended', [$this, 'moveAllPostsToExpiry'], 30);
        add_action('wiloke-listing-tools/payment-succeeded/listing_plan', [$this, 'migrateToPublish'], 30);
        add_action('wiloke-listing-tools/payment-succeeded/event_plan', [$this, 'migrateToPublish'], 30);
        add_action('wiloke-listing-tools/payment-renewed', [$this, 'migrateToPublish'], 20);
        add_action('wiloke-listing-tools/subscription-reactived', [$this, 'migrateToPublish'], 5);
        add_action('wiloke-listing-tools/payment-failed', [$this, 'rollupListingToPreviousStatus']);

        /*
         * It's different from wiloke-listing-tools/payment-return-cancel-page and wiloke-listing-tools/payment-cancelled
         * wiloke-listing-tools/payment-cancelled means Subscription was cancelled
         * wiloke-listing-tools/payment-return-cancel-page means Custom click Cancel button and do not purchase plan
         */
        add_action('wiloke-listing-tools/payment-return-cancel-page', [$this, 'rollupListingToPreviousStatus']);

        add_action('wiloke-listing-tools/woocommerce/after-order-succeeded', [
            $this,
            'migrateAllListingsBelongsToWooCommerceToPublish'
        ], 20);

        add_filter('wilcity/ajax/post-comment/post', [$this, 'insertComment']);

        add_action('wp_ajax_wilcity_hide_listing', [$this, 'hideListing']);
        add_action('wp_ajax_wilcity_republish_listing', [$this, 'rePublishPost']);
        add_action('wp_ajax_wilcity_delete_listing', [$this, 'deleteListing']);

        add_action('wiloke-listing-tools/on-changed-user-plan', [$this, 'updatePostToNewPlan'], 20, 1);
        add_action('wiloke/claim/approved', [$this, 'claimApproved'], 10, 3);
        add_action('wp_ajax_wilcity_fetch_posts', [$this, 'fetchPosts']);
        add_action('wp_ajax_nopriv_wilcity_fetch_posts', [$this, 'fetchPosts']);

        // Post Expired
        add_action(self::$expirationKey, [$this, 'postExpired']);

        // Delete Expired Event
        add_action(self::$deleteUnpaidListing, [$this, 'focusDeletePost']);

        // WooCommerce Subscription
        add_action('woocommerce_subscription_payment_complete', [$this, 'afterSubscriptionPaymentComplete']);
        add_action('woocommerce_checkout_subscription_created', [
            $this,
            'upgradeAllListingsBelongsToOldOldPlanToNewPlan'
        ]);
        add_action('woocommerce_subscription_date_updated', [$this, 'afterUpdatedSubscriptionNextPayment'], 10, 3);
        add_action('woocommerce_subscription_status_updated', [
            $this,
            'moveAllListingToDraftAfterSubscriptionChangedStatus'
        ], 10, 3);
        add_action('woocommerce_subscription_status_updated', [
            $this,
            'moveAllListingToPendingOrPublishStatus'
        ], 10, 3);

        add_action('after_delete_post', [$this, 'clearAllSchedules']);
//        add_action('wiloke/submitted-listing', array($this, 'maybeListingChangedPlan'), 10, 4);
    }

    /*
     * The Expired Listing = Real Expired Time + Move listing to expired store after x days (You can find this setting under Wiloke Submission)
     *
     * @since 1.1.7.3
     * @return int
     */
    private static function getExpiredListingTime($timestamp)
    {
        $plusExpiredTime = GetWilokeSubmission::getField('move_listing_to_expired_store_after');
        if (empty($plusExpiredTime)) {
            return $timestamp;
        }

        $oDT = new \DateTime();
        $oDT->setTimestamp($timestamp);
        $oDT->modify('+'.$plusExpiredTime.' day');

        return strtotime($oDT->format('Y-m-d H:i:s'));
    }

    /*
     * Get Almost expired Listing Time. We will set a schedule and send an email to customer
     *
     * @since 1.1.7.3
     * return int
     */
    private static function getAlmostExpiredDate($timestamp, $beforeXday = 1)
    {
        $oDT = new \DateTime();
        $oDT->setTimestamp($timestamp);
        $oDT->modify('-'.$beforeXday.' day');

        return strtotime($oDT->format('Y-m-d H:i:s'));
    }

    public function postExpired($postID)
    {
        wp_update_post([
            'ID'          => $postID,
            'post_status' => 'expired'
        ]);
    }

    public function focusDeletePost($postID)
    {
        if (GetWilokeSubmission::getField('delete_listing_conditional')) {
            return false;
        }

        if (get_post_status($postID) != 'expired' && get_post_status($postID) != 'unpaid') {
            return false;
        }

        wp_delete_post($postID, true);
    }

    public function fetchPosts()
    {
        $aPostIDs = GetSettings::getPostMeta($_GET['parentID'], 'my_posts');

        if (empty($aPostIDs)) {
            wp_send_json_error(['isLoaded' => 'yes']);
        }

        $aArgs = [
            'post_type'      => 'post',
            'posts_per_page' => 10,
            'post_status'    => 'publish',
            'post__in'       => is_array($aPostIDs) ? array_map('intval', $aPostIDs) : array_map('intval',
                explode(',', $aPostIDs))
        ];

        if (isset($_GET['paged']) && !empty($_GET['paged'])) {
            $aArgs['paged'] = $_GET['paged'];
        }

        if (isset($_GET['postNotIn']) && !empty($_GET['postNotIn'])) {
            if (isset($aArgs['post__in'])) {
                $post__not_in      = $_GET['postNotIn'];
                $post__not_in      = is_array($post__not_in) ? array_map('intval', $post__not_in) : array_map('intval',
                    explode(',', $post__not_in));
                $aArgs['post__in'] = array_diff($aArgs['post__in'], $post__not_in);
            }
        }

        if (isset($aArgs['post__in']) && empty($aArgs['post__in'])) {
            wp_send_json_error(['isLoaded' => 'yes']);
        }

        $query    = new \WP_Query($aArgs);
        $aPostIds = [];
        if ($query->have_posts()) {
            ob_start();
            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <div class="col-sm-6">
                    <?php wilcity_render_grid_post($query->post); ?>
                </div>
                <?php
                $aPostIds[] = $query->post->ID;
            }
            wp_reset_postdata();
            $content = ob_get_contents();
            ob_end_clean();
            wp_send_json_success([
                'args'     => $aArgs,
                'content'  => $content,
                'maxPages' => $query->post->max_num_pages,
                'maxPosts' => $query->post->found_posts,
                'postIDs'  => $aPostIds
            ]);
        } else {
            if (isset($_GET['postNotIn']) && !empty($_GET['postNotIn'])) {
                wp_send_json_error(['isLoaded' => 'yes']);
            }
            wp_send_json_error([
                'msg'      => esc_html__('There are no posts', 'wiloke-listing-tools'),
                'maxPages' => 0,
                'maxPosts' => 0
            ]);
        }
    }

    public function claimApproved($claimerID, $listingID, $claimID)
    {
        $planID = GetSettings::getPostMeta($claimID, 'claim_plan_id');

        if (!empty($planID)) {
            SetSettings::setPostMeta($listingID, 'belongs_to', $planID);
            $aPlanSettings = GetSettings::getPlanSettings($planID);
            if (!empty($aPlanSettings['regular_period'])) {
                $duration = strtotime('+'.$aPlanSettings['regular_period'].' day');
                SetSettings::setPostMeta($listingID, self::$expirationKey, $duration);
                $this->focusPostExpiration($listingID);
            }
        }
    }

    /**
     * Re-update Listing Order
     *
     * @since 1.2.0
     */
    private function reUpdateListingOrder($listingID, $newPlanID, $oldPlanID)
    {
        $listingOrder     = get_post_field('menu_order', $listingID);
        $aNewPlanSettings = GetSettings::getPlanSettings($newPlanID);
        $aOldPlanSettings = GetSettings::getPlanSettings($oldPlanID);

        if (isset($aOldPlanSettings['menu_order']) && !empty($aOldPlanSettings['menu_order'])) {
            $listingOrder = abs($listingOrder) - abs($aOldPlanSettings['menu_order']);
            $listingOrder = $listingOrder > 0 ? $listingOrder : 0;
        }

        if (isset($aNewPlanSettings['menu_order']) && !empty($aNewPlanSettings['menu_order'])) {
            $listingOrder = abs($listingOrder) + abs($aNewPlanSettings['menu_order']);
            $listingOrder = $listingOrder > 0 ? $listingOrder : 0;
        }

        wp_update_post([
            'ID'         => $listingID,
            'menu_order' => $listingOrder
        ]);
    }

    /*
     * Updating Listing Information Like Expiry Date, Belongs To after Plan was Changed
     *
     * @since 1.2.0
     */
    private function onChangePlan($aInfo, $paymentID)
    {
        global $wpdb;
        $postMetaTbl = $wpdb->postmeta;
        $postTbl     = $wpdb->posts;

        $aRawPostMetaIDs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT $postMetaTbl.meta_id, $postTbl.ID FROM $postMetaTbl LEFT JOIN $postTbl ON($postMetaTbl.post_id=$postTbl.ID) WHERE $postTbl.post_author=%d AND $postMetaTbl.meta_key=%s AND meta_value=%d AND post_status IN ('publish', 'pending')",
                $aInfo['userID'], General::generateMetaKey('belongs_to'), $aInfo['oldPlanID']
            ),
            ARRAY_A
        );

        if (empty($aRawPostMetaIDs)) {
            return false;
        }

        $aPostIDs = $aPostMetaIDs = [];
        foreach ($aRawPostMetaIDs as $aData) {
            $aPostMetaIDs[] = abs($aData['meta_id']);
            $aPostIDs[]     = [
                'objectID' => $aData['ID']
            ];
        }

        $wpdb->query($wpdb->prepare(
            "UPDATE $postMetaTbl SET $postMetaTbl.meta_value = %d WHERE $postMetaTbl.meta_key=%s AND $postMetaTbl.meta_id IN (".implode(',',
                $aPostMetaIDs).")",
            abs($aInfo['planID']), General::generateMetaKey('belongs_to')
        ));

        $this->expiredAt = PaymentMetaModel::getNextBillingDateGMT($paymentID);
        if (!empty($this->expiredAt)) {
            $this->inCaseToPublish($aPostIDs, [
                'nextBillingDateGMT' => $this->expiredAt,
                'oldPlanID'          => $aInfo['oldPlanID'],
                'planID'             => $aInfo['planID']
            ], __METHOD__);
        }
    }

//    public function onChangePlanViaRecurringPayPal($aInfo)
//    {
//        if ( !isset($aInfo['onChangedPlan']) || ($aInfo['onChangedPlan'] != 'yes') || GetWilokeSubmission::isNonRecurringPayment($aInfo['billingType'])) {
//            return false;
//        }
//        $this->onChangePlan($aInfo, $aInfo['paymentID']);
//    }

    public function updatePostToNewPlan($aInfo)
    {
        if (!isset($aInfo['userID']) || !isset($aInfo['planID']) || !isset($aInfo['oldPlanID']) || empty($aInfo['planID']) || empty($aInfo['userID']) || empty($aInfo['oldPlanID'])) {
            return false;
        }

        $this->onChangePlan($aInfo, $aInfo['paymentID']);
    }

    public function deleteListing()
    {
        $this->middleware(['isPostAuthor'], [
            'postID'        => $_POST['postID'],
            'passedIfAdmin' => true
        ]);
        $postType   = get_post_type($_POST['postID']);
        $postAuthor = get_post_field('post_author', $_POST['postID']);
        $planID     = GetSettings::getListingBelongsToPlan($_POST['postID']);

        wp_delete_post($_POST['postID'], true);
        do_action('wilcity/deleted/listing', $_POST['postID'], $postType, $postAuthor, $planID);

        wp_send_json_success([
            'msg' => esc_html__('Congrats! The listing has been deleted successfully', 'wiloke-listing-tools')
        ]);
    }

    public function rePublishPost()
    {
        $this->middleware(['isPostAuthor', 'isTemporaryHiddenPost'], [
            'postID'        => $_POST['postID'],
            'passedIfAdmin' => true
        ]);

        wp_update_post([
            'ID'          => $_POST['postID'],
            'post_status' => 'publish'
        ]);

        wp_send_json_success([
            'msg' => esc_html__('Congrats! The listing has been re-published successfully', 'wiloke-listing-tools')
        ]);
    }

    public function hideListing()
    {
        $this->middleware(['isPostAuthor', 'isPublishedPost'], [
            'postID'        => $_POST['postID'],
            'passedIfAdmin' => true
        ]);

        wp_update_post([
            'ID'          => $_POST['postID'],
            'post_status' => 'temporary_close'
        ]);

        wp_send_json_success([
            'msg' => esc_html__('Congrats! The listing has been hidden successfully', 'wiloke-listing-tools')
        ]);
    }

    public function insertComment($aData)
    {
        $commentID = wp_insert_comment([
            'user_id'         => get_current_user_id(),
            'comment_content' => $aData['content']
        ]);

        global $oReview, $wiloke;
        $wiloke->aThemeOptions           = \Wiloke::getThemeOptions();
        $wiloke->aConfigs['translation'] = wilcityGetConfig('translation');

        $aReview                 = get_comment($commentID, ARRAY_A);
        $aReview['ID']           = $aReview['comment_ID'];
        $aReview['post_content'] = $aReview['comment_content'];
        $oReview                 = (object)$aReview;

        ob_start();
        get_template_part('reviews/item');
        $html = ob_get_contents();
        ob_end_clean();

        wp_send_json_success([
            'html'      => $html,
            'commentID' => $commentID
        ]);
    }

    private function renewListingExpired($listingID)
    {
        $durationTimestampUTC = GetSettings::getPostMeta($listingID, 'durationTimestampUTC');
        $isNextBillingDate    = false;
        if (!empty($durationTimestampUTC)) {
            $timestampUTCToLocalTime = Time::convertUTCTimestampToLocalTimestamp($durationTimestampUTC);
            $duration                = self::getExpiredListingTime($timestampUTCToLocalTime);
            $isNextBillingDate       = true;
        } else {
            $duration = GetSettings::getPostMeta($listingID, 'duration');
        }

        self::setExpiration($listingID, $duration, $isNextBillingDate);
    }

    /*
     * Set schedules for a provided listing ID. We will send email before listing is expired and when the listing is expired
     *
     * @since 1.1.7.3
     */
    protected static function setScheduleExpiration($postID, $expirationTimestamp)
    {
        self::clearScheduled($postID);
        $postID = abs($postID);
        $expirationTimestamp = is_numeric($expirationTimestamp) ? $expirationTimestamp : strtotime($expirationTimestamp);
        $now                 = current_time('timestamp');

        $beforeOneWeek = self::getAlmostExpiredDate($expirationTimestamp, 4);
        if (Time::compareTwoTimes($beforeOneWeek, $now, 7)) {
            wp_schedule_single_event($beforeOneWeek, self::$almostExpiredKey, [$postID]);
        }

        $beforeThreeDays = self::getAlmostExpiredDate($expirationTimestamp, 3);
        if (Time::compareTwoTimes($beforeThreeDays, $now, 6)) {
            wp_schedule_single_event($beforeThreeDays, self::$almostExpiredKey, [$postID]);
        }

        $beforeOneDay = self::getAlmostExpiredDate($expirationTimestamp, 2);
        if ($beforeOneDay > $now) {
            wp_schedule_single_event($beforeOneDay, self::$almostExpiredKey, [$postID]);
        }

        wp_schedule_single_event($expirationTimestamp, self::$expirationKey, [$postID]);
    }

    private static function clearAutoDeleteUnpaidListing($postID)
    {
        wp_clear_scheduled_hook(self::$deleteUnpaidListing, [$postID]);
        wp_clear_scheduled_hook(self::$fNotificationAlmostDeletePost, [$postID]);
        wp_clear_scheduled_hook(self::$sNotificationAlmostDeletePost, [$postID]);
        wp_clear_scheduled_hook(self::$tNotificationAlmostDeletePost, [$postID]);

        wp_clear_scheduled_hook(self::$deleteUnpaidListing, ["$postID"]);
        wp_clear_scheduled_hook(self::$fNotificationAlmostDeletePost, ["$postID"]);
        wp_clear_scheduled_hook(self::$sNotificationAlmostDeletePost, ["$postID"]);
        wp_clear_scheduled_hook(self::$tNotificationAlmostDeletePost, ["$postID"]);

        SetSettings::deletePostMeta($postID, 'fwarning_delete_listing');
        SetSettings::deletePostMeta($postID, 'swarning_delete_listing');
        SetSettings::deletePostMeta($postID, 'twarning_delete_listing');
    }

    public function clearAllSchedules($postID)
    {
        self::clearAutoDeleteUnpaidListing($postID);
        self::clearScheduled($postID);
    }

    /*
     * Updating Expiry Schedule if Administrator changed Expiration value via back-end
     *
     *
     * @since 1.0
     */
    public function updateExpirationViaAdmin($metaID, $postID, $metaKey, $expirationTimestamp)
    {
        self::$directlyUpdatedExpirationDate = false;
        if (!General::isAdmin() || get_post_status($postID) != 'publish' || $metaKey != 'wilcity_post_expiry') {
            return false;
        }
        if (empty($expirationTimestamp)) {
            return true;
        }
        
        self::$directlyUpdatedExpirationDate = true;
        self::setScheduleExpiration($postID, $expirationTimestamp);
    }

    /**
     * Auto Add Listing Expiry if customer set Listing belongs to an external plan
     *
     * @since 1.0
     */
    public function autoSetExpirationViaAdmin($metaID, $postID, $metaKey, $planID)
    {
        if ($metaKey != 'wilcity_belongs_to' || !General::isAdmin() || empty($planID)) {
            return false;
        }

        if (self::$directlyUpdatedExpirationDate || get_post_status($postID) != 'publish') {
            return false;
        }

        $status = apply_filters('wilcity/wiloke-listing-tools/filter/auto-set-expiration-via-admin', true, $postID,
            $planID);

        if (!$status) {
            return false;
        }

        $aPlanSettings = GetSettings::getPlanSettings($planID);
        if (isset($aPlanSettings['regular_period']) && !empty($aPlanSettings['regular_period'])) {
            SetSettings::setPostMeta($postID, self::$expirationKey,
                strtotime('+'.$aPlanSettings['regular_period'].' day'));
        }
    }

    public static function setExpiration($postID, $duration, $isNextBillingDateVal = false)
    {
        if (empty($duration)) {
            // forever
            SetSettings::deletePostMeta($postID, self::$expirationKey);

            return true;
        }
        $expirationTimestamp = !$isNextBillingDateVal ? strtotime('+'.$duration.' day') : $duration;

        // We need that for triggering other actions. Remember that if wp_update return 0, WP will understand that there is no update
        SetSettings::setPostMeta($postID, self::$expirationKey, $expirationTimestamp);
        self::setScheduleExpiration($postID, $expirationTimestamp);
    }

    /*
     * This function is very important. It will setup expiration of Listing after it was updated
     *
     * @since 1.2.0
     *
     */
    public function updatePostExpiration($postID, $post, $isUpdate)
    {
        # Set Auto Delete If it's Unpaid or Expired Listing
        $postStatus = get_post_status($postID);
        if ($postStatus == 'unpaid' || $postStatus == 'expired') {
            self::setAutoDeleteUnpaidListing($postID);
        } else {
            # Clear Auto Delete If it's Pending or Publish status
            switch ($postStatus) {
                case 'pending':
                    self::clearAutoDeleteUnpaidListing($postID);
                    self::clearScheduled($postID);
                    break;
                case 'publish':
                    self::clearAutoDeleteUnpaidListing($postID);

                    if (self::$directlyUpdatedExpirationDate) {
                        return false;
                    }

                    $duration = GetSettings::getPostMeta($postID, 'duration');
                    if (empty($duration)) {
                        $durationGMT = GetSettings::getPostMeta($postID, 'durationTimestampUTC');
                        $now         = current_time('timestamp', 1);
                        // Fixing issue Listing Auto Move to Expired after Customer Edited.
                        // This is an issue caused with Stripe recurring payment
                        if ($now > $durationGMT) {
                            $durationGMT = '';

                            $listingExpiry = GetSettings::getPostMeta($postID, self::$expirationKey);
                            if (!empty($listingExpiry) && $listingExpiry > $now) {
                                $durationGMT = $listingExpiry;
                            } else {
                                $planID = GetSettings::getListingBelongsToPlan($postID);
                                if (!empty($planID)) {
                                    $aPlanSettings = GetSettings::getPlanSettings($planID);
                                    date_default_timezone_set('UTC');
                                    $durationGMT = strtotime('+'.$aPlanSettings['regular_period'].' day');
                                }
                            }
                        }

                        if (!empty($durationGMT)) {
                            self::setExpiration($postID, $durationGMT, true);
                        }
                    } else {
                        self::setExpiration($postID, $duration, false);
                    }
                    break;
            }
        }
    }

    /*
     * Update Listing Duration If It Is Changed Plan and it's approved immediately after changing
     *
     * @since 1.2.0
     */
    public function maybeListingChangedPlan($listingAuthor, $listingID, $autoApproved, $isChangedPlan)
    {
        if (!$isChangedPlan) {
            return false;
        }

        if (get_post_status($listingID) !== 'publish') {
            return false;
        }

        $this->renewListingExpired($listingID);
    }

    private static function clearScheduled($postID)
    {
        wp_clear_scheduled_hook(self::$expirationKey, ["$postID"]);
        wp_clear_scheduled_hook(self::$expirationKey, [$postID]);
        wp_clear_scheduled_hook(self::$almostExpiredKey, [$postID]);
        wp_clear_scheduled_hook(self::$almostExpiredKey, ["$postID"]);
    }

    private static function setAutoDeleteUnpaidListing($postID)
    {
        self::clearAutoDeleteUnpaidListing($postID);
        $postID = abs($postID);
        $duration = GetWilokeSubmission::getField('delete_listing_conditional');
        if (empty($duration)) {
            return false;
        }

        $oneDayToTimeStamp = 3600 * 24;
        $now               = current_time('timestamp');
        $deleteAt          = abs($duration) * $oneDayToTimeStamp;
        $deleteAt          = $now + $deleteAt;
        wp_schedule_single_event($deleteAt, self::$deleteUnpaidListing, [$postID]);

        $fDuration = $duration - 1;
        if ($fDuration > 0) {
            $fDuration     = abs($fDuration) * $oneDayToTimeStamp;
            $fNotification = $now + $fDuration;
            wp_schedule_single_event($fNotification, self::$fNotificationAlmostDeletePost, [$postID]);
            SetSettings::setPostMeta($postID, 'fwarning_delete_listing', $fNotification);

            $sDuration = $duration - 2;
            if ($sDuration > 0) {
                $sDuration     = abs($sDuration) * $oneDayToTimeStamp;
                $sNotification = $now + $sDuration;
                wp_schedule_single_event($sNotification, self::$sNotificationAlmostDeletePost, [$postID]);
                SetSettings::setPostMeta($postID, 'swarning_delete_listing', $sNotification);
            }

            $tDuration = $duration - 3;
            if ($tDuration > 0) {
                $tDuration     = abs($tDuration) * $oneDayToTimeStamp;
                $tNotification = $now + $tDuration;
                wp_schedule_single_event($tNotification, self::$tNotificationAlmostDeletePost, [$postID]);
                SetSettings::setPostMeta($postID, 'twarning_delete_listing', $tNotification);
            }
        }
    }

    public function focusPostExpiration($postID)
    {
        $duration = GetSettings::getPostMeta($postID, self::$expirationKey);
        if (!empty($duration)) {
            self::setScheduleExpiration($postID, $duration);
        }
    }

    public static function changePostsStatusByPaymentID($paymentID, $status)
    {
        $aPostIDs = PlanRelationshipModel::getObjectIDsByPaymentID($paymentID);

        if (empty($aPostIDs)) {
            return false;
        }

        foreach ($aPostIDs as $aPost) {
            SetSettings::setPostMeta($aPost['objectID'], 'old_status', get_post_status($aPost['objectID']));
            wp_update_post(
                [
                    'ID'          => $aPost['objectID'],
                    'post_status' => $status
                ]
            );
        }
    }

    protected static function migratePostAfterRenewPayment($paymentID, $nextBillingDateGMT)
    {
        $aPostIDs = PlanRelationshipModel::getObjectIDsByPaymentID($paymentID);

        if (empty($aPostIDs)) {
            return false;
        }

        foreach ($aPostIDs as $aPost) {
            $oldStatus = GetSettings::getPostMeta($aPost['objectID'], 'oldPostStatus');
            SetSettings::deletePostMeta($aPost['objectID'], 'oldPostStatus');
            SetSettings::setPostMeta($aPost['objectID'], 'durationTimestampUTC', $nextBillingDateGMT);

            if (empty($oldStatus)) {
                $oldStatus = get_post_status($aPost['objectID']);
            }

            if ($oldStatus == 'publish' || $oldStatus == 'expired') {
                $status = 'publish';
            } else {
                if ($oldStatus != 'pending') {
                    $approvalMethod = GetWilokeSubmission::getField('approved_method');
                    $status         = $approvalMethod == 'manual_review' ? 'pending' : 'publish';
                } else {
                    $status = 'pending';
                }
            }

            wp_update_post(
                [
                    'ID'          => $aPost['objectID'],
                    'post_status' => $status
                ]
            );
        }
    }

    public static function migratePostsToExpiredStatus($paymentID)
    {
        self::changePostsStatusByPaymentID($paymentID, 'expired');
    }

    public static function migratePostsToDraftStatus($paymentID)
    {
        self::changePostsStatusByPaymentID($paymentID, 'draft');
    }

    public static function migratePostsToPublishStatus($paymentID)
    {
        self::changePostsStatusByPaymentID($paymentID, 'publish');
    }

    public static function migratePostsToPendingStatus($paymentID)
    {
        self::changePostsStatusByPaymentID($paymentID, 'pending');
    }

    protected static function detectNewPostStatus($postID)
    {
        $postStatus = get_post_status($postID);
        if (Submission::listingStatusWillPublishImmediately($postStatus)) {
            return 'publish';
        } else {
            $oldPostStatus = GetSettings::getPostMeta($postID, 'oldPostStatus');

            return Submission::listingStatusWillPublishImmediately($oldPostStatus) ? 'publish' : Submission::detectPostStatus();
        }
    }

    /*
     * Upgrading all listings belong to previous plan to new plan (Change Plan session)
     *
     * @since 1.2.0
     */
    public function upgradeAllListingsBelongsToOldOldPlanToNewPlan(\WC_Subscription $that)
    {
        $orderID       = $that->get_parent_id();
        $lastPaymentID = PaymentModel::getPaymentIDsByWooOrderID($orderID, true);

        if (!empty($lastPaymentID)) {
            $oldOrderID = PaymentMetaModel::get($lastPaymentID, 'oldOrderID');
            if (!empty($oldOrderID)) {
                $aOldPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($oldOrderID);
                PaymentMetaModel::delete($lastPaymentID, 'oldOrderID');
                if (empty($aOldPaymentIDs)) {
                    return false;
                }
            } else {
                $oldPaymentID = PaymentMetaModel::get($lastPaymentID, 'oldPaymentID');
                if (empty($aOldObjectIDs)) {
                    return false;
                }

                $aOldPaymentIDs = [
                    [
                        'ID' => $oldPaymentID
                    ]
                ];
            }

            $this->expiredAt = strtotime($that->get_date('next_payment'));
            $planID          = PaymentModel::getField('planID', $lastPaymentID);

            foreach ($aOldPaymentIDs as $aOldPaymentID) {
                $aOldObjectIDs = PlanRelationshipModel::getObjectIDsByPaymentID($aOldPaymentID['ID']);
                if (empty($aOldObjectIDs)) {
                    continue;
                }
                $oldPlanID = PaymentModel::getField('planID', $aOldPaymentID['ID']);
                $this->inCaseToPublish($aOldObjectIDs, [
                    'planID'  => $planID,
                    'oldPlan' => $oldPlanID,
                    'orderID' => $orderID
                ], __METHOD__);
            }
        }
    }

    /*
     * Updating Expiry date for listing and post status after the payment has been completed successfully.
     * Note that We only process this task if it's not subscription
     *
     * We are using woocommerce_subscription_payment_complete https://docs.woocommerce.com/document/subscriptions/develop/action-reference/
     * This hook runs after wiloke-listing-tools/woocommerce/after-order-succeeded
     *
     * @since 1.1.7.3
     */
    public function afterSubscriptionPaymentComplete(\WC_Subscription $that)
    {
        $nextPayment = $that->get_date('next_payment');

        $aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($that->get_parent_id());
        if (empty($aPaymentIDs)) {
            return false;
        }

        $this->expiredAt = strtotime($nextPayment);

        foreach ($aPaymentIDs as $aPaymentID) {
            PaymentModel::updatePaymentStatus('succeeded', $aPaymentID['ID']);
            $aObjectIDs = PlanRelationshipModel::getObjectIDsByPaymentID($aPaymentID['ID']);
            if (empty($aObjectIDs)) {
                continue;
            }

            $planID = PaymentModel::getField('planID', $aPaymentID['ID']);
            $this->inCaseToPublish($aObjectIDs, [
                'orderID' => $that->get_parent_id(),
                'planID'  => $planID
            ], __METHOD__);
        }
    }

    /*
     * After the subscription changed status https://docs.woocommerce.com/document/subscriptions/develop/action-reference/
     * We will change listings status that belong to order
     *
     * @since 1.1.7.3
     */
    public function moveAllListingToDraftAfterSubscriptionChangedStatus(\WC_Subscription $that, $newStatus, $oldStatus)
    {
        if ($oldStatus == $newStatus || $oldStatus !== 'active') {
            return false;
        }

        switch ($newStatus) {
            case 'pending-cancel':
            case 'cancelled':
            case 'on-hold':
                $orderID     = $that->get_parent_id();
                $aSessionIDs = PaymentModel::getPaymentIDsByWooOrderID($orderID);

                if (empty($aSessionIDs)) {
                    return false;
                }

                foreach ($aSessionIDs as $aSession) {
                    $aPaymentIDs[] = $aSession['ID'];
                    self::migratePostsToExpiredStatus($aSession['ID']);
                }
                break;
        }
    }

    /*
     * After the subscription re-activated https://docs.woocommerce.com/document/subscriptions/develop/action-reference/
     * We will change listings status to Pending / Publish status
     *
     * @since 1.1.7.3
     */
    public function moveAllListingToPendingOrPublishStatus(\WC_Subscription $that, $newStatus, $oldStatus)
    {
        if ($newStatus != 'active' || $oldStatus == $newStatus || !in_array($oldStatus, [
                'on-hold',
                'pending-cancel',
                'expired',
                'pending'
            ])
        ) {
            return false;
        }

        $aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($that->get_parent_id());
        if (empty($aPaymentIDs)) {
            return false;
        }

        $nextBillingDateGMT = $that->get_date('next_payment');
        $nextBillingDateGMT = strtotime($nextBillingDateGMT);
        $nextBillingDateGMT = self::getExpiredListingTime($nextBillingDateGMT);

        foreach ($aPaymentIDs as $aPaymentID) {
            self::migratePostAfterRenewPayment($aPaymentID['ID'], $nextBillingDateGMT);
        }
    }

    /*
     *  After a Subscription is changed its status, We will change Listings that belongs to this order as well
     *
     * @var dateTime: It's next billing date. It's not timestamp, It's date time with human friendly format.
     * @since 1.1.7.3
     */
    public function afterUpdatedSubscriptionNextPayment(\WC_Subscription $that, $dateType, $dateTime)
    {
        if ($dateType == 'next_payment') {
            $nextBillingDateGMT = strtotime($dateTime);
            $nextBillingDateGMT = self::getExpiredListingTime($nextBillingDateGMT);

            $aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($that->get_parent_id());
            if (empty($aPaymentIDs)) {
                return false;
            }

            foreach ($aPaymentIDs as $aPaymentID) {
                self::migratePostAfterRenewPayment($aPaymentID['ID'], $nextBillingDateGMT);
            }
        }
    }

    /*
     * Updating Expiry date for listing and post status after the payment has been completed successfully.
     * Note that We only process this task if it's not subscription
     *
     * @var PostController $callFromWhere it's for debug
     * @var PostController $aData Required: contains orderID, planID Maybe: oldPlanID
     * @since 1.0
     */
    protected function inCaseToPublish($aObjectIDs, $aData, $callFromWhere = '')
    {
        if (!is_array($aObjectIDs)) {
            return false;
        }

        foreach ($aObjectIDs as $aObjectID) {
            if (!empty($aObjectID['objectID'])) {
                $postStatus    = self::detectNewPostStatus($aObjectID['objectID']);
                $aPlanSettings = GetSettings::getPlanSettings($aData['planID']);

                if (!isset($aData['orderID']) || empty($aData['orderID']) || !WooCommerceHelpers::isSubscription($aData['orderID'])) {
                    $duration = '';
                    if (isset($aData['nextBillingDateGMT']) && !empty($aData['nextBillingDateGMT'])) {
                        $duration      = $aData['nextBillingDateGMT'];
                        $isBillingDate = true;
                    } else {
                        if (isset($aData['isTrial']) && !empty($aData['isTrial'])) {
                            $duration = $aPlanSettings['trial_period'];
                        }

                        if (empty($duration)) {
                            $duration = $aPlanSettings['regular_period'];
                        }
                        $isBillingDate = false;
                    }
                } else {
                    $isBillingDate = true;
                    $duration      = $this->expiredAt;
                }

                if ($isBillingDate) {
                    SetSettings::setPostMeta($aObjectID['objectID'], 'durationTimestampUTC', $duration);
                } else {
                    SetSettings::setPostMeta($aObjectID['objectID'], 'duration', $duration);
                }

                $listingOrder = 0;
                if (isset($aData['objectID'])) {
                    $listingOrder = get_post_field('menu_order', $aData['objectID']);
                    $listingOrder = empty($listingOrder) ? 0 : abs($listingOrder);

                    if (isset($aData['oldPlanID']) && !empty($aData['oldPlanID'])) {
                        $oldPlanID = $aData['oldPlanID'];
                    } else {
                        $oldPlanID = GetSettings::getPostMeta($aObjectID['objectID'], 'oldPlanID');
                        SetSettings::deletePostMeta($aObjectID['objectID'], 'oldPlanID');
                    }

                    if (!empty($oldPlanID)) {
                        $aOldPlanSettings = GetSettings::getPlanSettings($oldPlanID);
                        if (!empty($aOldPlanSettings)) {
                            $oldPlanOrder = isset($aOldPlanSettings['menu_order']) && !empty($aOldPlanSettings['menu_order']) ? abs($aOldPlanSettings['menu_order']) : 0;
                            $listingOrder = $listingOrder - $oldPlanOrder;
                            $listingOrder = $listingOrder > 0 ? $listingOrder : 0;
                        }
                    }
                }

                if (isset($aPlanSettings['menu_order']) && !empty($aPlanSettings['menu_order'])) {
                    $listingOrder = empty($listingOrder) ? abs($aPlanSettings['menu_order']) : $listingOrder + abs($aPlanSettings['menu_order']);
                }

                self::$updatedExpirationTime = true;

                $aPostData = [
                    'ID'          => $aObjectID['objectID'],
                    'post_status' => $postStatus,
                    'menu_order'  => $listingOrder
                ];
                wp_update_post($aPostData);
            }
        }
    }

    /*
     * Changing all listings belong to this plan to publish status
     * This is for non recurring payment only
     *
     * @since 1.2.0
     */
    public function migrateAllListingsBelongsToWooCommerceToPublish($aResponse)
    {
        if (!GetWilokeSubmission::isNonRecurringPayment()) {
            return false;
        }

        $aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($aResponse['orderID']);
        if (empty($aPaymentIDs)) {
            return false;
        }

        foreach ($aPaymentIDs as $aPaymentID) {
            PaymentModel::updatePaymentStatus('succeeded', $aPaymentID['ID']);
            $aObjectIDs = PlanRelationshipModel::getObjectIDsByPaymentID($aPaymentID['ID']);
            if (empty($aObjectIDs)) {
                continue;
            }
            $this->inCaseToPublish($aObjectIDs, $aResponse, __METHOD__);
        }
    }

    public static function migratePostsToPendingOrPublishStatus($paymentID)
    {
        $aPostIDs = PlanRelationshipModel::getObjectIDsByPaymentID($paymentID);

        if (empty($aPostIDs)) {
            return false;
        }

        foreach ($aPostIDs as $aPost) {
            $newStatus = self::detectNewPostStatus($aPost['objectID']);
            wp_update_post(
                [
                    'ID'          => $aPost['objectID'],
                    'post_status' => $newStatus
                ]
            );
        }
    }

    public function moveAllPostsToUnPaid($aData)
    {
        $aObjectIDs = PlanRelationshipModel::getObjectIDsByPaymentID($aData['paymentID']);
        if (empty($aObjectIDs)) {
            return false;
        }
        foreach ($aObjectIDs as $aObjectID) {
            if (!empty($aObjectID['objectID'])) {
                wp_update_post(
                    [
                        'ID'          => $aObjectID['objectID'],
                        'post_status' => 'unpaid'
                    ]
                );
            }
        }
    }

    public function moveAllPostsToTrash($aData)
    {
        $aObjectIDs = PlanRelationshipModel::getObjectIDsByPaymentID($aData['paymentID']);
        if (empty($aObjectIDs)) {
            return false;
        }
        foreach ($aObjectIDs as $aObjectID) {
            if (!empty($aObjectID['objectID'])) {
                wp_update_post(
                    [
                        'ID'          => $aObjectID['objectID'],
                        'post_status' => 'expired'
                    ]
                );
            }
        }
    }

    public function migrateToPublish($aData)
    {
        if (GetWilokeSubmission::getField('approved_method') == 'manual_review' && isset($aData['postID'])) {
            $oldPostStatus = GetSettings::getPostMeta($aData['postID'], 'oldPostStatus');
            if (GetWilokeSubmission::isNonRecurringPayment($aData) && !empty($oldPostStatus)) {
                $this->migrateListingAfterUpgrading($aData, $oldPostStatus);

                return true;
            }
        }

        $aObjectIDs = PlanRelationshipModel::getObjectIDsByPaymentID($aData['paymentID']);
        if (empty($aObjectIDs)) {
            return false;
        }

        $this->inCaseToPublish($aObjectIDs, $aData, __METHOD__);
    }

    /**
     * If it's upgraded plan, We need to change that Listing status and Listing Expired
     *
     * @since 1.2.0
     */
    public function migrateListingAfterUpgrading($aData, $oldPostStatus)
    {
        if (!isset($aData['postID']) || empty($aData['postID'])) {
            return false;
        }

        $aPostTypeKeys = Submission::getAddListingPostTypeKeys();

        if (!in_array(get_post_type($aData['postID']), $aPostTypeKeys)) {
            return false;
        }

        if ($oldPostStatus == 'publish') {
            $this->inCaseToPublish([
                'objectID' => $aData['postID']
            ], $aData, __METHOD__);
        }
    }

    public function moveAllPostsToExpiry($aData)
    {
        $aObjectIDs = PlanRelationshipModel::getObjectIDsByPaymentID($aData['paymentID']);
        if (empty($aObjectIDs)) {
            return false;
        }

        foreach ($aObjectIDs as $aObjectID) {
            if (!empty($aObjectID['objectID'])) {
                wp_update_post(
                    [
                        'ID'          => $aObjectID['objectID'],
                        'post_status' => 'expired'
                    ]
                );
            }
        }
    }

    /*
     * Upgrading Listing and the payment was failed
     *
     * @since 1.2.0
     */
    public function rollupListingToPreviousStatus($aData)
    {
        if (isset($aData['postID']) && !empty($aData['postID'])) {
            $oldPostStatus = GetSettings::getPostMeta($aData['postID'], 'oldPostStatus');
            if (!empty($oldPostStatus)) {
                $oldPlanID = GetSettings::getPostMeta($aData['postID'], 'oldPlanID');
                SetSettings::setPostMeta($aData['postID'], 'belongs_to', $oldPlanID);
                wp_update_post(
                    [
                        'ID'          => $aData['postID'],
                        'post_status' => $oldPostStatus
                    ]
                );

                SetSettings::deletePostMeta($aData['postID'], 'oldPostStatus');
                SetSettings::deletePostMeta($aData['postID'], 'oldPlanID');
            }
        }
    }
}
