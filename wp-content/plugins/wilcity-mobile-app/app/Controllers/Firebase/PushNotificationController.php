<?php
namespace WILCITY_APP\Controllers\Firebase;


use Kreait\Firebase\ServiceAccount;
use WeatherStation\UI\Widget\Fire;
use WILCITY_APP\Controllers\JsonSkeleton;
use WILCITY_APP\Database\FirebaseDB;
use WILCITY_APP\Database\FirebaseDeviceToken;
use WILCITY_APP\Database\FirebaseMsgDB;
use WILCITY_APP\Database\FirebaseUser;
use WilokeListingTools\Controllers\FollowController;
use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Framework\Helpers\Firebase;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\FavoriteStatistic;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Models\UserModel;

class PushNotificationController  {
    use JsonSkeleton;
    
    private $aAdminSettings = null;
    private $aCustomerSettings;
    private $msg;
    private $aAdminDeviceTokens = null;
    private $pushNotificationCenter = 'https://exp.host/--/api/v2/push/send';
    private $aAdminKeys = array('someoneSubmittedAListingToYourSite', 'someoneSubmittedAProductYourSite');
    
    private function isDisableAllNotification($userID){
        return !FirebaseMsgDB::getNotificationStatus($userID, 'toggleAll');
    }
    
    private function getAdminSettings(){
        if ( $this->aAdminSettings !== null ){
            $this->aAdminSettings= GetSettings::getOptions('admin_receive_notifications_settings');
            return $this->aAdminSettings;
        }
        return false;
    }
    
    private function isEnableNotificationStatusOnDevice($userID, $key, $isAdmin=false){
        if ( $this->isDisableAllNotification($userID) ){
            return false;
        }
        
        if ( $isAdmin ){
            return true;
        }
        
        return FirebaseMsgDB::getNotificationStatus($userID, $key);
    }
    
    private function getAdminMsg($key){
        $this->getAdminSettings();
        return isset($this->aAdminSettings[$key]) && isset($this->aAdminSettings[$key]['msg']) ? $this->aAdminSettings[$key]['msg'] : '';
    }
    
    private function generalReplacements($string, $userID=null, $postID=null){
        $postTitle = '';
        $displayName = '';
        if ( !empty($postID) ){
            $postTitle = get_the_title($postID);
        };
        
        if ( !empty($userID) ){
            $displayName = User::getField('display_name', $userID);
        };
        
        return str_replace(array(
            '%userName%',
            '%postTitle%'
        ), array(
            $displayName,
            $postTitle
        ), $string);
    }
    
    private function push($aBody, $userID=null, $postID=null){
        $aBody['body'] = $this->generalReplacements($aBody['body'], $userID, $postID);
        $aBody['to'] = Firebase::getDeviceToken();
        $aBody['sound'] = 'default';
        
        wp_remote_post($this->pushNotificationCenter, array(
            'headers' => array(
                'Content-Type'    => 'application/json',
                'Accept'          => 'application/json',
                'Accept-encoding' => 'gzip, deflate'
            ),
            'body' => json_encode($aBody)
        ));
    }
    
    private function pushArray($aMsg){
        wp_remote_post($this->pushNotificationCenter, array(
            'headers' => array(
                'Content-Type'    => 'application/json',
                'Accept'          => 'application/json',
                'Accept-encoding' => 'gzip, deflate'
            ),
            'body' => json_encode($aMsg)
        ));
    }
    
    private function getAdminDeviceTokens($key){
        $aSuperAdmins = User::getSuperAdmins();
        if ( $this->aAdminDeviceTokens !== null ){
            return $this->aAdminDeviceTokens;
        }
        
        foreach ($aSuperAdmins as $oAdmin){
            $firebaseID = FirebaseUser::getFirebaseID($oAdmin->ID);
            if ( !empty($firebaseID) ){
                $isAdmin = in_array($key, $this->aAdminKeys);
                if ( !$this->isEnableNotificationStatusOnDevice($oAdmin->ID, $key, $isAdmin) ){
                    continue;
                }
                
                $deviceToken = FirebaseDeviceToken::getDeviceToken($oAdmin->ID, $firebaseID);
                if ( !empty($deviceToken) ){
                    $this->aAdminDeviceTokens[$oAdmin->ID] = $deviceToken;
                }
            }
        }
        
        if ( empty($this->aAdminDeviceTokens) ){
            $this->aAdminDeviceTokens = false;
            return false;
        }
        $this->msg = $this->getAdminMsg($key);
        return $this->aAdminDeviceTokens;
    }
    
    public function __construct() {
        add_action('wilcity/wilcity-mobile-app/notifications/someone-is-following-you', array($this, 'someoneFollowedYou'), 10, 2);
        add_action('wilcity/wilcity-mobile-app/notifications/send-published-post-to-followers', array($this, 'startSendNotificationToFollowers'), 10, 2);
        
        add_action('wilcity/wilcity-mobile-app/notifications/submitted-new-review', array($this, 'someoneLeftAReviewOnYourSite'), 10, 3);
        add_action('wilcity/wilcity-mobile-app/notifications/review-discussion', array($this, 'someLeftADiscussionOnYourReview'), 10, 2);
        add_action('wilcity/wilcity-mobile-app/notifications/someone-left-an-event-comment', array($this, 'someoneLeftACommentOnYourSite'), 10, 3);

//		add_action('wilcity/action/send-welcome-message', array($this, 'sendWelcomeMessage'), 10, 3);
        add_action('wilcity/action/received-message', array($this, 'someoneSendAMessageToYou'), 10, 3);
        add_action('wilcity/wiloke-listing-tools/observerSendMessage', array($this, 'ajaxObserverSendMessage'), 10, 1);
        
        add_action('wilcity/wilcity-mobile-app/notifications/claim-approved', array($this, 'yourClaimHasBeenApproved'), 10, 2);
        add_action('wilcity/wilcity-mobile-app/notifications/post-status-changed', array($this, 'postChanged'), 10, 2);
        
        add_action('wilcity/wilcity-mobile-app/notifications/product-published', array($this, 'productPublished'), 10, 3);
        
        add_action('wilcity/wilcity-mobile-app/notifications/someone-comment-on-product', array($this, 'someoneLeftARatingOnYourProduct'), 10, 3);
        add_action('wilcity/wilcity-mobile-app/notifications/sold-product', array($this, 'someonePurchasedYourProduct'), 10, 2);
        
        // Admin
        add_action('wilcity/wilcity-mobile-app/notifications/submitted-new-listing', array($this, 'someoneSubmittedAListingToYourSite'), 10, 1);
        add_action('wilcity/wilcity-mobile-app/notifications/inserted-new-product', array($this, 'someoneSubmittedANewProductToYourSite'), 10, 1);
        
        add_action('wilcity/wilcity-mobile-app/send-push-notification-directly', [$this, 'sendPushNotificationDirectly'], 10, 3);
        
        add_action( 'rest_api_init', function () {
            register_rest_route( WILOKE_PREFIX.'/v2/', '/notification-settings', array(
                'methods'   => 'GET',
                'callback'  => array($this, 'getNotificationSettings')
            ));
        });
        
        add_action( 'rest_api_init', function () {
            register_rest_route( WILOKE_PREFIX.'/v2/', '/firebase-configuration', array(
                'methods'   => 'GET',
                'callback'  => array($this, 'getFirebaseConfiguration')
            ));
        });
    }
    
    public function getFirebaseConfiguration(){
        $aConfigurations = Firebase::getFirebaseChatConfiguration();
        if ( empty($aConfigurations) ){
            return array(
                'status' => 'error',
                'msg'    => 'Firebase configuration is required: Log into your site -> Wiloke Tools -> Firebase Configuration'
            );
        }
        
        return array(
            'status' => 'success',
            'oConfiguration' => $aConfigurations
        );
    }
    
    public function getNotificationSettings(){
        if ( empty($this->aCustomerSettings) ){
            $this->aCustomerSettings = GetSettings::getOptions('customers_receive_notifications_settings');
        }
        
        $aNotificationSettings = array();
        foreach ($this->aCustomerSettings as $key => $aSettings){
            if ( $key !== 'toggleAll' && ( empty($aSettings['msg']) || $aSettings['status'] == 'off') ){
                continue;
            }
            
            unset($aSettings['msg']);
            unset($aSettings['status']);
            $aSettings['key'] = $key;
            
            $aNotificationSettings[] = $aSettings;
        }
        
        if ( empty($aNotificationSettings) ){
            return array(
                'status' => 'error',
                'msg'    => 'thisFeatureIsNotAvailable'
            );
        }
        
        return array(
            'status' => 'success',
            'aSettings' => $aNotificationSettings
        );
    }
    
    public function someoneSubmittedAListingToYourSite($oPost){
        if ( !$this->getAdminDeviceTokens('someoneSubmittedAListingToYourSite') ){
            return false;
        }
        
        $this->msg = str_replace(
            array(
                '%userName%',
                '%postTitle%',
                '%postDate%',
                '%postType%',
                '%postID%'
            ),
            array(
                User::getField('display_name', $oPost->post_author),
                $oPost->post_title,
                get_the_date(get_option('date_format') . ' ' . get_option('time_format'), $oPost),
                $oPost->post_type,
                $oPost->ID
            ),
            Firebase::getMessage()
        );
        
        $aBuildMsg = array();
        
        foreach ($this->aAdminDeviceTokens as $deviceToken){
            $aBuildMsg[] = array(
                'to'   => $deviceToken,
                'body' => $this->msg,
                'sound' => 'default',
                'data' => $oPost->post_type == 'event' ? $this->eventScreenSkeleton($oPost) : $this->listingScreenSkeleton($oPost)
            );
        }
        
        $this->pushArray($aBuildMsg);
        Firebase::resetInfo();
    }
    
    public function someoneSubmittedANewProductToYourSite($oPost){
        if ( !$this->getAdminDeviceTokens('someoneSubmittedAProductYourSite') ){
            return false;
        }
        
        $this->msg = str_replace(
            array(
                '%userName%',
                '%postTitle%',
                '%postDate%',
                '%postType%',
                '%postID%'
            ),
            array(
                User::getField('display_name', $oPost->post_author),
                $oPost->post_title,
                get_the_date(get_option('date_format') . ' ' . get_option('time_format'), $oPost),
                $oPost->post_type,
                $oPost->ID
            ),
            Firebase::getMessage()
        );
        
        $aBuildMsg = array();
        
        foreach ($this->aAdminDeviceTokens as $deviceToken){
            $aBuildMsg[] = array(
                'to'   => $deviceToken,
                'body' => $this->msg,
                'sound' => 'default'
            );
        }
        
        $this->pushArray($aBuildMsg);
        Firebase::resetInfo();
    }
    
    public function someonePurchasedYourProduct($orderID, $productID){
        if ( empty(Firebase::getDeviceToken()) ){
            return false;
        }
        
        $this->push(
            array(
                'body' => str_replace(array(
                    '%orderID%',
                    '%postTitle%'
                ), array(
                    $orderID,
                    get_the_title($productID),
                ), Firebase::getMessage())
            )
        );
        
        Firebase::resetInfo();
    }
    
    /**
     * @param               $aUserIDs
     * @param               $message
     * @param \WP_Post|null $oPost
     */
    public function sendPushNotificationDirectly($aUserIDs, $message, \WP_Post $oPost = null)
    {
        $aInfo = array(
            'body' => $message,
            'sound' => 'default'
        );
        
        if (!empty($aPost)) {
            $aInfo['data'] = $oPost->post_type == 'event' ? $this->eventScreenSkeleton($oPost) :
                $this->listingScreenSkeleton($oPost);
        }
        
        $aBuildMsg = [];
        $hasDeviceToken = false;
        foreach ($aUserIDs as $userID){
            $deviceToken = Firebase::focusGetDeviceToken($userID);
            if (!empty($deviceToken)) {
                $aInfo['to'] = $deviceToken;
                $aBuildMsg[] = $aInfo;
                $hasDeviceToken = true;
            }
        }
        
        if ($hasDeviceToken) {
            $this->pushArray($aBuildMsg);
        }
        
        Firebase::resetInfo();
    }
    
    public function someoneLeftARatingOnYourProduct($metaID, $commentID, $metaKey){
        if ( empty(Firebase::getDeviceToken()) ){
            return false;
        }
        
        $oComment = get_comment( $commentID );
        
        $this->push(
            array(
                'body' => str_replace(array(
                    '%postTitle%',
                    '%rating%',
                    '%reviewExcerpt%',
                    '%review%'
                ), array(
                    get_the_title($oComment->comment_post_ID),
                    get_comment_meta($commentID, 'rating', true),
                    \Wiloke::contentLimit(50, $oComment, true, $oComment->comment_content),
                    $oComment->comment_content
                ), Firebase::getMessage())
            )
        );
        Firebase::resetInfo();
    }
    
    public function yourClaimHasBeenApproved($claimerID, $listingID){
        if ( empty(Firebase::getDeviceToken()) ){
            return false;
        }
        
        $this->push(
            array(
                'body' => Firebase::getMessage(),
                'data' => get_post_type($listingID) == 'event' ? $this->eventScreenSkeleton(get_post($listingID)) : $this->listingScreenSkeleton(get_post($listingID))
            ),
            $claimerID,
            $listingID
        );
        
        Firebase::resetInfo();
    }
    
    
    public function someoneSendAMessageToYou($receiverID, $senderID, $msg){
        if ( !Firebase::isFirebaseEnable() ){
            return false;
        }
        
        if ( !Firebase::isCustomerEnable('privateMessages', $receiverID) ){
            Firebase::resetInfo();
            return false;
        }
        
        if ( empty(Firebase::getDeviceToken()) ){
            Firebase::resetInfo();
            return false;
        }
        
        $this->push(
            array(
                'body' => str_replace(array(
                    '%senderName%',
                    '%message%'
                ), array(
                    User::getField('display_name', $senderID),
                    $msg
                ), Firebase::getMessage()),
                'data' => array(
                    'screen' => 'SendMessageScreen',
                    'userID' => $senderID,
                    'displayName' => User::getField('display_name', $senderID)
                )
            )
        );
        
        Firebase::resetInfo();
    }
    
    public function ajaxObserverSendMessage($aInfo)
    {
        $this->someoneSendAMessageToYou($aInfo['chattedWithId'], User::getCurrentUserID(), $aInfo['msg']);
    }
    
    public function sendWelcomeMessage($receiverID, $senderID, $msg){
        $deviceToken = Firebase::focusGetDeviceToken($receiverID);
        if ( empty($deviceToken) ){
            return false;
        }
        self::someoneSendAMessageToYou($receiverID, $senderID, $msg);
    }
    
    public function productPublished($postID, $oPostAfter, $oPostBefore){
        if ( empty(Firebase::getDeviceToken()) ){
            return false;
        }
        
        $this->push(
            array(
                'body' => Firebase::getMessage()
            ),
            $oPostAfter->post_author,
            $postID
        );
        
        Firebase::resetInfo();
    }
    
    private function listingScreenSkeleton($oListing){
        return array(
            'screen' => 'ListingDetailScreen',
            'id'     => $oListing->ID,
            'name'   => $oListing->post_title,
            'tagline'=> GetSettings::getTagLine($oListing),
            'link'   => get_permalink($oListing->ID),
            'author' => array(
                'ID' => $oListing->post_author,
                'avatar' => User::getAvatar($oListing->post_author),
                'displayName' => User::getField('display_name', $oListing->post_author)
            ),
            'image' => GetSettings::getFeaturedImg($oListing->ID, 'thumbnail'),
            'logo'  => GetSettings::getLogo($oListing->ID)
        );
    }
    
    private function eventScreenSkeleton($oEvent){
        return array(
            'screen'    => 'EventDetailScreen',
            'id'        => $oEvent->ID,
            'link'      => get_permalink($oEvent->ID),
            'name'      => $oEvent->post_title,
            'address'   => GetSettings::getAddress($oEvent->ID, true),
            'hosted'    => GetSettings::getEventHostedByName($oEvent->ID),
            'interested'=> FavoriteStatistic::countFavorites($oEvent->ID),
            'image'     => GetSettings::getFeaturedImg($oEvent->ID, 'thumbnail')
        );
    }
    
    public function startSendNotificationToFollowers($oPost, $aFollowers){
        if ( empty($aFollowers) ){
            return false;
        }
        
        $aBuildMessages = array();
        
        $msg = str_replace(
            array(
                '%userName%',
                '%postTitle%',
                '%postExcerpt%'
            ),
            array(
                User::getField('display_name', $oPost->post_author),
                $oPost->post_title,
                \Wiloke::contentLimit(60, $oPost, true, $oPost->post_content)
            ),
            Firebase::getMessage()
        );
        
        foreach ($aFollowers as $aFollow){
            $firebaseID = FirebaseDB::getFirebaseID($aFollow['followerID']);
            if ( $firebaseID ){
                if ( $deviceToken = FirebaseDeviceToken::getDeviceToken($aFollow['followerID'], $firebaseID) )
                    $aBuildMessages = array(
                        'to'    => $deviceToken,
                        'sound' => 'default',
                        'body'  => $msg
                    );
            }
        }
        if ( !empty($aBuildMessages) ){
            $this->pushArray($aBuildMessages);
        }
        
        Firebase::resetInfo();
    }
    
    public function postChanged($oPostAfter, $oPostBefore){
        if ( empty(Firebase::getDeviceToken()) ){
            return false;
        }
        $aMessage = array(
            'body' => str_replace(array(
                '%postTitle%',
                '%beforeStatus%',
                '%afterStatus%'
            ), array(
                $oPostAfter->post_title,
                $oPostBefore->post_status,
                $oPostAfter->post_status
            ), Firebase::getMessage())
        );
        
        if ( $oPostAfter->post_status == 'publish' ){
            $aMessage['data'] = $oPostAfter->post_type == 'event' ? $this->eventScreenSkeleton($oPostAfter) : $this->listingScreenSkeleton($oPostAfter);
        }
        
        $this->push($aMessage);
        Firebase::resetInfo();
    }
    
    public function someoneLeftACommentOnYourSite($commentID, $commenterID, $eventID){
        if ( empty(Firebase::getDeviceToken()) ){
            return false;
        }
        
        $oPost = get_post($commentID);
        $this->push(array(
            'body' => str_replace(array(
                '%commentExcerpt%',
                '%comment%'
            ), array(
                \Wiloke::contentLimit(50, $oPost, true, $oPost->post_content),
                $oPost->post_content
            ), Firebase::getMessage()),
            'data' => array(
                'screen' => 'EventCommentDiscussionScreen',
                'item' => $this->eventCommentItem(get_post($eventID)),
                'autoFocus' => true
            )
        ), $commenterID, $eventID);
        
        Firebase::resetInfo();
        
    }
    
    public function someoneFollowedYou($followerID, $authorID){
        if ( empty(Firebase::getDeviceToken()) ){
            return false;
        }
        
        $msg = str_replace('%userName%', User::getField('display_name', $followerID), Firebase::getMessage());
        $this->push(array(
            'body' => $msg
        ));
        
        Firebase::resetInfo();
    }
    
    public function someLeftADiscussionOnYourReview($discussionID, $reviewID){
        if ( empty(Firebase::getDeviceToken()) ){
            return false;
        }
        
        $postAuthor = get_post_field('post_author', $reviewID);
        $oPost = get_post($discussionID);
        
        $listingID = wp_get_post_parent_id($reviewID);
        $postType = get_post_field('post_type', $listingID);
        $aDetails = GetSettings::getOptions(General::getReviewKey('details', $postType));
        
        $this->push(array(
            'body' => str_replace(array(
                '%reviewExcerpt%',
                '%review%'
            ), array(
                \Wiloke::contentLimit(50, $oPost),
                $oPost->post_content
            ), Firebase::getMessage()),
            'data' => array(
                'screen' => 'CommentListingScreen',
                'id'     => $listingID,
                'key'    => 'reviews',
                'item'   => $this->getReviewItem(get_post($listingID), $listingID, $aDetails),
                'autoFocus' => true,
                'mode'  => ReviewController::getMode($postType)
            )
        ), $postAuthor, $reviewID);
        Firebase::resetInfo();
    }
    
    public function someoneLeftAReviewOnYourSite($reviewID, $parentID, $reviewerID){
        if ( empty(Firebase::getDeviceToken()) ){
            return false;
        }
        
        $postAuthor = get_post_field('post_author', $parentID);
        $postType = get_post_field('post_type', $parentID);
        $this->msg = $this->generalReplacements(Firebase::getMessage(), $reviewerID, $parentID);
        $aDetails = GetSettings::getOptions(General::getReviewKey('details', $postType));
        
        $this->push(array(
            'body' => str_replace(array(
                '%reviewExcerpt%',
                '%averageRating%'
            ), array(
                \Wiloke::contentLimit(50, get_post($reviewID)),
                ReviewMetaModel::getAverageReviewsItem($reviewID)
            ), Firebase::getMessage()),
            'data' => array(
                'screen' => 'CommentListingScreen',
                'id'     => $parentID,
                'key'    => 'reviews',
                'item'   => $this->getReviewItem(get_post($parentID), $parentID, $aDetails),
                'autoFocus' => true,
                'mode'  => ReviewController::getMode($postType)
            )
        ), $postAuthor, $reviewID);
        Firebase::resetInfo();
    }
}
