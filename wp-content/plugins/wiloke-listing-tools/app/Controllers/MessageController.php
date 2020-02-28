<?php
namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\Firebase;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Frontend\User as WilokeUser;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\MessageModel;
use WilokeListingTools\Models\UserModel;

class MessageController extends Controller
{
    public $aAuthorAvatars = [];
    public $aDisplayName = [];

    public function __construct()
    {
        add_action('wp_ajax_wilcity_fetch_author_messages', [$this, 'fetchAuthorMessages']);
        add_action('wp_ajax_wilcity_search_users', [$this, 'searchUsers']);
        add_action('wp_ajax_wilcity_fetch_chatted_with', [$this, 'fetchChattedWith']);
        add_action('wp_ajax_wilcity_fetch_author_msg_info', [$this, 'fetchAuthorMsgInfo']);
        add_action('wp_ajax_wilcity_submit_new_msg', [$this, 'submitNewMsg']);
        add_action('wp_ajax_wilcity_update_read_message', [$this, 'updateReadMsgStatus']);
        add_action('wilcity/footer/vue-popup-wrapper', [$this, 'printFooterCode']);
        add_action('wp_ajax_wilcity_count_messages_to_me', [$this, 'countMessagesToMe']);

        add_action('wilcity/header/after-menu', [$this, 'messageNotifications'], 15);
        add_action('wp_ajax_wilcity_count_new_messages', [$this, 'countNewMessagesOfAuthor']);
        add_action('wp_ajax_wilcity_reset_new_messages', [$this, 'resetNewMessages']);
        add_action('wp_ajax_wilcity_fetch_list_messages', [$this, 'fetchLatestMessagesOfUser']);

        add_action('user_register', [$this, 'sendWelcomeMessage'], 99);
        add_action('wp_ajax_wilcity_delete_chat_room', [$this, 'deleteChatRoom']);
        add_action('wp_ajax_wilcity_delete_single_message', [$this, 'deleteSingleMessageInfo'], 10);
        add_action('wp_ajax_wilcity_user_display_name_avatar', [$this, 'getUserDisplayNameAndAvatar']);
        add_action('wp_ajax_chat_fetch_user_profile_details', [$this, 'getUserProfileDetails']);
//        add_action('wp_ajax_wilcity_sent_message', [$this, 'sentMessage']);
        add_action('wp_ajax_wilcity_subscribe_chat_room', [$this, 'subscribeChatRoom']);
        add_action('wp_ajax_wilcity_send_message', [$this, 'observerSendMessage']);
    }

    public function observerSendMessage()
    {
        check_ajax_referer('wilSecurity', 'security', 1);
        /**
         * @hooked WilokeListingTools\Controllers\EmailController@ajaxSendMessageToEmail 10
         * @hooked WILCITY_APP\Controllers\Firebase\PushNotificationController@ajaxObserverSendMessage 10
         */
        do_action('wilcity/wiloke-listing-tools/observerSendMessage', $_POST, true); // is ajax or
        // not
    }

    public function sentMessage()
    {
        check_ajax_referer('wilSecurity', 'security');
        do_action('wilcity/action/after-sent-message', $_POST['receiverID'], get_current_user_id(), $_POST['message']);
    }

    public function getUserProfileDetails()
    {
        if (!isset($_GET['userId']) || empty($_GET['userId'])) {
            wp_send_json_error();
        }

        $userID = abs($_GET['userId']);

        $aUserInfo['avatar']          = User::getAvatar($userID);
        $aUserInfo['phone']           = User::getPhone($userID);
        $aUserInfo['website']         = User::getWebsite($userID);
        $aUserInfo['address']         = User::getAddress($userID);
        $aUserInfo['aSocialNetworks'] = User::getSocialNetworks($userID);
        $aUserInfo['position']        = User::getPosition($userID);
        $aUserInfo['displayName']     = User::getField('display_name', $userID);
        $aUserInfo['profileUrl']      = get_author_posts_url($userID);

        wp_send_json_success($aUserInfo);
    }

    public function getUserDisplayNameAndAvatar()
    {
        if (!User::isUserLoggedIn()) {
            wp_send_json_error();
        }

        $aUser['avatar']      = User::getAvatar($_GET['uid']);
        $aUser['displayName'] = User::getField('display_name', $_GET['uid']);

        wp_send_json_success($aUser);
    }

    public function deleteSingleMessageInfo()
    {
        $status = MessageModel::deleteMessageByCurrentID($_POST['ID']);

        if ($status) {
            wp_send_json_success([
                'remove' => $_POST['ID']
            ]);
        } else {
            wp_send_json_error([
                'msg' => esc_html__('Wrong message ID', 'wiloke-listing-tools')
            ]);
        }
    }

    public function deleteChatRoom()
    {
        if (!isset($_POST['chattingWithId']) || empty($_POST['chattingWithId'])) {
            wp_send_json_error(
                [
                    'msg' => esc_html__('This chat room id does not exists', 'wiloke-listing-tools')
                ]
            );
        }
        MessageModel::deleteChatRoom($_POST['chattingWithId']);
        wp_send_json_success();
    }

    public function sendWelcomeMessage($userID)
    {
        $aThemeOptions = \Wiloke::getThemeOptions();
        if (isset($aThemeOptions['welcome_message']) && !empty($aThemeOptions['welcome_message'])) {
            $oFirstSuperAdmin = User::getFirstSuperAdmin();

            if (Firebase::isFirebaseEnable()) {
                do_action('wilcity/action/send-welcome-message', $userID, $oFirstSuperAdmin->ID,
                    $aThemeOptions['welcome_message']);
            } else {
                MessageModel::insertNewMessage($userID, $aThemeOptions['welcome_message'], $oFirstSuperAdmin->ID);
            }
        }
    }

    public function fetchLatestMessagesOfUser()
    {
        $limit      = isset($_POST['limit']) || $_POST['limit'] > 100 ? abs($_POST['limit']) : 20;
        $paged      = isset($_POST['paged']) ? abs($_POST['paged']) : 1;
        $offset     = ($paged - 1) * $limit;
        $receiverID = User::getCurrentUserID();

        $aRawMessages = MessageModel::getLatestMessageOfUser($receiverID, $limit, $offset);
        if (!$aRawMessages) {
            wp_send_json_error([
                'msg' => esc_html__('No Messages', 'wiloke-listing-tools')
            ]);
        }
        $dashboardUrl = GetWilokeSubmission::getField('dashboard_page', true);
        $dashboardUrl .= '#/messages';

        $aMessages = [];

        foreach ($aRawMessages as $oMessage) {
            $aMessages[] = [
                'avatar'      => User::getAvatar($oMessage->messageAuthorID),
                'displayName' => User::getField('display_name', $oMessage->messageAuthorID),
                'message'     => \Wiloke::truncateString($oMessage->messageContent, 40),
                'link'        => $dashboardUrl.'?u='.User::getField('user_login',
                        $oMessage->messageAuthorID).'&id='.$oMessage->messageAuthorID,
                'time'        => Time::timeFromNow(strtotime($oMessage->messageDateUTC), true)
            ];

            MessageModel::resetNewMessages($receiverID);
        }

        wp_send_json_success(
            [
                'aInfo'        => $aMessages,
                'dashboardUrl' => $dashboardUrl
            ]
        );
    }

    public function resetNewMessages()
    {
        $this->middleware(['isUserLoggedIn'], []);
        MessageModel::resetNewMessages(get_current_user_id());
    }

    public function countNewMessagesOfAuthor()
    {
        $this->middleware(['isUserLoggedIn'], []);
        $total = MessageModel::countUnReadMessages(get_current_user_id());
        wp_send_json_success($total);
    }

    public function messageNotifications()
    {
        if (!is_user_logged_in() || !GetWilokeSubmission::isSystemEnable()) {
            return '';
        }
        ?>
        <div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-message-notifications')); ?>"
             class="header_loginItem__oVsmv">
            <message-notifications
                dashboard-url="<?php echo esc_url(GetWilokeSubmission::getField('dashboard_page', true)); ?>"
                user-id="<?php echo absint(User::getCurrentUserID()); ?>"></message-notifications>
        </div>
        <?php
    }

    public function countMessagesToMe()
    {
        $total = MessageModel::countMessages(get_current_user_id());
        if (empty($total)) {
            wp_send_json_error(esc_html__('No alerts or messages at this time', 'wiloke-listing-tools'));
        }
        wp_send_json_success($total);
    }

    public function printFooterCode()
    {
        if (!is_user_logged_in()) {
            return '';
        }

        if (is_singular() || is_author()) :
            global $post;
            $current_id = get_current_user_id();

            if ($current_id == $post->post_author) {
                $oFirstSuperAdmin = User::getFirstSuperAdmin();
                $authorID         = $oFirstSuperAdmin->ID;
            } else {
                $authorID = is_author() ? get_query_var('author') : $post->post_author;
            }
            ?>
            <message-popup receive-id="<?php echo esc_attr($authorID); ?>"
                           display-name="<?php echo esc_attr(WilokeUser::getField('display_name',
                               $authorID)); ?>"></message-popup>
            <?php
        endif;
    }

    public function updateReadMsgStatus()
    {
        if (!isset($_POST['chattingWithId']) || empty($_POST['chattingWithId'])) {
            wp_send_json_error();
        }
        MessageModel::updateReadMessage($_POST['chattingWithId']);
        wp_send_json_success();
    }

    public function getAuthorDisplayName($userID)
    {
        if (!empty($this->aDisplayName[$userID])) {
            return $this->aDisplayName[$userID];
        }

        $this->aDisplayName[$userID] = WilokeUser::getField('display_name', get_current_user_id());

        return $this->aDisplayName[$userID];
    }

    public function getAuthorAvatar($userID)
    {
        if (isset($this->aAuthorAvatars[$userID])) {
            return $this->aAuthorAvatars[$userID];
        }

        $this->aAuthorAvatars[$userID] = WilokeUser::getAvatar($userID);

        return $this->aAuthorAvatars[$userID];
    }

    protected function insertInstantMessage($receiverID)
    {
        $instantMsg = WilokeUser::getInstantMessage($receiverID);
        if (!empty($instantMsg)) {
            MessageModel::insertNewMessage(get_current_user_id(), WilokeUser::getInstantMessage($receiverID),
                $receiverID);
        }
    }

    public function submitNewMsg()
    {
        $this->middleware(apply_filters('wilcity/filter/submit-new-message-middleware',
            ['beforeSubmitMessage', 'isUserLoggedIn']),
            apply_filters('wilcity/filter/submit-new-message-middleware-options', [
                'receiveID' => $_POST['receiveID']
            ]));

        if (!isset($_POST['message']) || empty($_POST['message'])) {
            wp_send_json_error([
                'msg' => esc_html__('Please enter your message', 'wiloke-listing-tools')
            ]);
        }

        $msgID = true;
        if (!Firebase::isFirebaseEnable()) {
            $msgID = MessageModel::insertNewMessage($_POST['receiveID'], $_POST['message']);
        } else {
            do_action('wilcity/action/received-message', $_POST['receiveID'], User::getCurrentUserID(),
                wp_unslash($_POST['message']));
        }

        if (!$msgID) {
            wp_send_json_error([
                'msg' => esc_html__('Oops! We could not send your message, please try it again. ',
                    'wiloke-listing-tools')
            ]);
        } else {
            if (!isset($_POST['isChatting']) && !Firebase::isFirebaseEnable()) {
                $this->insertInstantMessage($_POST['receiveID']);
            }

            $aResponse = [
                'ID'                    => $msgID,
                'message'               => stripslashes($_POST['message']),
                'messageAt'             => '',
                'messageAuthorID'       => User::getCurrentUserID(),
                'avatar'                => $this->getAuthorAvatar(User::getCurrentUserID()),
                'displayName'           => $this->getAuthorDisplayName(User::getCurrentUserID()),
                'messageReceivedSeen'   => 'no',
                'messageUserReceivedID' => $_POST['receiverID']
            ];

            if (isset($_POST['isSentFromPopup'])) {
                $aResponse['instantMessage'] = esc_html__('Thank for contacting us, We will read and reply to you shortly',
                    'wiloke-listing-tools');
            }

            if (!Firebase::isFirebaseEnable()) {
                User::setLastSentMessage($_POST['receiveID']);
            }

            // true mean is chatted via web
            wp_send_json_success($aResponse);
        }
    }

    private function buildChatRoomUserProfiles($chatWith)
    {
        $chatWith          = abs($chatWith);
        $userID            = User::getCurrentUserID();
        $userDisplayName   = $this->getAuthorDisplayName($userID);
        $authorDisplayName = $this->getAuthorDisplayName($chatWith);
        $userAvatar        = $this->getAuthorAvatar($userID);
        $authorAvatar      = $this->getAuthorAvatar($_POST['authorID']);
        $authorProfile     = get_author_posts_url($userID);

        $aCommonResponse = [
            'userAvatar'        => $userAvatar,
            'authorAvatar'      => $authorAvatar,
            'userDisplayName'   => $userDisplayName,
            'authorDisplayName' => $authorDisplayName,
            'authorProfile'     => $authorProfile,
            'userID'            => $userID
        ];

        return $aCommonResponse;
    }

    private function buildChatRoomMessagesSkeleton($aRawResults)
    {
        $aResponse   = [];
        $date        = '';
        $aRawResults = array_reverse($aRawResults);
        foreach ($aRawResults as $key => $aResult) {
            $newDate = date('Y-m-d', strtotime($aResult['messageDate']));
            if ($newDate !== $date) {
                array_push($aResponse, ['breakDate' => $newDate]);
            }

            $aResponse[$key]              = $aResult;
            $aResponse[$key]['messageAt'] = date_i18n(get_option('time_format'), strtotime($aResult['messageDate']));
            $aResponse[$key]['message']   = stripslashes($aResult['messageContent']);
        }

        return $aResponse;
    }

    public function subscribeChatRoom()
    {
        $aErrorTypes = [
            [
                'codeMsg' => 'chat_room_not_exist'
            ],
            [
                'codeMsg' => 'no_new_messages'
            ]
        ];

        if (!isset($_GET['chatWith']) || empty($_GET['chatWith'])) {
            wp_send_json_error(
                $aErrorTypes[0]
            );
        }

        $chatWith = abs($_GET['chatWith']);

        if (empty($chatWith)) {
            wp_send_json_error(
                $aErrorTypes[0]
            );
        }

        $lastMaxID       = $_GET['lastMaxID'];
        $aNewestMessages = MessageModel::getNewestChat(User::getCurrentUserID(), $chatWith, $lastMaxID);

        if (empty($aNewestMessages)) {
            wp_send_json_error(
                $aErrorTypes[1]
            );
        }

        $aResponse = $this->buildChatRoomMessagesSkeleton($aNewestMessages);
        wp_send_json_success([
            'messages' => $aResponse,
            'codeMsg'  => 'new_messages'
        ]);
    }

    public function fetchAuthorMessages()
    {
        $chatWith    = abs($_POST['chattingWithId']);
        $userID      = User::getCurrentUserID();
        $aRawResults = MessageModel::getMyChat($userID, $chatWith);

        if (empty($aRawResults)) {
            wp_send_json_error();
        }

        $aCommonResponse        = $this->buildChatRoomUserProfiles($chatWith);
        $aResponse              = $this->buildChatRoomMessagesSkeleton($aRawResults);
        $aCommonResponse['msg'] = $aResponse;

        if (count($aRawResults) < 10) {
            $aCommonResponse['reachedMaximum'] = 'yes';
            wp_send_json_success($aCommonResponse);
        } else {
            wp_send_json_success($aCommonResponse);
        }
    }

    public function fetchAuthorMsgInfo()
    {
        wp_send_json_success([
            'displayName' => WilokeUser::getField('display_name', $_POST['authorID'])
        ]);
    }

    private function getUserSkeleton($aUser)
    {
        $aLatestMessage = MessageModel::getLatestMessageChattedWith($aUser['messageAuthorID'],
            User::getCurrentUserID());
        $aUser          = wp_parse_args($aLatestMessage, $aUser);

        $aAuthor['userID']          = abs($aUser['messageAuthorID']);
        $aAuthor['displayName']     = $aUser['displayName'];
        $aAuthor['avatar']          = $this->getAuthorAvatar($aUser['messageAuthorID']);
        $aAuthor['phone']           = WilokeUser::getPhone($aUser['messageAuthorID']);
        $aAuthor['position']        = WilokeUser::getPosition($aUser['messageAuthorID']);
        $aAuthor['address']         = WilokeUser::getAddress($aUser['messageAuthorID']);
        $aAuthor['aSocialNetworks'] = WilokeUser::getSocialNetworks($aUser['messageAuthorID']);
        $aAuthor['profileUrl']      = get_author_posts_url($aUser['messageAuthorID']);
        $aAuthor['message']         = '';
        $aAuthor['timestamp']       = '';
        $aAuthor['messageDateUTC']  = $aUser['messageDateUTC'];
        $aAuthor['diff']            = '';

        if (isset($aUser['messageDateUTC'])) {
            $diffInMinutes      = Time::dateDiff(strtotime($aUser['messageDateUTC']), current_time('timestamp', 1),
                'minute');
            $aAuthor['message'] = stripslashes($aUser['messageContent']);

            if ($diffInMinutes < 60) {
                if (empty($diffInMinutes)) {
                    $aAuthor['diff'] = esc_html__('A few seconds ago', 'wiloke-listing-tools');
                } else {
                    $aAuthor['diff'] = sprintf(_n('%s minute ago', '%s minutes ago', $diffInMinutes,
                        'wiloke-listing-tools'), $diffInMinutes);
                }
            } else {
                $diffInHours = Time::dateDiff(strtotime($aUser['messageDateUTC']), current_time('timestamp', 1),
                    'hour');
                if ($diffInHours < 24) {
                    $aAuthor['diff'] = sprintf(_n('%s hour ago', '%s hours ago', $diffInHours,
                        'wiloke-listing-tools'), $diffInHours);
                } elseif (Time::isDateInThisWeek(strtotime($aUser['messageDate']))) {
                    $aAuthor['diff'] = date_i18n('l', strtotime($aUser['messageDate']));
                } else {
                    $aAuthor['diff'] = date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime
                    ($aUser['messageDate']));
                }
            }

            $aAuthor['seen'] = $aLatestMessage['messageReceivedSeen'];
        }

        return $aAuthor;
    }

    public function fetchChattedWith()
    {
        $status = check_ajax_referer('wilSecurity', 'security', false);
        if (!$status) {
            wp_send_json_error();
        }

        $limit = isset($_POST['limit']) && !empty($_POST['limit']) ? abs($_POST['limit']) : 10;

        $aRawUsers = MessageModel::getMessageAuthors(get_current_user_id(),
            isset($_POST['excludes']) ? $_POST['excludes'] : [], $limit);

        if (empty($aRawUsers)) {
            wp_send_json_error();
        }

        $aUsers = [];

        foreach ($aRawUsers as $key => $aUser) {
            $aUsers[$aUser['messageAuthorID']] = $this->getUserSkeleton($aUser);
        }

        uasort($aUsers, function ($aItemA, $aItemB) {
            return strtotime($aItemA['messageDateUTC']) < strtotime($aItemB['messageDateUTC']);
        });

        wp_send_json_success([
            'users' => $aUsers
        ]);
    }

    public function searchUsers()
    {
        $aRawResults = [];
        if (!isset($_POST['s']) || empty($_POST['s'])) {
            wp_send_json_error();
        } else {
            $s           = sanitize_text_field($_POST['s']);
            $aRawResults = MessageModel::searchAuthorMessage($s);
        }

        if (empty($aRawResults)) {
            wp_send_json_error();
        }

        $aAuthors = [];
        foreach ($aRawResults as $key => $aUser) {
            $aAuthors[$aUser['messageAuthorID']] = $this->getUserSkeleton($aUser);
        }

        wp_send_json_success([
            'users'          => $aAuthors,
            'userID'         => get_current_user_id(),
            'reachedMaximum' => 'yes'
        ]);
    }

    public function fetchAuthorsSendMessage()
    {
        $aExcludes = [];

        if (isset($_POST['excludes']) && !empty($_POST['excludes'])) {
            $aExcludes = array_map(function ($userID) {
                return abs($userID);
            }, $_POST['excludes']);
        }

        $aRawResults = MessageModel::getMessageAuthors(get_current_user_id(), $aExcludes);

        if (empty($aRawResults)) {
            wp_send_json_error();
        } else {
            $aAuthors = [];
            foreach ($aRawResults as $key => $aUser) {
                $aExcludes[]                         = $aUser['messageAuthorID'];
                $aAuthors[$aUser['messageAuthorID']] = $this->getUserSkeleton($aUser);
            }

            if (count($aRawResults) >= 10) {
                wp_send_json_success([
                    'users'    => $aAuthors,
                    'userID'   => get_current_user_id(),
                    'excludes' => $aExcludes
                ]);
            } else {
                wp_send_json_success([
                    'users'          => $aAuthors,
                    'userID'         => get_current_user_id(),
                    'reachedMaximum' => 'yes'
                ]);
            }
        }
    }
}
