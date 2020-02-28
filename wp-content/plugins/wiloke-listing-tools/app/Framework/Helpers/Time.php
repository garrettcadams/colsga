<?php

namespace WilokeListingTools\Framework\Helpers;


class Time {
	public static $timezoneString;

	public static function convertUTCTimestampToLocalTimestamp($timestamp){
		$isoDate        = date( 'Y-m-d H:i:s', $timestamp );
		return get_date_from_gmt( $isoDate, 'U' );
	}

	/*
	 * Compare 2 Times
	 *
	 * @var $biggerThan (int) Bigger than X day
	 * @since 1.2.0
	 */
	public static function compareTwoTimes($dateA, $dateB='', $biggerThan=1){
		$dateB = empty($dateB) ? current_time('timestamp') : $dateB;
		$timeDiff = $dateA -  $dateB;

		if ( $timeDiff < 0 ){
			return false;
		}

		return $timeDiff >  86400*absint($biggerThan);
	}

	public static function convertJSDateFormatToPHPDateFormat($format){
		$format = str_replace(array('yy', 'mm', 'dd'), array('Y', 'm', 'd'), $format);
		return apply_filters('wilcity/filter/convert-js-date-format-to-php-date-format', $format);
	}

	public static function toTimestamp($format, $date, $timezone = null){
	    if (!empty($timezone)) {
            $timezone = new \DateTimeZone($timezone);
        }
		$oDT = \DateTime::createFromFormat($format, $date, $timezone);
		return $oDT ? $oDT->getTimestamp() : '';
	}

	public static function isDateInThisWeek($day){
		$monday  = date('Y-m-d', strtotime('monday this week'));
		$sunday  = date('Y-m-d', strtotime('sunday this week'));
		$day = date('Y-m-d', strtotime($day));

		return $day >= $monday && $day <= $sunday;
	}

	public static function getPostDate($postDate){
		return date_i18n(get_option('date_format'), strtotime($postDate));
	}

	public static function renderTimeFormat($timestamp, $postID){

		$timeFormat = GetSettings::getPostMeta($postID, 'event_time_format');

		if( empty($timeFormat) || $timeFormat == 'inherit' ) {
			$aThemeOptions = \Wiloke::getThemeOptions(true);
			$timeFormat = $aThemeOptions['timeformat'];
		}

		if ( empty($timeFormat) ){
			return date_i18n(get_option('time_format'), $timestamp);
		}

		if ( $timeFormat == 12 ){
			return date_i18n('h:i A', $timestamp);
		}

		return date_i18n('H:i', $timestamp);
	}

	public static function getAllDaysInThis($timeFormat='Y-m-d'){
		return array(
			'monday' => date($timeFormat, strtotime('monday this week')),
			'tuesday' => date($timeFormat, strtotime('tuesday this week')),
			'wednesday' => date($timeFormat, strtotime('wednesday this week')),
			'thursday' => date($timeFormat, strtotime('thursday this week')),
			'friday' => date($timeFormat, strtotime('friday this week')),
			'saturday' => date($timeFormat, strtotime('saturday this week')),
			'sunday' => date($timeFormat, strtotime('sunday this week'))
		);
	}

	public static function getDayKeyOfWeek($today){
		$aDayOfWeek = wilokeListingToolsRepository()->get('general:aDayOfWeek');
		$aDayOfWeekKey = array_keys($aDayOfWeek);
		return $aDayOfWeekKey[$today-1];
	}

	public static function dateDiff($timestamp1, $timestamp2, $diffIn='hour'){
		$diff = $timestamp2 - $timestamp1;
		switch ($diffIn){
			case 'day':
				return floor($diff / (60*60*24) );
				break;
			case 'minute':
				return floor($diff / 60 );
				break;
			case 'hour':
				return floor($diff / (60*60) );
				break;
			default:
				return $diff;
				break;
		}
	}

	public static function timeFromNow($timestamp, $isUTC=false){
		$now = $isUTC ? current_time('timestamp', true) : current_time('timestamp');
		$minutes = self::dateDiff($timestamp, $now, 'minute');

		if ( $minutes < 60 ){
			return sprintf(_n('%s minute ago', '%s minutes ago', $minutes, 'wiloke-listing-tools'), $minutes);
		}

		$hours = self::dateDiff($timestamp, $now, 'minute');
		if ( $hours < 24 ){
			return sprintf(_n('%s hour ago', '%s hours ago', $hours, 'wiloke-listing-tools'), $hours);
		}

		return date(get_option('date_format'), $timestamp);
	}

	public static function getTimeFormat($format=''){
		switch ($format){
			case '24':
				$format = 'H:i';
				break;
			case '12':
				$format = 'h:i A';
				break;
			default:
				$aThemeOptions = class_exists('Wiloke') ? \Wiloke::getThemeOptions() : array();
				$format = isset($aThemeOptions['timeformat']) ? $aThemeOptions['timeformat'] : 12;
				if ( $format == 12 ){
					$format = 'h:i A';
				}else{
					$format = 'H:i';
				}
				break;
		}

		return $format;
	}

	public static function toMysqlDateFormat($time='', $isUTC=false){
		if ( empty($time) ){
			$time = current_time('timestamp', $isUTC);
		}
		return date('Y-m-d', $time);
	}

	public static function toTwelveFormat($timestamp){
		return date('h:i A', $timestamp);
	}

	public static function renderTime($hour, $format){
		$format = self::getTimeFormat($format);

		if ( is_numeric($format) ){
			if ( $format == 24 ){
				return date_i18n('H:i', strtotime($hour));
			}

			return self::toTwelveFormat(strtotime($hour));
		}

		return date_i18n($format, strtotime($hour));
	}

	public static function toDateFormat($date, $format=''){
		$dateFormat = empty($format) ? get_option('date_format') : $format;
		return date_i18n($dateFormat, strtotime($date));
	}

	public static function toTimeFormat($time, $format=''){
		$format = self::getTimeFormat($format);
		return date_i18n($format, strtotime($time));
	}

	public static function findUTCOffsetByTimezoneID($timezone){
		if ( empty($timezone) ){
			return 'UTC';
		}

		$dtz = new \DateTimeZone($timezone);
		$timeZoneIn = new \DateTime('now', $dtz);

		$offset = $dtz->getOffset( $timeZoneIn );
		$utcOffset = $offset/3600;

		if ( $utcOffset > 0 ){
			return 'UTC+'.$utcOffset;
		}
		return 'UTC-'.$utcOffset;
	}

	public static function mysqlDateTime($timestamp=''){
		$timestamp = empty($timestamp) ? current_time('timestamp') : $timestamp;
		return date('Y-m-d H:i:s', $timestamp);
	}

	public static function timeStampNow(){
		return current_time('timestamp');
	}

	/*
	 * Return object $oNow
	 */
	public static function getAtomString(){
		return date(DATE_ATOM, current_time('timestamp'));
	}

	public static function getAtomUTCString(){
		return date(DATE_ATOM, current_time('timestamp', 1));
	}

	/*
	 * String To UTC Time
	 *
	 * @param number $timestamp
	 * @return string $date
	 */
	public static function toAtomUTC($timeStamp){
		$timeStamp = preg_match('/[^0-9]/', $timeStamp) ? strtotime($timeStamp) : $timeStamp;
		date_default_timezone_set("UTC");
		return date(DATE_ATOM, $timeStamp);
	}

	public static function toAtom($timeStamp){
		return date(DATE_ATOM, $timeStamp);
	}

	public static function iso8601StartDate(){
		$startDate = date('c', current_time('timestamp'));
		$startDate = date('c', strtotime($startDate  . ' +1 day'));
		$startDate = str_replace('+00:00', 'Z', $startDate);
		return $startDate;
	}

	public static function utcToLocal($utc, $timezone){
		date_default_timezone_set($timezone);
		return strtotime($utc);
	}

	/**
	 * Get timestamp UTC now
	 */
	public static function timestampUTCNow($plus=''){
		date_default_timezone_set('UTC');
		return empty($plus) ? time() : strtotime($plus);
	}

	/**
	 * Get timestamp UTC now
	 */
	public static function timestampUTC($timestamp, $plus=null){
		return strtotime(self::toAtomUTC($timestamp) . ' ' . $plus);
	}

	public static function convertDayToSeconds($day){
		return $day*24*60*60;
	}

	public static function convertSecondsToDay($seconds, $type='floor'){
		if ( $type == 'floor' ){
			return floor($seconds/(24*60*60));
		}else{
			return ceil($seconds/(24*60*60));
		}
	}

	public static function convertToTimezoneUTC($dateTime, $fromTimezone, $format='Y-m-d'){
		if ( empty($fromTimezone) || strpos($fromTimezone, 'UTC') !== false){
			$msg = sprintf(__('Please set Timezone to this post or go to <a href="%s">General</a> -> Set General Timezone for your site. Note that you have to use GMT format instead of UTC format', 'wiloke-listing-tools'), admin_url('options-general.php'));
			if ( wp_doing_ajax() ){
				wp_send_json_error(
					array(
						'msg' => $msg
					)
				);
			}
			wp_die($msg);
		}

		$dt = new \DateTime($dateTime, new \DateTimeZone($fromTimezone));

		$dt->setTimeZone(new \DateTimeZone('UTC'));
		return $dt->format($format);
	}

	public static function convertToTwentyFourFormat($hour){
		return date('H:i', strtotime($hour));
	}

	public static function setTimeExpiration($utc=''){
		if ( !empty($utc) ){
			$prevTimezone = date_default_timezone_get();
			date_default_timezone_set($utc);
		}
		$aDate['date']  = date('m/d/Y', time());
		$aDate['time'] = date('h:i', time());

		if ( !empty($utc) ){
			date_default_timezone_set($prevTimezone);
		}

		return $aDate;
	}

	public static function dayOfWeekKeys(){
		$aDayOfWeek = wilokeListingToolsRepository()->get('general:aDayOfWeek');
		return array_keys($aDayOfWeek);
	}

	public static function getDayKey($index){
		$aKeys = self::dayOfWeekKeys();
		return $aKeys[$index];
	}

	public static function mysqlDate($timestamp=null){
		$timestamp = empty($timestamp) ? current_time('timestamp') : $timestamp;
		return date('Y-m-d', $timestamp);
	}

	public static function getDefaultTimezoneString(){
		$timezoneString = GetSettings::getOptions('timezone_string');
		if ( empty($timezoneString) ){
			return false;
		}

		return $timezoneString;
	}
}
