<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\Firebase;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\FollowerModel;
use WilokeListingTools\Models\NotificationsModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\PlanRelationshipModel;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\UserModel;

class NotificationsController extends Controller {
	protected static $connect = '___';
	protected static $notificationKey = 'notifications';

	public function __construct() {
		/*
		 * Notify to Admin
		 */
		add_action('post_updated', array($this, 'addSubmittedListingNotification'), 10, 3);
		add_action('wp_insert_post', array($this, 'addNotificationToAdminAboutNewProduct'), 10, 2);
        add_action('wiloke-listing-tools/payment-pending', array($this, 'afterChoosingPayViaDirectbankTransfer'), 10, 1);
        /*
         * Customer
         */
//		add_action('post_updated', array($this, 'addedNewListing'), 10, 3);
		add_action('wilcity/wiloke-listing-tools/app/Controllers/FollowController/new-follower', array($this, 'someoneIsFollowingYou'), 10, 2);
		add_action('post_updated', array($this, 'postChanged'), 10, 3);

		add_action('wilcity/submitted-new-review', array($this, 'addReviewNotification'), 10, 3);
		add_action('wilcity/review/discussion', array($this, 'addReviewDiscussionNotification'), 10, 2);
		add_action('wiloke/claim/approved', array($this, 'addClaimApproved'), 10, 2);
		add_action('wiloke/claim/cancelled', array($this, 'addClaimCancelled'), 10, 2);
		add_action('wilcity/submitted-report', array($this, 'addReportNotification'), 10, 2);
		add_action('post_updated', array($this, 'productPublished'), 10, 3);
		add_action('woocommerce_order_status_completed', array($this, 'someonePurchasedYourProduct'), 10, 1);
		add_action('wilcity/event/after-inserted-comment', array($this, 'afterSubmittingEventComment'), 10, 3);
		add_action('post_updated', array($this, 'handleNotificationToFollowers'), 10, 3);
		add_action('wilcity_start_send_notification_to_followers', array($this, 'sentAddedNewListingToTheRestFollowers'), 10, 2);
		add_action('added_comment_meta', array($this, 'someoneLeftARatingOnYourProduct'), 10, 3);
		/*
		 * Ajax
		 */
		add_action('wp_ajax_wilcity_fetch_notifications', array($this, 'fetchNotifications'));
		add_action('wp_ajax_wilcity_fetch_list_notifications', array($this, 'fetchNotifications'));
		add_action('wp_ajax_wilcity_delete_notification', array($this, 'deleteNotification'));
		add_action('wilcity/header/after-menu', array($this, 'quickNotification'), 15);
		add_action('wp_ajax_wilcity_count_new_notifications', array($this, 'fetchCountNewNotifications'));
		add_action('wp_ajax_wilcity_reset_new_notifications', array($this, 'resetNewNotifications'));
	}

	public function sendWelcomeMessage($userID){
		$aThemeOptions = \Wiloke::getThemeOptions();
		if ( !isset($aThemeOptions['welcome_message']) || empty($aThemeOptions['welcome_message']) ){
            return false;
		}

        NotificationsModel::add($userID, 'welcome_notifications', '');
    }

	public function resetNewNotifications(){
		SetSettings::setUserMeta(User::getCurrentUserID(), NotificationsModel::$countNewKey, 0);
	}

	public function fetchCountNewNotifications(){
		$count = GetSettings::getUserMeta(get_current_user_id(), NotificationsModel::$countNewKey);
		$count = empty($count) ? 0 : abs($count);
		wp_send_json_success($count);
	}

	public function someoneLeftARatingOnYourProduct($metaID, $commentID, $metaKey){
		if ( $metaKey != 'rating' ){
			return false;
		}

		if ( !class_exists('WooCommerce') ){
			return false;
		}

		$oComment = get_comment( $commentID );
		if ( get_post_type($oComment->comment_post_ID) != 'product' ){
			return false;
		}

		$postAuthor = get_post_field('post_author', $oComment->comment_post_ID);

		if ( !Firebase::isCustomerEnable('productReview', $postAuthor) ){
			return false;
		}

		NotificationsModel::add($postAuthor, 'someone_comment_on_product', $commentID);

        do_action('wilcity/wilcity-mobile-app/notifications/someone-comment-on-product', $metaID, $commentID, $metaKey);
	}

	public function someonePurchasedYourProduct($orderID){
		$order = wc_get_order( $orderID );
		$aItems = $order->get_items();
		foreach ($aItems as $oItem){
			$postAuthor = get_post_field('post_author', $oItem->get_product_id());
			if ( !Firebase::isCustomerEnable('soldProduct', $postAuthor) ){
				return false;
			}

			NotificationsModel::add($postAuthor, 'sold_product', $orderID);
			do_action('wilcity/wilcity-mobile-app/notifications/sold-product', $orderID, $oItem->get_product_id());
        }
    }

	public function productPublished($postID, $oPostAfter, $oPostBefore){
		if ($oPostAfter->post_type !== 'product'){
			return false;
		}

		if ( $oPostAfter->post_status == $oPostBefore->post_status ){
			return false;
		}

		if( !User::isSubmissionRole($oPostAfter->post_author) ){
			return false;
		}

		if ( !Firebase::isCustomerEnable('productPublished', $oPostAfter->post_author) ){
			return false;
		}

		NotificationsModel::add($oPostAfter->post_author, 'product_published', $postID);
		do_action('wilcity/wilcity-mobile-app/notifications/product-published', $postID, $oPostAfter, $oPostBefore);
	}

	public function afterChoosingPayViaDirectbankTransfer(){
        $listingID = Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'));
        if ( empty($listingID) ){
            return false;
        }

        $aPostTypes = General::getPostTypeKeys(false, false);
        if ( !in_array(get_post_type($listingID), $aPostTypes) ){
            return false;
        }

		$this->sendSomeoneAddNewListingNotificationToAdmin($listingID, get_post($listingID));
    }

	public function addNotificationToAdminAboutNewProduct($postID, $oPost){
		if ( ($oPost->post_status != 'pending' && $oPost->post_status != 'publish') || $oPost->post_type != 'product' ){
			return false;
		}

		if ( !User::isSubmissionRole($oPost->post_author) ){
			return false;
		}

		if ( !Firebase::isAdminEnable('someoneSubmittedAProductYourSite') ){
			return false;
		}

	    $oSuperAdmin = User::getFirstSuperAdmin();
		NotificationsModel::add($oSuperAdmin->ID, 'dokan_submitted_a_product', $postID);

		do_action('wilcity/wilcity-mobile-app/notifications/inserted-new-product', $oPost);
	}

	public function postChanged($postID, $oPostAfter, $oPostBefore){
	    if ( $oPostAfter->post_status == 'trash' || $oPostAfter->post_status == 'draft' ){
	        return false;
        }

		if ( $oPostAfter->post_status == $oPostBefore->post_status ){
			return false;
		}

		$aListingTypes = GetSettings::getFrontendPostTypes(true, false);

		if ( !in_array($oPostAfter->post_type, $aListingTypes) ){
			return false;
		}

		if( !User::isSubmissionRole($oPostAfter->post_author) ){
			return false;
		}

		if ( !Firebase::isCustomerEnable('listingStatus', $oPostAfter->post_author) ){
			return false;
		}

		NotificationsModel::add($oPostAfter->post_author, 'listing_status_changed', $postID, User::getFirstSuperAdmin()->ID);
		do_action('wilcity/wilcity-mobile-app/notifications/post-status-changed', $oPostAfter, $oPostBefore);
	}

	public function addReviewNotification($reviewID, $parentID, $senderID){
	    if ( !Firebase::isCustomerEnable('review', get_post_field('post_author', $parentID)) ){
            return false;
        }

		$receiverID = get_post_field('post_author', $parentID);
		NotificationsModel::add($receiverID, 'review', $reviewID, $senderID);
		do_action('wilcity/wilcity-mobile-app/notifications/submitted-new-review', $reviewID, $parentID, $senderID);
	}

	public function someoneIsFollowingYou($followerID, $targetID){
		if ( !Firebase::isCustomerEnable('newFollowers', $targetID) ){
			return false;
		}

		NotificationsModel::add($targetID, 'someone_is_following_you', '', $followerID);
		do_action('wilcity/wilcity-mobile-app/notifications/someone-is-following-you', $followerID, $targetID);
	}

	public function addReportNotification($listingID, $reportID){
		$receiverID = get_post_field('post_author', $listingID);
		NotificationsModel::add($receiverID, 'report', $reportID, '');
    }

    public function addClaimApproved($claimerID, $listingID){
	    if ( !Firebase::isCustomerEnable('claimApproved', $claimerID) ){
            return false;
        }

	    NotificationsModel::add($claimerID, 'claim_approved', $listingID, User::getFirstSuperAdmin()->ID);

	    do_action('wilcity/wilcity-mobile-app/notifications/claim-approved', $claimerID, $listingID);
    }

    public function addClaimCancelled($claimerID, $listingID){
	    NotificationsModel::add($claimerID, 'claim_rejected', $listingID, '');
    }

	public function addReviewDiscussionNotification($discussionID, $reviewID){
		$receiverID = get_post_field('post_author', $reviewID);
        if ( !Firebase::isCustomerEnable('reviewDiscussion', $receiverID) ){
            return false;
        }

		NotificationsModel::add($receiverID, 'review_discussion', $discussionID, User::getCurrentUserID());

        $postType = get_post_type($reviewID) == 'event';
        if ( $postType != 'event' ){
	        do_action('wilcity/wilcity-mobile-app/notifications/someone-left-an-event-comment', $discussionID, get_post_field('post_author', $discussionID), $reviewID);
        }else{
	        do_action('wilcity/wilcity-mobile-app/notifications/review-discussion', $discussionID, $receiverID);
        }
	}

	public function afterSubmittingEventComment($commentID, $userID, $parentID){
	    if ( !Firebase::isCustomerEnable('eventComment', get_post_field('post_author', $parentID)) ){
	        return false;
        }

		NotificationsModel::add(get_post_field('post_author', $parentID), 'comment_discussion', $commentID, $userID);

	    do_action('wilcity/wilcity-mobile-app/notifications/someone-left-an-event-comment', $commentID, $userID, $parentID);
    }

    private function sendSomeoneAddNewListingNotificationToAdmin($postID, $oPostAfter){
	    $aDirectoryType = GetSettings::getFrontendPostTypes(true, false);

	    if ( !in_array($oPostAfter->post_type, $aDirectoryType) ){
		    return false;
	    }

	    if ( !User::isSubmissionRole($oPostAfter->post_author) ){
		    return false;
	    }

	    if ( !Firebase::isAdminEnable('someoneSubmittedAListingToYourSite') ){
		    return false;
	    }

	    $aSuperAdmins = User::getSuperAdmins();
	    foreach ($aSuperAdmins as $oAdmin){
		    NotificationsModel::add($oAdmin->ID, 'submitted_listing', $postID, $oPostAfter->post_author);
	    }

	    do_action('wilcity/wilcity-mobile-app/notifications/submitted-new-listing', $oPostAfter);
    }

    public function userSubmitListingWithSkipPreviewStep($authorID, $postID){
        if ( !\WilokeThemeOptions::isEnable('addlisting_skip_preview_step', false) ){
            return false;
        }

	    $this->sendSomeoneAddNewListingNotificationToAdmin($postID, get_post($postID));
    }

	public function addSubmittedListingNotification($postID, $oPostAfter, $oPostBefore){
		if ( $oPostAfter->post_status == $oPostBefore->post_status ){
			return false;
		}

		if ( $oPostAfter->post_status != 'pending' && $oPostAfter->post_status != 'publish' ){
			return false;
		}

        $this->sendSomeoneAddNewListingNotificationToAdmin($postID, $oPostAfter);
    }

	public function quickNotification(){
		if ( !is_user_logged_in() || !GetWilokeSubmission::isSystemEnable() ){
			return '';
		}
		?>
		<div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-quick-notifications')); ?>" class="header_loginItem__oVsmv">
			<quick-notifications></quick-notifications>
		</div>
		<?php
	}

	public function deleteNotification(){
		if ( !isset($_POST['ID']) || empty($_POST['ID']) ){
			wp_send_json_error(array(
				'msg' => esc_html__('The notification id is required', 'wiloke-listing-tools')
			));
		}

		$userID = get_current_user_id();
		NotificationsModel::deleteOfReceiver($_POST['ID'], $userID);
		wp_send_json_success();
	}

	public function handleNotificationToFollowers($postID, $oPostAfter, $oPostBefore){
		if ( $oPostAfter->post_status == $oPostBefore->post_status ){
			return false;
		}

		if ( $oPostAfter->post_status !== 'publish' ){
			return false;
		}

		$aListingTypes = GetSettings::getFrontendPostTypes(true, false);

		if ( !in_array($oPostAfter->post_type, $aListingTypes) ){
			return false;
		}

		if ( !Firebase::getCustomerMsg('followerPublishedNewListing') ){
            return false;
        }

        $this->sentAddedNewListingToTheRestFollowers($oPostAfter, array());
	}

	public function sentAddedNewListingToTheRestFollowers($oAfterPost, $aListSent){
		$aFollowers = UserModel::getFollowingsWithExcludes($oAfterPost->post_author, $aListSent);

		if ( empty($aFollowers) ){
		    return false;
        }

		foreach ($aFollowers as $order => $aFollow){
			$aListSent[] = $aFollow['followerID'];

			if ( !Firebase::isCustomerEnable('followerPublishedNewListing', $aFollow['followerID']) ){
				unset($aFollowers[$order]);
			    continue;
			}

			if ( User::userIDExists($aFollow['followerID']) ){
				NotificationsModel::add($aFollow['followerID'], 'published_new_listing', $oAfterPost->ID, $oAfterPost->post_author);
            }else{
				UserModel::deleteFollowingUser($aFollow['followerID']);
            }
		}

		do_action('wilcity/wilcity-mobile-app/notifications/send-published-post-to-followers', $oAfterPost, $aFollowers);
		wp_schedule_single_event( time() + 120, 'wilcity_start_send_notification_to_followers', array( $oAfterPost, $aListSent ) );
    }


	public function test(){
		$this->update(1, 1756, 'review', 'add', array());
	}

	/*
	 * @receivedID: the id of receiver
	 * @type: The type of action: comment, review, like
	 * @status: remove/update
	 * @aOtherArgs: The other information
	 */
	public function update($receivedID, $targetID, $type, $status, $aOtherInfo){

	}

	public function getAuthorInfo($postID){
		$authorID = get_post_field('post_author', $postID);
		$aData['authorName']  = get_the_author_meta('display_name', $authorID);
		$aData['authorAvatar']  = User::getAvatar($authorID);
		return $aData;
	}

	public static function getReport($oInfo){
		$postStatus = get_post_status($oInfo->objectID);
		if ( empty($postStatus) || is_wp_error($postStatus) ){
			return array(
				'content' => esc_html__('Report no longer available', 'wiloke-listing-tools'),
				'link'    => '#',
				'time' => Time::timeFromNow(strtotime($oInfo->date)),
				'type' => 'report',
				'ID'   => absint($oInfo->ID)
			);
		}else{
			$postID = GetSettings::getPostMeta($oInfo->objectID, 'listing_name');
			return array(
				'title' => esc_html__('Warning', 'wiloke-listing-tools'),
				'featuredImg'      => User::getAvatar($postID),
				'content' => __('You got a report for', 'wiloke-listing-tools'),
				'contentHighlight' => get_the_title($postID),
				'link'    => add_query_arg(
					array(
						'action' => 'edit',
						'post'   => $oInfo->objectID
					),
					admin_url('post.php')
				),
				'time' => Time::timeFromNow(strtotime($oInfo->date)),
                'type' => 'report',
                'ID'   => absint($oInfo->ID)
			);
		}
	}

	public static function getProductCommentMsg($oInfo){
		$oComment = get_comment( $oInfo->objectID );

		$msg = str_replace(array(
			'%postTitle%',
			'%rating%',
			'%reviewExcerpt%',
			'%review%'
		), array(
			get_the_title($oComment->comment_post_ID),
			get_comment_meta($oInfo->objectID, 'rating', true),
			\Wiloke::contentLimit(50, $oComment, true, $oComment->comment_content),
			$oComment->comment_content
		), Firebase::getCustomerMsg('productReview'));

		return array(
			'featuredImg' => User::getAvatar($oComment->user_id),
			'content' => $msg,
			'link'    => get_permalink($oComment->comment_post_ID),
			'time' => Time::timeFromNow(strtotime($oComment->comment_date)),
			'type' => 'someone_comment_on_product',
			'ID'   => absint($oInfo->ID)
		);
    }

	public static function getDokanOrderCompleted($oInfo){
		$productID = GetSettings::getFirstDokanProductByOrder( $oInfo->objectID );
        $msg = str_replace(array(
	        '%orderID%',
	        '%postTitle%'
        ), array(
	        $oInfo->objectID,
	        get_the_title($productID)
        ), Firebase::getCustomerMsg('soldProduct'));

		return array(
			'featuredImg' => GetSettings::getProductThumbnail($productID),
			'content' => $msg,
			'link'    => function_exists('dokan_get_navigation_url') ? dokan_get_navigation_url('orders') : '#',
			'time' => Time::timeFromNow(strtotime(GetSettings::getOrderDate($oInfo->objectID))),
            'type' => 'sold_product',
			'ID'   => absint($oInfo->ID)
		);
    }

	public static function getDokanCustomerSubmittedAProductToSite($oInfo){
		$oSuperAdmin = User::getFirstSuperAdmin();
		$postAuthor = get_post_field($oInfo->objectID);

		if ( $postAuthor == $oSuperAdmin->ID ){
			return '';
		}
		$oPost = get_post($oInfo->objectID);

		$msg = str_replace(
			array(
				'%userName%',
				'%postTitle%',
				'%postDate%',
				'%postType%',
				'%postID%'
			),
			array(
				'<strong>'.User::getField('display_name', $oPost->post_author).'</strong>',
				'<strong>'.$oPost->post_title.'</strong>',
				get_the_date(get_option('date_format') . ' ' . get_option('time_format'), $oPost),
				$oPost->post_type,
				$oPost->ID
			),
			Firebase::getAdminMsg('someoneSubmittedAProductYourSite')
		);

		return array(
			'featuredImg' => User::getAvatar($postAuthor),
			'content'     => $msg,
			'link'        => add_query_arg(
                array(
                    'post' => $oInfo->objectID,
                    'action' => 'edit'
                ),
                admin_url('post.php')
            ),
			'time'        => Time::timeFromNow(strtotime(get_post_field('post_date', $oInfo->objectID))),
			'type' => 'dokan_submitted_a_product',
			'ID'   => absint($oInfo->ID)
		);
	}

	public static function getDokanApprovedWithdrawal($oInfo){
	    $oUserAdmin = User::getFirstSuperAdmin();

	    return array(
		    'title'       => User::getField('display_name', $oUserAdmin->ID),
		    'featuredImg' => User::getAvatar($oUserAdmin->ID),
		    'content'     => __('Your withdrawal has been processed.', 'wiloke-listing-tools'),
		    'contentHighlight' => esc_html__('View Detail', 'wiloke-listing-tools'),
		    'link'        => function_exists('dokan_get_navigation_url') ? dokan_get_navigation_url('withdraw') : '#',
		    'time'        => Time::timeFromNow(strtotime(GetSettings::getDokanWithDrawField('date', $oInfo->objectID))),
		    'type' => 'dokan_approved_withdrawal',
		    'ID'   => absint($oInfo->ID)
	    );
    }

    public static function getDokanCancelledWithdrawal($oInfo){
	    $oUserAdmin = User::getFirstSuperAdmin();

	    return array(
		    'title'       => User::getField('display_name', $oUserAdmin->ID),
		    'featuredImg' => User::getAvatar($oUserAdmin->ID),
		    'content'     => __('Unfortunately, Your withdrawal has been cancelled.', 'wiloke-listing-tools'),
		    'contentHighlight' => esc_html__('View Detail', 'wiloke-listing-tools'),
		    'link'        => function_exists('dokan_get_navigation_url') ? dokan_get_navigation_url('withdraw') : '#',
		    'time'        => Time::timeFromNow(strtotime(GetSettings::getDokanWithDrawField('date', $oInfo->objectID))),
		    'type' => 'dokan_cancelled_withdrawal',
		    'ID'   => absint($oInfo->ID)
	    );
    }

    public static function getDokanRequestWithdrawnal($oInfo){
	    $userID = User::dokanGetUserIDByWithDrawID($oInfo->objectID);
        $amount = GetSettings::getDokanWithDrawField('amount', $oInfo->objectID);
        $priceFormat = get_woocommerce_price_format();
        $symbol = get_woocommerce_currency_symbol();
	    $amount = sprintf($priceFormat, html_entity_decode($symbol), $amount);

	    return array(
		    'title'       => User::getField('display_name', $userID),
		    'featuredImg' => User::getAvatar($userID),
		    'content'     => sprintf(__('requested %s withdrawal.', 'wiloke-listing-tools'), $amount),
		    'contentHighlight' => esc_html__('View Detail', 'wiloke-listing-tools'),
		    'link'        => add_query_arg(
                array(
                    'page' => 'dokan#/withdraw?status=pending'
                ),
                admin_url('admin.php')
            ),
		    'time'        => Time::timeFromNow(strtotime(GetSettings::getDokanWithDrawField('date', $oInfo->objectID))),
		    'type' => 'dokan_requested_withdrawal',
		    'ID'   => absint($oInfo->ID)
	    );
    }

	public static function getProductPublished($oInfo){
		return array(
			'featuredImg' => GetSettings::getProductThumbnail($oInfo->objectID),
			'content' => str_replace('%postTitle%', get_the_title($oInfo->objectID), Firebase::getCustomerMsg('productPublished')),
			'link'    => get_permalink($oInfo->objectID),
			'time' => Time::timeFromNow(strtotime(get_post_field('post_date_gmt', $oInfo->objectID)), true),
			'type' => 'product_published',
			'ID'   => absint($oInfo->ID)
		);
	}

	public static function getPublishedNewListing($oInfo){
		$oPost = get_post($oInfo->objectID);
		if ( empty($oPost) || is_wp_error($oPost) ){
			return array(
				'type' => 'published_new_listing',
				'ID'   => absint($oInfo->ID),
				'time' => Time::timeFromNow(strtotime($oInfo->date)),
				'content' => esc_html__('Listing no longer available', 'wiloke-listing-tools'),
				'link'    => '#',
				'isNoLogger' => 'yes'
			);
		}else{
			$msg = str_replace(
				array(
					'%userName%',
					'%postTitle%',
					'%postDate%',
					'%postType%',
					'%postID%',
                    '%postExcerpt%'
				),
				array(
					User::getField('display_name', $oPost->post_author),
					$oPost->post_title,
					get_the_date(get_option('date_format') . ' ' . get_option('time_format'), $oPost),
					$oPost->post_type,
					$oPost->ID,
					\Wiloke::contentLimit(60, $oPost, true, $oPost->post_content)
				),
				Firebase::getCustomerMsg('followerPublishedNewListing')
			);

			return array(
				'title' => User::getField('display_name', $oPost->post_author),
				'featuredImg' => User::getAvatar($oPost->post_author),
				'content' => $msg,
				'link' => get_permalink($oInfo->objectID),
				'time' => Time::timeFromNow(strtotime($oInfo->date)),
				'type' => 'published_new_listing',
				'ID'   => absint($oInfo->ID)
			);
		}
    }

	public static function getSubmittedListing($oInfo){
		$postStatus = get_post_status($oInfo->objectID);
		if ( empty($postStatus) || is_wp_error($postStatus) ){
			return array(
                'type'    => 'submitted_listing',
                'ID'   => absint($oInfo->ID),
                'time' => Time::timeFromNow(strtotime($oInfo->date)),
				'content' => esc_html__('Listing no longer available', 'wiloke-listing-tools'),
				'link'    => '#',
                'isNoLogger' => 'yes'
			);
		}else{
		    $authorID = get_post_field('post_author', $oInfo->objectID);
			$oPost = get_post($oInfo->objectID);

			$msg = str_replace(
				array(
					'%userName%',
					'%postTitle%',
					'%postDate%',
					'%postType%',
					'%postID%'
				),
				array(
					'<strong>'.User::getField('display_name', $oPost->post_author).'</strong>',
					'<strong>'.$oPost->post_title.'</strong>',
					get_the_date(get_option('date_format') . ' ' . get_option('time_format'), $oPost),
					$oPost->post_type,
					$oPost->ID
				),
				Firebase::getAdminMsg('someoneSubmittedAListingToYourSite')
			);

			return array(
				'featuredImg' => User::getAvatar($authorID),
				'content' => $msg,
				'link'    => add_query_arg(
					array(
						'action' => 'edit',
                        'post'   => $oInfo->objectID
					),
					admin_url('post.php')
				),
				'time' => Time::timeFromNow(strtotime($oInfo->date)),
				'type' => 'submitted_listing',
				'ID'   => absint($oInfo->ID)
			);
		}
	}

	public static function getClaimApproved($oInfo){
	    $msg = Firebase::getCustomerMsg('claimApproved');
		$msg = str_replace('%postTitle%', get_the_title($oInfo->objectID), $msg);

		return array(
			'featuredImg'   => User::getAvatar($oInfo->senderID),
			'content'       => $msg,
			'link'          => get_permalink($oInfo->objectID),
			'time'          => Time::timeFromNow(strtotime($oInfo->date)),
			'type' => 'claim_approved',
			'ID'   => absint($oInfo->ID)
		);
    }

    public static function getFollowerInfo($oInfo){
	    $msg = str_replace('%userName%', User::getField('display_name', $oInfo->senderID), Firebase::getCustomerMsg('newFollowers'));

	    return array(
		    'featuredImg'   => User::getAvatar($oInfo->senderID),
		    'content'       => $msg,
		    'link'          => get_author_posts_url($oInfo->senderID),
		    'time'          => Time::timeFromNow(strtotime($oInfo->date)),
		    'type'          => 'someone_is_following_you',
		    'ID'            => absint($oInfo->ID)
	    );
    }

    public static function getListingStatusChangedMsg($oInfo){
        $oPost = get_post($oInfo->objectID);

	    return array(
		    'featuredImg'   => User::getAvatar($oInfo->senderID),
		    'content'       => sprintf(__('Your article %s has been changed to %s', 'wiloke-listing-tools'), $oPost->post_title, $oPost->post_status),
		    'link'          => get_permalink($oPost->ID),
		    'time'          => Time::timeFromNow(strtotime($oInfo->date)),
		    'type'          => 'listing_status_changed',
		    'ID'            => absint($oInfo->ID)
	    );
    }

	public static function getClaimRejected($oInfo){
		return array(
			'title'             => User::getField('display_name', $oInfo->senderID),
			'featuredImg'       => User::getAvatar($oInfo->senderID),
			'content'           => sprintf(__('We are regret to inform you that your claim %s has been rejected.', 'wiloke-listing-tools'), get_the_title($oInfo->objectID)),
			'contentHighlight'  => '',
			'link'              => get_permalink($oInfo->objectID),
			'time'              => Time::timeFromNow(strtotime($oInfo->date)),
			'type' => 'claim_rejected',
			'ID'   => absint($oInfo->ID)
		);
	}

	public static function getReviewDiscussion($oInfo){
		$postStatus = get_post_status($oInfo->objectID);
		if ( empty($postStatus) || is_wp_error($postStatus) ){
			return array(
				'content' => esc_html__('Review no longer available', 'wiloke-listing-tools'),
				'link'    => '#',
				'type' => 'review_discussion',
                'objectID'    => $oInfo->objectID,
				'ID'   => absint($oInfo->ID),
				'isNoLogger' => 'yes',
				'featuredImg' => ''
			);
		}else{
			$oPost = get_post($oInfo->objectID);
			$reviewID = get_post_field('post_parent', $oInfo->objectID);
			$postID   = get_post_field('post_parent', $reviewID);
            $msg = str_replace(array(
	            '%userName%',
	            '%postTitle%',
	            '%reviewExcerpt%',
	            '%review%'
            ), array(
                User::getField('display_name', $postID),
                get_the_title($reviewID),
	            \Wiloke::contentLimit(50, $oPost),
	            $oPost->post_content
            ), Firebase::getCustomerMsg('reviewDiscussion'));

			return array(
				'featuredImg' => User::getAvatar($oPost->post_author),
				'content' => $msg,
				'link'    => add_query_arg(
					array(
						'st'  => 'js-review-discussion-'.$oInfo->objectID,
						'tab' => 'reviews'
					),
					get_permalink($postID)
				),
				'time' => Time::timeFromNow(strtotime($oInfo->date), false),
                'type' => 'review_discussion',
                'parentID'    => $reviewID,
                'objectID'    => $oInfo->objectID,
				'ID'   => absint($oInfo->ID)
			);
		}
	}

	public static function getReview($oInfo){
		$postStatus = get_post_status($oInfo->objectID);
		if ( empty($postStatus) || is_wp_error($postStatus) ){
			return array(
				'content' => esc_html__('Review no longer available', 'wiloke-listing-tools'),
				'link'    => '#',
				'type' => 'review',
                'isNoLogger' => 'yes'
			);
		}else{
			$authorID = get_post_field('post_author', $oInfo->objectID);
			$parentID = get_post_field('post_parent', $oInfo->objectID);
			$post_status = get_post_field('post_status', $oInfo->objectID);

			if( $post_status == 'publish' ) {
				$postDateUTC = get_post_field('post_date_gmt', $oInfo->objectID);
			} else {
				$postDateUTC = get_post_field('post_date', $oInfo->objectID);
			}

            $msg = str_replace(array(
	            '%userName%',
	            '%postTitle%',
	            '%reviewExcerpt%',
	            '%averageRating%'
            ), array(
	            User::getField('display_name', $authorID),
	            get_the_title($parentID),
	            \Wiloke::contentLimit(50, get_post($oInfo->objectID)),
	            ReviewMetaModel::getAverageReviewsItem($oInfo->objectID, $postStatus)
            ), Firebase::getCustomerMsg('review'));

			return array(
				'featuredImg' => User::getAvatar($authorID),
				'content' => $msg,
				'link'    => $postStatus == 'publish' ? add_query_arg(
					array(
						'st'  => 'js-review-item-'.$oInfo->objectID,
						'tab' => 'reviews'
					),
					get_permalink($parentID)
				) : add_query_arg(
					array(
						'post' => $oInfo->objectID,
						'action' => 'edit'
					),
					admin_url('post.php')
				),
				'time' => Time::timeFromNow(strtotime('2019-04-01 01:08:15'), true),
                'type' => 'review',
                'objectID' => $oInfo->objectID,
                'parentID' => $parentID,
				'ID'   => absint($oInfo->ID)
			);
		}
	}

	public static function getLike($targetID){
		return false;
	}

	public static function getEventDiscussion($oInfo){
	    $oPost = get_post($oInfo->objectID);
	    $parentID = wp_get_post_parent_id($oInfo->objectID);
        $msg = str_replace(array(
	        '%userName%',
	        '%postTitle%',
	        '%commentExcerpt%',
	        '%comment%'
        ), array(
            User::getField('display_name', $oPost->post_author),
            get_the_title($parentID),
	        \Wiloke::contentLimit(50, $oPost, true, $oPost->post_content),
	        $oPost->post_content
        ), Firebase::getCustomerMsg('eventComment'));

		return array(
			'featuredImg'       => User::getAvatar($oInfo->senderID),
			'link'              => get_permalink($parentID),
			'content'           => $msg,
			'time'              => Time::timeFromNow(strtotime($oInfo->date)),
			'type'              => 'comment_discussion',
            'ID'                => absint($oInfo->ID),
			'objectID'          => absint($oInfo->objectID),
            'senderID'          => $oInfo->senderID
		);
    }

    public static function getWelcomeMessage($oInfo){
	    $oFirstSuperAdmin = User::getFirstSuperAdmin();

	    return array(
		    'featuredImg'       => User::getAvatar($oFirstSuperAdmin->ID),
		    'link'              => GetWilokeSubmission::getDashboardUrl('dashboard_page', 'notifications'),
		    'content'           => \WilokeThemeOptions::getOptionDetail('welcome_message'),
		    'time'              => Time::timeFromNow(strtotime($oInfo->date)),
		    'type'              => 'welcome_notifications',
		    'ID'                => absint($oInfo->ID),
		    'objectID'          => '',
		    'senderID'          => $oFirstSuperAdmin->ID
	    );
    }

	public static function getNotificationType($oInfo){
		$aNotification = '';
		switch ($oInfo->type){
            case 'welcome_notifications':
	            $aNotification = self::getWelcomeMessage($oInfo);
                break;
			case 'review':
				$aNotification = self::getReview($oInfo);
				break;
			case 'review_discussion':
				$aNotification = self::getReviewDiscussion($oInfo);
				break;
			case 'report':
				$aNotification = self::getReport($oInfo);
				break;
			case 'claim_approved':
				$aNotification = self::getClaimApproved($oInfo);
				break;
            case 'someone_is_following_you':
	            $aNotification = self::getFollowerInfo($oInfo);
                break;
            case 'listing_status_changed':
	            $aNotification = self::getListingStatusChangedMsg($oInfo);
                break;
			case 'claim_rejected':
				$aNotification = self::getClaimRejected($oInfo);
				break;
			case 'submitted_listing':
				$aNotification = self::getSubmittedListing($oInfo);
				break;
            case 'published_new_listing':
	            $aNotification = self::getPublishedNewListing($oInfo);
                break;
			case 'product_published':
				$aNotification = self::getProductPublished($oInfo);
				break;
			case 'sold_product':
				$aNotification = self::getDokanOrderCompleted($oInfo);
				break;
            case 'someone_comment_on_product':
	            $aNotification = self::getProductCommentMsg($oInfo);
                break;
			case 'dokan_requested_withdrawal':
				$aNotification = self::getDokanRequestWithdrawnal($oInfo);
				break;
			case 'dokan_approved_withdrawal':
				$aNotification = self::getDokanApprovedWithdrawal($oInfo);
				break;
			case 'dokan_cancelled_withdrawal':
				$aNotification = self::getDokanCancelledWithdrawal($oInfo);
				break;
			case 'dokan_submitted_a_product':
				$aNotification = self::getDokanCustomerSubmittedAProductToSite($oInfo);
				break;
			case 'like':
				$aNotification = self::getLike($oInfo);
				break;
            case 'comment_discussion':
	            $aNotification = self::getEventDiscussion($oInfo);
                break;
            default:
                $aNotification = apply_filters('wilcity/wiloke-listing-tools/get-notification/'.$oInfo->type, '',
                    $oInfo);
                break;
		}
		return $aNotification;
    }

	public function fetchNotifications(){
		$userID = get_current_user_id();
		$this->middleware(['isUserLoggedIn'], array());
		$errMsg = esc_html__('No Notifications', 'wiloke-listing-tools');

		$limit = isset($_POST['limit']) && $_POST['limit'] <= 100 ? abs($_POST['limit']) : 20;
		$paged = isset($_POST['paged']) ? abs($_POST['paged']) : 1;
		$offset = ($paged-1)*$limit;

		$aNotifications = NotificationsModel::get($userID, $limit, $offset);

		if ( !$aNotifications ){
			wp_send_json_error(array(
				'msg' => $errMsg
			));
		}else{
			$aNotificationsInfo = array();
			foreach ($aNotifications['aResults'] as $oInfo){
				$aNotification = self::getNotificationType($oInfo);

				if ( $aNotification ){
					$aNotification['ID']     = $oInfo->ID;
					if ( isset($aNotification['content']) ){
					    $aNotification['content'] = stripslashes($aNotification['content']);
                    }
					$aNotificationsInfo[]   = $aNotification;
				}
			}

			if ( empty($aNotificationsInfo) ){
				if ( !isset($_POST['paged']) ){
					wp_send_json_error(array(
						'msg' => $errMsg
					));
				}else{
					wp_send_json_success(array(
						'isFinished' => true
					));
				}
			}

			$dashboardUrl = '';
			if ( isset($_POST['needDashboardUrl']) && $_POST['needDashboardUrl'] == 'yes' ){
			    $dashboardUrl = GetWilokeSubmission::getField('dashboard_page', true);
				$dashboardUrl .= '#/notifications';
            }

			wp_send_json_success(
				array(
					'oInfo'         => $aNotificationsInfo,
					'dashboardUrl'  => $dashboardUrl,
					'maxPages'      => ceil($aNotifications['total']/$limit)
				)
			);
		}
	}
}
