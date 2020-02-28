<?php

namespace WILCITY_APP\Controllers\Firebase;

use Kreait\Firebase\Database;
use WILCITY_APP\Database\FirebaseDB;
use WILCITY_APP\Database\FirebaseMsgDB;
use WILCITY_APP\Database\FirebaseUser;
use WilokeListingTools\Controllers\DashboardController;
use WilokeListingTools\Framework\Helpers\Firebase;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Frontend\User;

class MessageController extends Controller
{
    private $db;
    
    public function __construct()
    {
        add_filter('wilcity/filter/submit-new-message-middleware', [$this, 'addMiddlewareToSubmitNewMessage'], 1);
        add_filter('wilcity/filter/submit-new-message-middleware-options', [$this, 'addOptionsToMiddleWare'], 1);
        add_action('wilcity/action/received-message', [$this, 'updateLatestChat'], 10, 3);
        add_filter('wilcity/filter/firebase/delete/msg', [$this, 'deleteMsg'], 10, 5);
        add_filter('wilcity/filter/firebase/delete-msgs-chat-with', [$this, 'deleteMsgWithChatWith'], 10, 2);
        add_action('wilcity/wiloke-listing-tools/update-read-message-status', [$this, 'updateReadStatus']);
        
        add_action('wilcity/action/send-welcome-message', [$this, 'sendWelcomeMessage'], 10, 3);
        add_action('wp_ajax_build_chat_room', [$this, 'ajaxBuildChatRoom']);
        add_action('wp_ajax_wilcity_fix_chat_room_key', [$this, 'fixChatRoomKey']);
//        add_action('init', [$this, 'testUpdateFirebase']);
    }
    
    private function findMyPartnerID($aParseChatKey)
    {
        foreach ($aParseChatKey as $userID) {
            if (!empty($userID) && $userID != get_current_user_id()) {
                return $userID;
            }
        }
        
        return false;
    }
    
    public function fixChatRoomKey()
    {
        if (!Firebase::isFirebaseEnable()) {
            wp_send_json_error();
        }
        
        check_ajax_referer('wilSecurity', 'security');
        
        if (!isset($_POST['chatKey']) || empty($_POST['chatKey'])) {
            wp_send_json_error();
        }
        
        $chatKey = trim($_POST['chatKey']);
        $myID = get_current_user_id();
        
        $aParseChatKey = explode('___', $chatKey);
        $partnerID = $this->findMyPartnerID($aParseChatKey);
        
        if (empty($partnerID)) {
            wp_send_json_error();
        }
        
        if ($_POST['tested']['fUser'] === 'false') {
            $_POST['tested']['fUser'] = '';
        }
        
        if ($_POST['tested']['sUser'] === 'false') {
            $_POST['tested']['sUser'] = '';
        }
        
        if (!in_array($myID, $aParseChatKey)) {
            wp_send_json_error();
        }
        
        $aReference = FirebaseMsgDB::getChatRoom($chatKey);
        $myFireaseID = FirebaseDB::getFirebaseID(get_current_user_id());
        $partnerFirebaseID = FirebaseDB::getFirebaseID($partnerID);
        $oReference = FirebaseDB::getDB()->getReference('messages/chats/'.$chatKey.'');
        
        if (empty($_POST['tested']['fUser']) && empty($_POST['tested']['sUser'])) {
            if (!empty($aReference) && (!empty($aReference['fUser']) || !empty($aReference['sUser']))) {
                wp_send_json_error();
            }
            
            try {
                if (!empty($myFireaseID)) {
                    $oReference->update(
                        [
                            'fUser' => $myFireaseID
                        ]
                    );
                }
                
                if (!empty($partnerFirebaseID)) {
                    $oReference->update(
                        [
                            'sUser' => $partnerFirebaseID
                        ]
                    );
                }
                
            } catch (\Exception $e) {
                return false;
            }
        } else {
            if (empty($_POST['tested']['fUser'])) {
                if ($myFireaseID == $aReference['sUser']) {
                    $oReference->update(
                        [
                            'fUser' => $partnerFirebaseID
                        ]
                    );
                } else {
                    $oReference->update(
                        [
                            'fUser' => $myFireaseID
                        ]
                    );
                }
            } else if (empty($_POST['tested']['sUser'])) {
                if ($myFireaseID == $aReference['fUser']) {
                    $oReference->update(
                        [
                            'sUser' => $partnerFirebaseID
                        ]
                    );
                } else {
                    $oReference->update(
                        [
                            'sUser' => $myFireaseID
                        ]
                    );
                }
            }
        }
    }
    
    public function updateReadStatus($senderID)
    {
        FirebaseMsgDB::updateReadMessageStatus($senderID);
    }
    
    public function testUpdateFirebase()
    {
        if (!isset($_REQUEST['testfirebase']) || $_REQUEST['testfirebase'] !== 'yes') {
            return false;
        }
        
        $aUsers   = get_users([
            'number' => 30
        ]);
        
        $myUserId = get_current_user_id();
        foreach ($aUsers as $oUser) {
            $this->db = null;
            if ($oUser->ID == $myUserId) {
                continue;
            }
            
            if ($this->buildChatRoom($myUserId, $oUser->ID)) {
                $msg = 'This is my test '.$oUser->ID;
                $this->updateMessageToFirebase($myUserId, $oUser->ID, $msg);
                $this->updateLatestChat($myUserId, $oUser->ID, $msg);
            }
        }
    }
    
    public function testUpdateMessage()
    {
        if (!is_admin()) {
            return false;
        }
        for ($i = 0; $i < 100; $i++) {
            $this->referMsgDB(22, 6);
            $this->db->getChild('lists')->push([
                'message'   => 'This is test '.$i,
                'userID'    => 6,
                'timestamp' => Database::SERVER_TIMESTAMP
            ])
            ;
        }
    }
    
    // Delete message on the both Receiver and Sender
    private function deleteLatestMessage($chatWithID, $userID)
    {
        $aLatestMsg = FirebaseMsgDB::getLatestMessageIDByUserID($chatWithID);
        
        if (!empty($aLatestMsg)) {
            $aKeys = array_keys($aLatestMsg);
            FirebaseMsgDB::deleteLatestMessage($aKeys[0]);
        }
        
        $aLatestMsg = FirebaseMsgDB::getLatestMessageIDByUserID($userID, $chatWithID);
        
        if (!empty($aLatestMsg)) {
            $aKeys = array_keys($aLatestMsg);
            FirebaseMsgDB::deleteLatestMessage($aKeys[0], $chatWithID);
        }
    }
    
    public function deleteMsgWithChatWith($status, $chatWithID)
    {
        $userID    = User::getCurrentUserID();
        $chatRoom  = '___'.$chatWithID.'___'.$userID.'___';
        $aChatRoom = FirebaseMsgDB::getChatRoom($chatRoom);
        
        if (empty($aChatRoom)) {
            $chatRoom  = '___'.$userID.'___'.$chatWithID.'___';
            $aChatRoom = FirebaseMsgDB::getChatRoom($chatRoom);
            $this->deleteLatestMessage($chatWithID, $userID);
            if (empty($aChatRoom)) {
                return false;
            } else {
                FirebaseMsgDB::deleteChatRoom($chatRoom);
            }
        } else {
            FirebaseMsgDB::deleteChatRoom($chatRoom);
            $this->deleteLatestMessage($chatWithID, $userID);
        }
        
        return true;
    }
    
    public function deleteMsg($status, $chatRoom, $msgID, $senderID, $receiverID)
    {
        $aGetMsg = FirebaseMsgDB::getMsgByID($chatRoom, $msgID);
        if (empty($aGetMsg) || $aGetMsg['userID'] != User::getCurrentUserID()) {
            return false;
        }
        FirebaseMsgDB::deleteMsg($chatRoom, $msgID);
        $currentMsg = FirebaseMsgDB::getChatRoom($chatRoom);
        if (empty($currentMsg)) {
            $aLatestMsg = FirebaseMsgDB::getLatestMessageIDByUserID($senderID, $receiverID);
            if (!empty($aLatestMsg)) {
                $aKeys = array_keys($aLatestMsg);
                FirebaseMsgDB::deleteLatestMessage($aKeys[0], $receiverID);
            }
            
            $aLatestMsg = FirebaseMsgDB::getLatestMessageIDByUserID($receiverID, $senderID);
            if (!empty($aLatestMsg)) {
                $aKeys = array_keys($aLatestMsg);
                FirebaseMsgDB::deleteLatestMessage($aKeys[0], $senderID);
            }
        }
        
        return true;
    }
    
    private function buildUserId($user1, $user2)
    {
        return '___'.$user1.'___'.$user2.'___';
    }
    
    private function referMsgDB($user1, $user2)
    {
        if (empty($this->db)) {
            $this->db = FirebaseDB::getDB()->getReference('messages/chats/'.$this->buildUserId($user1, $user2));
            if ($this->db->getValue() === null) {
                $this->db = FirebaseDB::getDB()->getReference('messages/chats/'.$this->buildUserId($user2, $user1));
            }
        }
        
        return $this->db;
    }
    
    public function updateLatestChat($receiverID, $senderID, $msg, $activate = true)
    {
        $aStatus = FirebaseMsgDB::updateLatestChat($receiverID, $senderID, $msg, $activate);
        if ($aStatus['status'] == 'error') {
            Message::error($aStatus['msg']);
        }
        FirebaseMsgDB::updateLatestChat($senderID, $receiverID, $msg, null);
        $this->updateMessageToFirebase($receiverID, $senderID, $msg);
    }
    
    public function buildReference($senderID, $receiverID)
    {
        return $this->referMsgDB($senderID, $receiverID);
    }
    
    public function updateMessageToFirebase($receiverID, $senderID, $msg)
    {
        $this->referMsgDB($receiverID, $senderID);
        
        $this->db->getChild('lists')->push([
            'message'   => $msg,
            'userID'    => $senderID,
            'timestamp' => Database::SERVER_TIMESTAMP
        ])
        ;
        
        if (!$this->db->getChild('fUser')->getSnapshot()->exists()) {
            $this->db->update([
                'fUser' => FirebaseUser::getFirebaseID(),
                'sUser' => FirebaseDB::getFirebaseID($receiverID)
            ]);
        }
        /*
         * Hooked WilokeListingTools\Controllers\EmailController@sendMessageToEmail 10
         */
        do_action('wilcity/action/after-sent-message', $receiverID, $senderID, $msg);
        
        return true;
    }
    
    public function sendWelcomeMessage($receiverID, $senderID, $message)
    {
        $status = $this->buildChatRoom($receiverID, $senderID);
        
        if ($status) {
            $this->updateMessageToFirebase($receiverID, $senderID, $message);
            FirebaseMsgDB::updateLatestChat($receiverID, $senderID, $message, true);
        }
    }
    
    public function addOptionsToMiddleWare($aOptions)
    {
        $aOptions['oChatRef'] = $this->referMsgDB($aOptions['receiveID'], User::getCurrentUserID());
        
        return $aOptions;
    }
    
    public function addMiddlewareToSubmitNewMessage($aMiddleware)
    {
        $aMiddleware[] = 'isLoggedInToFirebase';
        $aMiddleware[] = 'verifyFirebaseChat';
        
        return $aMiddleware;
    }
    
    private function buildChatRoom($receiverID, $senderID)
    {
        $this->referMsgDB($receiverID, $senderID);
        if (!$this->db->getChild('fUser')->getSnapshot()->exists()) {
            $sFirebaseID = FirebaseDB::getFirebaseID($receiverID);
            if (empty($sFirebaseID)) {
                $status = apply_filters(
                    'wilcity/create-firebase-account',
                    User::getField('user_email', $receiverID),
                    User::getField('user_pass', $receiverID)
                );
                
                if (!$status) {
                    return false;
                }
            }
            
            if (empty(FirebaseUser::getFirebaseID($senderID))) {
                $status = apply_filters(
                    'wilcity/create-firebase-account',
                    User::getField('user_email', $senderID),
                    User::getField('user_pass', $senderID)
                );
                
                if (!$status) {
                    return false;
                }
            }
            
            $this->db->update([
                'fUser' => FirebaseUser::getFirebaseID($senderID),
                'sUser' => FirebaseDB::getFirebaseID($receiverID)
            ]);
            
            return true;
        }
        
        return false;
    }
    
    public function ajaxBuildChatRoom()
    {
        $receiverID = $_POST['chattingWithId'];
        $status     = $this->buildChatRoom($receiverID, get_current_user_id());
        
        if (!$status) {
            wp_send_json_error();
        }
        
        wp_send_json_success();
    }
}
