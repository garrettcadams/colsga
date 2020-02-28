<?php

namespace WilokeListingTools\Models;


use WilokeListingTools\AlterTable\AlterTableFavoritesStatistic;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Helpers\General;

class FavoriteStatistic {
	protected static $tableName;

	public static function tableName(){
		global $wpdb;
		self::$tableName = $wpdb->prefix . AlterTableFavoritesStatistic::$tblName;
	}
	
	public static function countMyFavorites($userID=''){
		
		$userID = empty($userID) ? get_current_user_id() : $userID;

		$aFavorites = GetSettings::getUserMeta($userID, 'my_favorites');

		if( empty($aFavorites) ) {
			return 0;
		}

		$post_types = General::getPostTypeKeys(false, false);

		$query = new \WP_Query(array(
			'post_type'		=> $post_types,
			'post_status'	=> 'publish',
			'post__in'		=> $aFavorites
		));

		return absint($query->found_posts);
	}
	
	public static function countFavorites($postID){
		global $wpdb;
		self::tableName();

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT sum(countLoved) FROM ".self::$tableName." WHERE objectID=%d",
				$postID
			)
		);
		return abs($count);
	}

	public static function getTotalFavoritesOfAuthorInDay($userID, $day){
		global $wpdb;
		$postsTbl = $wpdb->posts;
		self::tableName();
		$statisticTbl = self::$tableName;

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $statisticTbl.countLoved FROM $statisticTbl LEFT JOIN $postsTbl ON ($postsTbl.ID = $statisticTbl.objectID) WHERE $postsTbl.post_status=%s AND $postsTbl.post_author=%d AND $statisticTbl.date=%s",
				'publish', $userID, $day
			)
		);
		return $total ? absint($total) : 0;
	}

	public static function getTotalFavoritesInRange($userID, $start, $end, $postID=null){
		global $wpdb;
		$postsTbl = $wpdb->posts;
		self::tableName();
		$statisticTbl = self::$tableName;

		$query = "SELECT $statisticTbl.countLoved FROM $statisticTbl LEFT JOIN $postsTbl ON ($postsTbl.ID = $statisticTbl.objectID) WHERE $postsTbl.post_status=%s AND $postsTbl.post_author=%d AND $statisticTbl.date BETWEEN %s AND %s";

		if ( !empty($postID) ){
			$query .= " AND $statisticTbl.objectID=%d";
			$total = $wpdb->get_var(
				$wpdb->prepare(
					$query,
					'publish', $userID, $start, $end, $postID
				)
			);
		}else{
			$total = $wpdb->get_var(
				$wpdb->prepare(
					$query,
					'publish', $userID, $start, $end
				)
			);
		}

		return $total ? absint($total) : 0;
	}

	public static function getTotalFavoritesOfAuthor($userID){
		global $wpdb;
		$postsTbl = $wpdb->posts;
		self::tableName();
		$statisticTbl = self::$tableName;

		$post_types = \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, false);
		$post_types = implode("','", $post_types);

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT sum($statisticTbl.countLoved) FROM $statisticTbl LEFT JOIN $postsTbl ON ($postsTbl.ID = $statisticTbl.objectID) WHERE $postsTbl.post_status=%s AND $postsTbl.post_type IN ('%s') AND $postsTbl.post_author=%d",
				'publish', $post_types, $userID
			)
		);
		return $total ? absint($total) : 0;
	}

	public static function insert($postID){
		global $wpdb;
		self::tableName();

		$status = $wpdb->insert(
			self::$tableName,
			array(
				'objectID'  => $postID,
				'countLoved'=> 1,
				'date'      => current_time('mysql')
			),
			array(
				'%d',
				'%d',
				'%s'
			)
		);

		return $status ? $wpdb->insert_id : false;
	}

	public static function update($postID, $plus=true){
		global $wpdb;
		self::tableName();

		$aData = self::isTodayCreated($postID);

		if ( !$aData ){
			$insertID = self::insert($postID);
			if ( $insertID ){
				do_action('wiloke-listing-tools/notification', get_post_field('post_author', $postID), $insertID, 'like', 'add', array('postID'=>$postID));
			}
			return $insertID;
		}else{
			$countLoved = abs($aData['countLoved']);
			$countLoved = $plus ? $countLoved + 1 : $countLoved - 1;
			$countLoved = abs($countLoved);
			$status = $wpdb->update(
				self::$tableName,
				array(
					'countLoved' => $countLoved
				),
				array(
					'ID' => $aData['ID']
				),
				array(
					'%d'
				),
				array(
					'%d'
				)
			);

			if ( $status && !$plus ){
				do_action('wiloke-listing-tools/notification', get_post_field('post_author', $postID), $aData['ID'], 'remove', 'add', array());
			}

			return $status;
		}
	}

	public static function isTodayCreated($postID){
		self::tableName();
		global $wpdb;
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT ID, countLoved FROM " .self::$tableName . " WHERE objectID=%d AND date=%s",
				$postID, Time::mysqlDate(current_time('timestamp'))
			),
			ARRAY_A
		);
	}
}