<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\AlterTable\AlterTableFollower;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\HTML;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Frontend\User;

class FollowController extends Controller {
	public static $isIamFollowing = null;

	public function __construct() {
		add_action('wp_ajax_wilcity_update_following', array($this, 'updateFollow'));
	}

	public static function toggleFollow(){
		global $wiloke;
		return $wiloke->aThemeOptions['general_toggle_follow'] == 'enable';
	}

	public static function isIamFollowing($authorID){
		if ( !is_user_logged_in() ){
			return false;
		}

		if ( self::$isIamFollowing !== null ){
			return self::$isIamFollowing;
		}

		global $wpdb;
		$followTbl = $wpdb->prefix . AlterTableFollower::$tblName;

		self::$isIamFollowing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT followerID FROM $followTbl WHERE authorID=%d AND followerID=%d",
				$authorID, get_current_user_id()
			)
		);
		return self::$isIamFollowing;
	}

	public static function insertFollower($authorID){
		global $wpdb;
		$followTbl = $wpdb->prefix . AlterTableFollower::$tblName;

		return $wpdb->insert(
			$followTbl,
			array(
				'authorID'   => $authorID,
				'followerID' => get_current_user_id(),
				'date'       => Time::mysqlDateTime()
			),
			array(
				'%d',
				'%d',
				'%s'
			)
		);
	}

	public static function deleteFollower($authorID) {
		global $wpdb;
		$followTbl = $wpdb->prefix . AlterTableFollower::$tblName;

		return $wpdb->delete(
			$followTbl,
			array(
				'authorID'   => $authorID,
				'followerID' => get_current_user_id()
			),
			array(
				'%d',
				'%d'
			)
		);
	}

	public static function countFollowings($authorID=null, $isStyleNumber=false){
		global $wpdb;
		$authorID = empty($authorID) ? get_current_user_id() : $authorID;
		$followTbl = $wpdb->prefix . AlterTableFollower::$tblName;

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(authorID) FROM $followTbl WHERE followerID=%d",
				$authorID
			)
		);

		$total = !$total ? 0 : $total;
		return $isStyleNumber ? HTML::reStyleText($total) : $total;
	}

	public static function countFollowers($authorID=null, $isStyleNumber=false){
		global $wpdb;
		$followTbl = $wpdb->prefix . AlterTableFollower::$tblName;
		$authorID = empty($authorID) ? get_current_user_id() : $authorID;
		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(followerID) FROM $followTbl WHERE authorID=%d",
				$authorID
			)
		);

		$total = !$total ? 0 : $total;
		return $isStyleNumber ? HTML::reStyleText($total) : $total;
	}

	public function updateFollow(){
		if ( !is_user_logged_in() ){
			wp_send_json_error(array(
				'msg' => esc_html__('Please log into your account before following', 'wiloke-listing-tool')
			));
		}

		if ( !isset($_POST['authorID']) || empty($_POST['authorID']) ){
			wp_send_json_error(array(
				'msg' => esc_html__('The author data is required.', 'wiloke-listing-tool')
			));
		}

		if ( get_current_user_id() == $_POST['authorID'] ){
			wp_send_json_error(array(
				'msg' => esc_html__('You can not follow yourself.', 'wiloke-listing-tool')
			));
		}

		$status = self::isIamFollowing($_POST['authorID']);

		if ( $status ){
			self::deleteFollower($_POST['authorID']);
		}else{
			self::insertFollower($_POST['authorID']);

			do_action('wilcity/wiloke-listing-tools/app/Controllers/FollowController/new-follower', User::getCurrentUserID(), $_POST['authorID']);
		}

		$total = self::countFollowers($_POST['authorID']);
		wp_send_json_success(HTML::reStyleText($total));
	}
}