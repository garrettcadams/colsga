<?php

namespace WilokeListingTools\Models;


use phpDocumentor\Reflection\File;
use WilokeListingTools\AlterTable\AlterTableViewStatistic;
use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\Time;

class ViewStatistic {
	protected static $tableName;

	public static function tableName(){
		global $wpdb;
		self::$tableName = $wpdb->prefix . AlterTableViewStatistic::$tblName;
	}

	public static function countAllViews(){
		global $wpdb;
		self::tableName();
		$statisticTbl = self::$tableName;
		$total = $wpdb->get_var("SELECT SUM($statisticTbl.countView) FROM $statisticTbl");
		return abs($total);
	}

	public static function getTotalViewsOfAuthorInDay($userID, $day){
		global $wpdb;
		$postsTbl = $wpdb->posts;
		self::tableName();
		$statisticTbl = self::$tableName;

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $statisticTbl.countView FROM $statisticTbl LEFT JOIN $postsTbl ON ($postsTbl.ID = $statisticTbl.objectID) WHERE $postsTbl.post_status=%s AND $postsTbl.post_author=%d AND $statisticTbl.date=%s",
				'publish', $userID, $day
			)
		);
		return $total ? absint($total) : 0;
	}

	public static function getTotalViewsInRange($userID, $start, $end, $postID=null){
		global $wpdb;
		$postsTbl = $wpdb->posts;
		self::tableName();
		$statisticTbl = self::$tableName;

		$query = "SELECT $statisticTbl.countView FROM $statisticTbl LEFT JOIN $postsTbl ON ($postsTbl.ID = $statisticTbl.objectID) WHERE $postsTbl.post_status=%s AND $postsTbl.post_author=%d AND $statisticTbl.date BETWEEN %s AND %s";

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

	public static function getTotalViewsOfAuthor($userID){
		global $wpdb;
		$postsTbl = $wpdb->posts;
		self::tableName();
		$statisticTbl = self::$tableName;

		$post_types = \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, false);
		$post_types = implode("','", $post_types);

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM($statisticTbl.countView) FROM $statisticTbl LEFT JOIN $postsTbl ON ($postsTbl.ID = $statisticTbl.objectID) WHERE $postsTbl.post_status=%s AND $postsTbl.post_type IN ('".$post_types."') AND $postsTbl.post_author=%d",
				'publish', $userID
			)
		);

		return $total ? absint($total) : 0;
	}

	public static function insert($postID, $totalViews=1){
		global $wpdb;
		self::tableName();

		$status = $wpdb->insert(
			self::$tableName,
			array(
				'objectID'  => $postID,
				'countView' => $totalViews,
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

	public static function countViewsInDay($postID, $day){
		global $wpdb;
		self::tableName();

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT countView FROM ".self::$tableName." WHERE objectID=%d AND date=%s",
				$postID, $day
			)
		);
	}

	public static function countViews($postID){
		global $wpdb;
		self::tableName();

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM(countView) FROM ".self::$tableName." WHERE objectID=%d",
				$postID
			)
		);

		return abs($count);
	}

	public static function update($postID){
		global $wpdb;
		self::tableName();

		$aData = self::isTodayCreated($postID);

		if ( !$aData ){
			self::insert($postID);
		}else{
			$countView = abs($aData['countView']);
			$countView = $countView+1;
			return $wpdb->update(
				self::$tableName,
				array(
					'countView' => $countView
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
		}
	}

	public static function isTodayCreated($postID){
		self::tableName();
		global $wpdb;
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT ID, countView FROM " .self::$tableName . " WHERE objectID=%d AND date=%s",
				$postID, Time::mysqlDate(current_time('timestamp'))
			),
			ARRAY_A
		);
	}
}