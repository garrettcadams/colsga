<?php

namespace WilokeListingTools\Models;

use WilokeListingTools\AlterTable\AlterTableMessage;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Frontend\User;

class MessageModel
{
    public static function getMessageAuthors($userID, $aExcludes = [], $limit = 10, $lastMaxId = '')
    {
        global $wpdb;
        $usersTbl = $wpdb->users;
        $msgTbl   = $wpdb->prefix.AlterTableMessage::$tblName;

        if (empty($aExcludes)) {
            $sql =  $wpdb->prepare(
                "SELECT DISTINCT $msgTbl.messageAuthorID, $usersTbl.display_name AS displayName, 
$msgTbl.messageDateUTC FROM 
$msgTbl LEFT JOIN $usersTbl ON ($usersTbl.ID = $msgTbl.messageAuthorID) WHERE $msgTbl.messageUserReceivedID=%d",
                $userID, $limit
            );

            if (!empty($lastMaxId)) {
                $sql .= $wpdb->prepare(
                    " AND $msgTbl.ID > %d",
                    $lastMaxId
                );
            }

            $sql .=  $wpdb->prepare(
                " GROUP BY $msgTbl.messageAuthorID LIMIT %d",
                $limit
            );
            $aRawResults = $wpdb->get_results(
                $sql, ARRAY_A
            );
            return $aRawResults;
        } else {
            $aExcludes = array_map(function ($id) {
                return abs($id);
            }, $aExcludes);

            $sql = $wpdb->prepare(
                "SELECT DISTINCT $msgTbl.messageAuthorID, $usersTbl.display_name AS displayName, $msgTbl.* 
FROM $msgTbl
LEFT JOIN $usersTbl ON ($usersTbl.ID = $msgTbl.messageAuthorID) WHERE $msgTbl.messageUserReceivedID=%d AND $msgTbl.messageAuthorID NOT IN (".implode(',',
                    $aExcludes).")",
                $userID
            );

            if (!empty($lastMaxId)) {
                $sql .= $wpdb->prepare(
                    " AND $msgTbl.ID > %d",
                    $lastMaxId
                );
            }

            $sql .=  $wpdb->prepare(
                " GROUP BY $msgTbl.messageAuthorID ORDER BY $msgTbl.ID DESC LIMIT %d",
                $limit
            );

            $aRawResults = $wpdb->get_results(
                $sql,
                ARRAY_A
            );
        }

        return $aRawResults;
    }

    public static function getLatestMessageChattedWith($senderID, $receiverID)
    {
        global $wpdb;
        $msgTbl = $wpdb->prefix.AlterTableMessage::$tblName;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT messageContent, messageDate, messageDateUTC, messageReceivedSeen FROM $msgTbl WHERE messageAuthorID = %d AND messageUserReceivedID = %d ORDER BY
 ID DESC LIMIT 1",
                $senderID, $receiverID
            ),
            ARRAY_A
        );
    }

    public static function searchAuthorMessage($s, $limit = 10)
    {
        global $wpdb;
        $usersTbl    = $wpdb->users;
        $s           = '%'.$s.'%';
        $aRawResults = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DISTINCT $usersTbl.ID as messageAuthorID, $usersTbl.display_name as displayName FROM $usersTbl  WHERE
($usersTbl.display_name LIKE %s || $usersTbl.user_login LIKE %s ) AND ($usersTbl.ID != %d)
 GROUP BY $usersTbl.ID LIMIT %d",
                $s, $s, get_current_user_id(), $limit
            ), ARRAY_A
        );

        return $aRawResults;
    }

    public static function getMyChat($userID, $chatFriendID, $aExcludes = [])
    {
        global $wpdb;
        $msgTbl = $wpdb->prefix.AlterTableMessage::$tblName;

        if (empty($aExcludes)) {
            $aChat = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $msgTbl WHERE (messageUserReceivedID=%d AND messageAuthorID=%d) OR (messageUserReceivedID=%d AND messageAuthorID=%d) ORDER BY ID DESC LIMIT 10",
                    $userID, $chatFriendID, $chatFriendID, $userID
                ),
                ARRAY_A
            );
        } else {
            $aChat = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $msgTbl WHERE ( (messageUserReceivedID=%d AND messageAuthorID=%d) OR (messageUserReceivedID=%d AND messageAuthorID=%d) ) AND $msgTbl.ID NOT IN (".implode(',',
                        $aExcludes).") ORDER BY ID DESC LIMIT 10",
                    $userID, $chatFriendID, $chatFriendID, $userID
                ),
                ARRAY_A
            );
        }

        return empty($aChat) ? false : $aChat;
    }

    public static function getNewestChat($userID, $chatFriendID, $lastMaxMsgID)
    {
        global $wpdb;
        $msgTbl = $wpdb->prefix.AlterTableMessage::$tblName;

        $sql = $wpdb->prepare(
            "SELECT * FROM $msgTbl WHERE (messageUserReceivedID=%d AND messageAuthorID=%d)",
            $userID, $chatFriendID
        );

        if (!empty($lastMaxMsgID)) {
            $sql = $wpdb->prepare(
                $sql."  AND $msgTbl.ID > %d",
                $lastMaxMsgID
            );
        }

        $sql         .= ' ORDER BY ID DESC LIMIT 10';
        $aRawResults = $wpdb->get_results(
            $sql,
            ARRAY_A
        );

        return empty($aRawResults) ? false : $aRawResults;
    }

    public static function getLatestMessageOfUser($receiverID, $limit = 10, $offset = 0)
    {
        global $wpdb;
        $msgTbl = $wpdb->prefix.AlterTableMessage::$tblName;

        $aMessages = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $msgTbl WHERE messageUserReceivedID=%d ORDER BY ID DESC LIMIT $offset,$limit",
                $receiverID
            ));
        if (empty($aMessages) || is_wp_error($aMessages)) {
            return false;
        }

        return $aMessages;
    }

    public static function resetNewMessages($receiverID)
    {
        global $wpdb;
        $msgTbl = $wpdb->prefix.AlterTableMessage::$tblName;

        return $wpdb->update(
            $msgTbl,
            [
                'messageReceivedSeen' => 'yes'
            ],
            [
                'messageUserReceivedID' => $receiverID,
            ],
            [
                '%s'
            ],
            [
                '%d'
            ]
        );
    }

    public static function updateReadMessage($senderID)
    {
        global $wpdb;
        $msgTbl = $wpdb->prefix.AlterTableMessage::$tblName;
        $wpdb->update(
            $msgTbl,
            [
                'messageReceivedSeen' => 'yes'
            ],
            [
                'messageAuthorID'       => $senderID,
                'messageUserReceivedID' => User::getCurrentUserID(),
            ],
            [
                '%s'
            ],
            [
                '%d',
                '%d'
            ]
        );
    }

    public static function deleteMessageByCurrentID($messageID)
    {
        $currentUserID = User::getCurrentUserID();
        global $wpdb;
        $tableName = $wpdb->prefix.AlterTableMessage::$tblName;

        $id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM $tableName WHERE messageAuthorID=%d AND ID = %d",
            $currentUserID, $messageID
        ));

        if ($id) {
            $wpdb->delete(
                $tableName,
                [
                    'ID' => $id
                ],
                [
                    '%d'
                ]
            );

            return true;
        }

        return false;
    }

    public static function deleteChatRoom($chattingWithId)
    {
        $currentUserID = User::getCurrentUserID();
        global $wpdb;
        $chattingWithId = $wpdb->_real_escape(abs($chattingWithId));
        $tableName      = $wpdb->prefix.AlterTableMessage::$tblName;

        $wpdb->delete(
            $tableName,
            [
                'messageUserReceivedID' => $chattingWithId,
                'messageAuthorID'       => $currentUserID
            ],
            [
                '%d',
                '%d'
            ]
        );

        $wpdb->delete(
            $tableName,
            [
                'messageUserReceivedID' => $currentUserID,
                'messageAuthorID'       => $chattingWithId
            ],
            [
                '%d',
                '%d'
            ]
        );

        return true;
    }

    public static function countMessages($receiveID)
    {
        global $wpdb;
        $msgTbl = $wpdb->prefix.AlterTableMessage::$tblName;

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(ID) FROM $msgTbl WHERE messageUserReceivedID=%d",
            $receiveID
        ));

        return abs($count);
    }

    public static function countUnReadMessages($receiveID)
    {
        global $wpdb;
        $msgTbl = $wpdb->prefix.AlterTableMessage::$tblName;

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(ID) FROM $msgTbl WHERE messageUserReceivedID=%d AND messageReceivedSeen=%s",
            $receiveID, 'no'
        ));

        return abs($count);
    }

    public static function insertNewMessage($receiveID, $msg, $senderID = null)
    {
        global $wpdb;
        $msgTbl   = $wpdb->prefix.AlterTableMessage::$tblName;
        $senderID = empty($senderID) ? User::getCurrentUserID() : $senderID;

        if ($receiveID == $senderID) {
            return false;
        }

        $status = $wpdb->insert(
            $msgTbl,
            [
                'messageUserReceivedID' => $receiveID,
                'messageAuthorID'       => $senderID,
                'messageContent'        => $msg,
                'messageDate'           => Time::mysqlDateTime(),
                'messageDateUTC'        => Time::mysqlDateTime(current_time('timestamp', 1)),
                'messageReceivedSeen'   => 'no'
            ],
            [
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s'
            ]
        );
        if (!$status) {
            return false;
        }

        do_action('wilcity/action/after-sent-message', $receiveID, $senderID, $msg);

        return $wpdb->insert_id;
    }

    public static function getMessage($receiveID)
    {
        global $wpdb;
        $msgTbl = $wpdb->prefix.AlterTableMessage::$tblName;

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $msgTbl WHERE messageAuthorID=%d AND messageUserReceivedID=%d",
            get_current_user_id(), $receiveID
        ), ARRAY_A);
    }

    public static function updateField($fieldID, $fieldName, $val)
    {
        global $wpdb;
        $msgTbl = $wpdb->prefix.AlterTableMessage::$tblName;

        return $wpdb->update(
            $msgTbl,
            [
                $fieldName    => $val,
                'messageDate' => Time::mysqlDateTime()
            ],
            [
                'ID' => $fieldID
            ],
            [
                '%s',
                '%s'
            ],
            [
                '%d'
            ]
        );
    }
}
