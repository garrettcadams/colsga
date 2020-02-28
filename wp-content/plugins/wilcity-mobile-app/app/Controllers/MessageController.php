<?php

namespace WILCITY_APP\Controllers;


use WilokeListingTools\Controllers\NotificationsController as ThemeNotificationController;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\MessageModel;
use WilokeListingTools\Models\NotificationsModel;
use \WilokeListingTools\Controllers\MessageController as ThemeMessageController;

class MessageController {
	use VerifyToken;
	use JsonSkeleton;
	use ParsePost;

	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/get-authors-chatted', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'getAuthorsChatted' )
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/get-author-messages', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'getAuthorMessages' )
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/delete-message', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'deleteMessage' )
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/delete-author-chatted', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'deleteAuthorChatted' )
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/send-message', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'sendMessage' )
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/count-new-messages', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'countNewMessages' )
			) );
		} );
	}

	public function countNewMessages(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}
		$oToken->getUserID();

		$total = MessageModel::countMessages($oToken->userID);
		return array(
			'status' => 'success',
			'count'  => abs($total)
		);
	}

	public function sendMessage(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}

		$oToken->getUserID();
		$aData = $this->parsePost();

		if ( !isset($aData['chatFriendID']) || empty($aData['chatFriendID']) ){
			return array(
				'status' => 'error',
				'msg'    => 'chatFriendIDIsRequired'
			);
		}

		if ( !isset($aData['content']) || empty($aData['content']) ){
			return array(
				'status' => 'error',
				'msg'    => 'messageContentIsRequired'
			);
		}

		if ( !User::userIDExists($aData['chatFriendID']) ){
			return array(
				'status' => 'error',
				'msg'    => 'userDoesNotExists'
			);
		}

		$msgID = MessageModel::insertNewMessage($aData['chatFriendID'], $aData['content']);

		if ( !$msgID ){
			return array(
				'status' => 'error',
				'msg'   => 'couldNotSendMessage'
			);
		}else{
			return array(
				'status' => 'success',
				'msg'   => ''
			);
		}
	}

	public function deleteAuthorChatted(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}
		$oToken->getUserID();

		$aData = $this->parsePost();
		if ( !isset($aData['chatFriendID']) || empty($aData['chatFriendID']) ){
			return array(
				'status' => 'error',
				'msg'    => 'authorIDIsRequired'
			);
		}

		$status = MessageModel::deleteChatRoom($_POST['chatFriendID']);
		if ( $status ){
			return array(
				'status' => 'error',
				'msg'    => 'weCouldNotDeleteAuthorMessage'
			);
		}else{
			return array(
				'status' => 'success',
				'msg'    => 'messageHasBeenDelete'
			);
		}
	}

	public function deleteMessage(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}
		$oToken->getUserID();

		$aData = $this->parsePost();
		if ( !isset($aData['ID']) || empty($aData['ID']) ){
			return array(
				'status' => 'error',
				'msg'    => 'msgIDIsRequired'
			);
		}

		$status = MessageModel::deleteMessageByCurrentID($_POST['ID']);
		if ( $status ){
			return array(
				'status' => 'error',
				'msg'    => 'weCouldNotDeleteMessage'
			);
		}else{
			return array(
				'status' => 'success',
				'msg'    => 'messageHasBeenDelete'
			);
		}
	}

	protected static function aParseExcludeIDs($exclude){
		if ( empty($exclude) ){
			return array();
		}

		$aExcludes = !empty($exclude) ? explode(',', $exclude) : $exclude;
		if ( empty($aExcludes) ){
			return array();
		}

		return array_map(function($userID){
			return abs($userID);
		}, $aExcludes);
	}

	public function buildChatResult($aData){
		$diffInMinutes = Time::dateDiff(strtotime($aData['messageDateUTC']), current_time('timestamp', 1), 'minute');

		if ( $diffInMinutes < 60  ){
			if ( empty($diffInMinutes) ){
				$at = 'aFewSecondAgo';
			}else{
				$at = str_replace('%s', $diffInMinutes, wilcityAppGetLanguageFiles('xMinutesAgo'));
			}
		}else{
			$diffInHours = Time::dateDiff(strtotime($aData['messageDateUTC']), current_time('timestamp', 1), 'hour');
			if ( $diffInHours < 24 ){
				$at = str_replace('%s', $diffInMinutes, wilcityAppGetLanguageFiles('xHoursAgo'));
			}elseif (Time::isDateInThisWeek(strtotime($aData['messageDate']))){
				$at = date_i18n('l', strtotime($aData['messageDate']));
			}else{
				$at = date_i18n(get_option('date_format'), strtotime($aData['messageDate']));
			}
		}

		$aExcludes[] = $aData['ID'];

		return array(
			'oProfile' => $this->getUserProfile($aData['messageAuthorID'], false),
			'oMessage' => array(
				'at' => $at,
				'content' => $aData['messageContent']
			),
			'aExcludes' => $aExcludes
		);
	}

	public function getAuthorMessages(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}
		$oToken->getUserID();

		if ( !isset($_GET['chatFriendID']) || empty($_GET['chatFriendID']) ){
			return array(
				'status' => 'error',
				'msg'    => 'noMessage'
			);
		}

		$chatFiendID = $_GET['chatFriendID'];

		$aExcludes = array();
		if ( isset($_GET['excludes']) ){
			$aExcludes = self::aParseExcludeIDs($_GET['excludes']);
		}
		if ( isset($_GET['isFetchLatestChat']) ){
			$aRawResults = MessageModel::getNewestChat($oToken->userID, $chatFiendID, $aExcludes);
		}else{
			$aRawResults = MessageModel::getMyChat($oToken->userID, $chatFiendID, $aExcludes);
		}

		if ( empty($aRawResults) ){
			return array(
				'status' => 'error',
				'msg'    => 'noMessage'
			);
		}

		$aResults = array();
		foreach ($aRawResults as $aData){
			$aResults[] = $this->buildChatResult($aData);
		}

		return array(
			'status'    => 'success',
			'oResults'  => $aResults
		);
	}

	public function getAuthorsChatted(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}
		$oToken->getUserID();

		$postsPerPage = isset($_GET['postsPerPage']) ? $_GET['postsPerPage'] : 10;

		$aExcludes = isset($_GET['excludes']) ? self::aParseExcludeIDs($_GET['excludes']) : '';

		$aRawResults = MessageModel::getMessageAuthors($oToken->userID, $aExcludes, $postsPerPage);

		if ( empty($aRawResults) ){
			if ( empty($aExcludes) ){
				return array(
					'status' => 'error',
					'msg'    => 'noMessage'
				);
			}

			return array(
				'status' => 'error',
				'msg'    => 'fetchedAllMessages'
			);
		}

		$aResults = array();
		foreach ($aRawResults as $aData){
			$aResults[] = $this->buildChatResult($aData);
		}

		return array(
			'status'    => 'success',
			'oResults'  => $aResults
		);
	}
}
