<?php

namespace WILCITY_APP\Database;

use Kreait\Firebase\Database;
use WilokeListingTools\Frontend\User;

class FirebaseMsgDB
{
    private static function buildKey($userID)
    {
        return 'messages/users/___'.$userID.'___';
    }

    public static function deleteMsg($chatRoom, $msgID)
    {
        $oReference = FirebaseDB::getDB()->getReference('messages/chats/'.$chatRoom.'/lists');
        $oReference->update(
            [
                $msgID => null
            ]
        );
    }

    public static function deleteChatRoom($chatRoom)
    {
        $oReference = FirebaseDB::getDB()->getReference('messages/chats');
        $oReference->update(
            [
                $chatRoom => null
            ]
        );
    }

    public static function getNotificationStatus($userID, $notificationKey)
    {
        return FirebaseDeviceToken::getNotificationStatus($userID, $notificationKey);
    }

    public static function getLatestMessageIDByUserID($senderID, $receiverID = null)
    {
        $receiverID = !empty($receiverID) ? abs($receiverID) : User::getCurrentUserID();

        return FirebaseDB::getDB()
                         ->getReference(self::buildKey($receiverID))
                         ->orderByChild('userID')
                         ->equalTo(abs($senderID))
                         ->getSnapshot()
                         ->getValue()
            ;
    }

    public static function deleteLatestMessage($key, $authorID = null)
    {
        $authorID   = empty($authorID) ? User::getCurrentUserID() : abs($authorID);
        $oReference = FirebaseDB::getDB()->getReference(self::buildKey($authorID));
        $oReference->update(
            [
                $key => null
            ]
        );
    }

    public static function getChatRoom($chatRoom)
    {
        $oReference = FirebaseDB::getDB()->getReference('messages/chats/'.$chatRoom);
        $oSnapshot  = $oReference->getSnapshot();

        return $oSnapshot->getValue();
    }

    public static function getMsgByID($chatRoom, $msgID)
    {
        $oReference = FirebaseDB::getDB()->getReference('messages/chats/'.$chatRoom.'/lists/'.$msgID);
        $oSnapshot  = $oReference->getSnapshot();

        return $oSnapshot->getValue();
    }

    public static function getMessageUserChatKey($senderID, $receiverID)
    {
        $aValue = FirebaseDB::getDB()
                            ->getReference(self::buildKey($receiverID))
                            ->orderByChild('userID')
                            ->equalTo(abs($senderID))
                            ->getSnapshot()
                            ->getValue()
        ;

        if (!empty($aValue)) {
            $aKey = array_keys($aValue);

            return $aKey[0];
        }

        return false;
    }

    public static function updateReadMessageStatus($senderID)
    {
        $aValue = FirebaseDB::getDB()
                            ->getReference(self::buildKey(User::getCurrentUserID()))
                            ->orderByChild('userID')
                            ->equalTo(abs($senderID))
                            ->getSnapshot()
                            ->getValue()
        ;
        if (!empty($aValue)) {
            $aChatKey = array_keys($aValue);

            FirebaseDB::getDB()->getReference(self::buildKey(User::getCurrentUserID()))->update([
                $aChatKey[0].'/new' => null
            ])
            ;
        }
    }

    public static function updateLatestChat($receiverID, $senderID, $message, $activate = true)
    {
        $oReference = FirebaseDB::getDB()->getReference(self::buildKey($receiverID));

        // Check whether message is existed or not
        try {
            $aChat = $oReference->orderByChild('userID')->equalTo(abs($senderID))->getSnapshot()->getValue();
            if (empty($aChat)) {
                $oReference->push(
                    [
                        'active'      => false,
                        'message'     => $message,
                        'new'         => $activate,
                        'timestamp'   => Database::SERVER_TIMESTAMP,
                        'userID'      => abs($senderID),
                        'displayName' => User::getField('display_name', $senderID),
                        'avatar'      => User::getAvatar($senderID)
                    ]
                );
            } else {
                $aChatKey = array_keys($aChat);
                FirebaseDB::getDB()->getReference(self::buildKey($receiverID))->update([
                    $aChatKey[0].'/active'      => false,
                    $aChatKey[0].'/message'     => $message,
                    $aChatKey[0].'/new'         => $activate,
                    $aChatKey[0].'/timestamp'   => Database::SERVER_TIMESTAMP,
                    $aChatKey[0].'/userID'      => abs($senderID),
                    $aChatKey[0].'/displayName' => User::getField('display_name', $senderID),
                    $aChatKey[0].'/avatar'      => User::getAvatar($senderID)
                ])
                ;
            }

            return ['status' => 'success'];
        } catch (\Exception $oMsg) {
            $errMsg = $oMsg->getMessage();
            if (strpos($errMsg, 'Index not defined') !== false) {
                $errMsg = 'There is an error in your Firebase setting. Please read and follow <a target="_blank" href="https://documentation.wilcity.com/knowledgebase/notification-settings/">Notification Settings</a> -> Step 2: Creating Firebase Database -> 6 to resolve this bug.';
            }

            return [
                'msg'    => $errMsg,
                'status' => 'error'
            ];
        }
    }
}
