<?php
namespace WilokeListingTools\Models;


use WilokeListingTools\AlterTable\AlterTableEventsData;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Time;

class EventModel {
	public static $aCache = array();

	public static function countUnpaidEvents($authorID){
		global $wpdb;
		$eventTbl = $wpdb->prefix . AlterTableEventsData::$tblName;
		$postTbl = $wpdb->posts;

		$sql = "SELECT COUNT($postTbl.ID) FROM $postTbl LEFT JOIN $eventTbl ON ($eventTbl.objectID = $postTbl.ID) WHERE $postTbl.post_status='unpaid' AND $postTbl.post_author=%d AND $postTbl.post_type='event'";

		$count = $wpdb->get_var($wpdb->prepare(
			$sql,
			$authorID
		));

		return empty($count) ? 0 : abs($count);
	}

	public static function countUpcomingEventsOfAuthor($authorID){
		global $wpdb;
		$eventTbl = $wpdb->prefix . AlterTableEventsData::$tblName;
		$postTbl = $wpdb->posts;
		$now = Time::mysqlDateTime(current_time('timestamp', true));

		$sql = "SELECT COUNT($postTbl.ID) FROM $postTbl LEFT JOIN $eventTbl ON ($eventTbl.objectID = $postTbl.ID) WHERE $postTbl.post_status='publish' AND $postTbl.post_author=%d AND $postTbl.post_type='event' AND $eventTbl.startsOnUTC > '".$now."'";

		$count = $wpdb->get_var($wpdb->prepare(
			$sql,
			$authorID
		));

		return empty($count) ? 0 : abs($count);
	}

	public static function countOnGoingEventsOfAuthor($authorID){
		global $wpdb;
		$eventTbl = $wpdb->prefix . AlterTableEventsData::$tblName;
		$postTbl = $wpdb->posts;
		$now = Time::mysqlDateTime(current_time('timestamp', true));

		$sql = "SELECT COUNT($postTbl.ID) FROM $postTbl LEFT JOIN $eventTbl ON ($eventTbl.objectID = $postTbl.ID) WHERE $postTbl.post_status='publish' AND $postTbl.post_author=%d AND $postTbl.post_type='event' AND $eventTbl.startsOnUTC <= '".$now."' AND $eventTbl.endsOnUTC >= '".$now."' AND $postTbl.post_status ='publish'";

		$count = $wpdb->get_var($wpdb->prepare(
			$sql,
			$authorID
		));

		return empty($count) ? 0 : abs($count);
	}

	public static function countExpiredEventsOfAuthor($authorID){
		global $wpdb;
		$eventTbl = $wpdb->prefix . AlterTableEventsData::$tblName;
		$postTbl = $wpdb->posts;
		$now = Time::mysqlDateTime(current_time('timestamp', true));

        $sql = "SELECT COUNT($postTbl.ID) FROM $postTbl LEFT JOIN $eventTbl ON ($eventTbl.objectID = $postTbl.ID) WHERE $postTbl.post_author=%d AND $postTbl.post_type='event' AND ($eventTbl.endsOnUTC <= '".$now."' OR $eventTbl.endsOnUTC <= $eventTbl.startsOnUTC || $eventTbl.startsOnUTC IS NULL) AND ($postTbl.post_status ='publish' || $postTbl.post_status ='expired')";

		$count = $wpdb->get_var($wpdb->prepare(
			$sql,
			$authorID
		));
		return empty($count) ? 0 : abs($count);
	}

	public static function convertTimeToUTC($time, $timezone){
		date_default_timezone_set($timezone);
		$date = date('Y-m-d H:i:s', $time);
		date_default_timezone_set('UTC');
		$dateGMT = date('Y-m-d H:i:s', strtotime($date));
		return $dateGMT;
	}

	/*
	 * $objectID int required
	 * $aData array: type, startsGMT, endsOn, openingAt, closedAt, timezone, lat, lng, googleAddress
	 * $parentID int It's listing ID
	 */
	public static function setEventData($aRawData){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTableEventsData::$tblName;

		$aKeys = array('objectID', 'parentID', 'frequency', 'specifyDays', 'startsOn', 'endsOn', 'startsOnUTC', 'endsOnUTC', 'timezone');
		$aPrepares = array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

		$aData = array();
		foreach ($aKeys as $key){
			$aData[$key] = isset($aRawData['values'][$key]) ? sanitize_text_field($aRawData['values'][$key]) : '';
		}

		$status = $wpdb->insert(
			$tblName,
			$aData,
			$aPrepares
		);

		if ( !$status ){
			return false;
		}

		return $wpdb->insert_id;
	}

	public static function isEventDataExists($objectID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTableEventsData::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tblName WHERE objectID=%d",
				$objectID
			)
		);
	}

	public static function rightTimestampFormat($time, $timezone=''){
		if ( !empty($timezone) ){
			date_default_timezone_set($timezone);
		}
		$time = strtotime($time);
		return date('Y-m-d', $time);
	}

	public static function updateEventData($objectID, $aUpdates){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTableEventsData::$tblName;

		if (  isset($aUpdates['values']['specifyDays']) ){
			$aDays = $aUpdates['values']['specifyDays'];
			unset($aUpdates['values']['specifyDays']);
			$aUpdates['values']['specifyDays'] = is_array($aDays) ? implode(',', $aDays) : $aDays;
		}else{
			$aUpdates['values']['specifyDays']   = 'always';
			$aUpdates['prepares'][] = '%s';
		}

		foreach ($aUpdates['values'] as $key => $val){
			$aUpdates['values'][$key] = sanitize_text_field($val);
		}

		$timeFormat = Time::getTimeFormat();
		$dateFormat = apply_filters('wilcity_date_picker_format', 'mm/dd/yy');
		$dateFormat = Time::convertJSDateFormatToPHPDateFormat($dateFormat);

		if ( !empty($aUpdates['values']['openingAt']) ){
			$dateFormat = $dateFormat . ' ' . Time::getTimeFormat();
            $openingAt = strtotime($aUpdates['values']['openingAt']);
			$startsOn = $aUpdates['values']['starts'] . ' ' . date($timeFormat, $openingAt);
			$startsOn = Time::toTimestamp($dateFormat, $startsOn);
		}else{
			$startsOn = Time::toTimestamp($dateFormat, $aUpdates['values']['starts']);
		}

		if ( !isset($aUpdates['values']['timezone']) || empty($aUpdates['values']['timezone']) ){
			$aUpdates['values']['timezone'] = get_option('timezone_string');
		}

		if ( !empty($aUpdates['values']['closedAt']) ){
            $closedAt = strtotime($aUpdates['values']['closedAt']);
			$endsOn = $aUpdates['values']['endsOn'] . ' ' . date($timeFormat, $closedAt);
			$endsOn = Time::toTimestamp($dateFormat, $endsOn);
		}else{
			$endsOn = Time::toTimestamp($dateFormat, $aUpdates['values']['endsOn']);
		}

		$startsOn = Time::mysqlDateTime($startsOn);
		$endsOn = Time::mysqlDateTime($endsOn);

		$startsOnUTC = Time::convertToTimezoneUTC($startsOn, $aUpdates['values']['timezone'], 'Y-m-d H:i:s');

		$endsOnUTC = Time::convertToTimezoneUTC($endsOn, $aUpdates['values']['timezone'], 'Y-m-d H:i:s');

		$aUpdates['values']['startsOn'] = $startsOn;
		$aUpdates['values']['endsOn'] = $endsOn;
		$aUpdates['values']['startsOnUTC'] = $startsOnUTC;
		$aUpdates['values']['endsOnUTC'] = $endsOnUTC;
		$aUpdates['prepares'][] = '%s';
		$aUpdates['prepares'][] = '%s';
		$aUpdates['prepares'][] = '%s';
		$aUpdates['prepares'][] = '%s';
		if ( $ID = self::isEventDataExists($objectID) ){
			$wpdb->update(
				$tblName,
				array(
					'frequency'     => $aUpdates['values']['frequency'],
					'specifyDays'   => $aUpdates['values']['specifyDays'],
					'startsOn'      => $aUpdates['values']['startsOn'],
					'endsOn'        => $aUpdates['values']['endsOn'],
					'startsOnUTC'   => $aUpdates['values']['startsOnUTC'],
					'endsOnUTC'     => $aUpdates['values']['endsOnUTC'],
					'timezone'      => $aUpdates['values']['timezone']
				),
				array(
					'ID' => abs($ID)
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s'
				),
				array(
					'%d'
				)
			);
			$status = true;
			do_action('wilcity/wiloke-listing-tools/updated-event-data', $objectID, $aUpdates, $ID);
		}else{
			$aUpdates['objectID'] = $ID;
			if ( !isset($aUpdates['parentID']) ){
				$aUpdates['parentID'] = '';
			}
			$status = self::setEventData($aUpdates);
			if ( empty($status) ){
				return false;
			}

			$eventID = $wpdb->insert_id;
			do_action('wilcity/wiloke-listing-tools/inserted-event-data', $objectID, $aUpdates, $eventID);
			return $eventID;
		}
		return $status;
	}

	public static function getField($fieldName, $eventID){
		if ( isset(self::$aCache[$fieldName.$eventID]) ){
			return self::$aCache[$fieldName.$eventID];
		}

		global $wpdb;
		$tblName = $wpdb->prefix . AlterTableEventsData::$tblName;

		self::$aCache[$fieldName.$eventID] = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $fieldName FROM $tblName WHERE objectID=%d",
				$eventID
			)
		);
		return self::$aCache[$fieldName.$eventID];
	}

	public static function getEventData($postID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTableEventsData::$tblName;
		$aData = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $tblName WHERE objectID=%d",
				$postID
			),
			ARRAY_A
		);

		$aData = empty($aData) ? array() : $aData;

		$video = GetSettings::getPostMeta($postID, 'video');
		if ( empty($video) && empty($aData) ){
			return false;
		}else{
			$aData = empty($aData) ? array() : $aData;
			$aData['video'] = $video;
		}

		return $aData;
	}
}
