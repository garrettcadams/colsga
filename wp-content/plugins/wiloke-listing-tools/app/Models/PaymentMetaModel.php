<?php
namespace WilokeListingTools\Models;


use WilokeListingTools\AlterTable\AlterTablePaymentMeta;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;

class PaymentMetaModel {
	public static $tblName;

	public static function addPrefixToMetaName($metaKey){
		return $metaKey = wilokeListingToolsRepository()->get('general:metaboxPrefix') . $metaKey;
	}

	/**
	 * @return void
	 */
	public static function generateTableName($wpdb){
		self::$tblName = $wpdb->prefix . AlterTablePaymentMeta::$tblName;
	}

	/**
	 * Set Payment Meta
	 *
	 * @param number $sessionID
	 * @param string $metaKey
	 * @param mixed $val
	 *
	 * @return bool
	 */
	public static function set($paymentID, $metaKey, $val){
		global $wpdb;
		self::generateTableName($wpdb);
		if ( empty(self::get($paymentID, $metaKey)) ){
			return $wpdb->insert(
				self::$tblName,
				array(
					'paymentID' => $paymentID,
					'meta_key'  => self::addPrefixToMetaName($metaKey),
					'meta_value'=> maybe_serialize($val)
				),
				array(
					'%d',
					'%s',
					'%s'
				)
			);
		}else{
			return self::patch($paymentID, self::addPrefixToMetaName($metaKey), $val);
		}
	}

	public static function patch($paymentID, $metaKey, $val){
		global $wpdb;
		self::generateTableName($wpdb);

		return $wpdb->update(
			self::$tblName,
			array(
				'meta_value' => maybe_serialize($val)
			),
			array(
				'paymentID' => $paymentID,
				'meta_key'  => $metaKey,
			),
			array(
				'%s'
			),
			array(
				'%d',
				'%s'
			)
		);
	}

	public static function update($sessionID, $metaKey, $val){
		return self::patch($sessionID, self::addPrefixToMetaName($metaKey), $val);
	}

	/**
	 * Get Payment Meta by specifying meta key and sessionID
	 *
	 * @param number $sessionID
	 * @param string $metaKey
	 *
	 * @return mixed
	 */
	public static function get($sessionID, $metaKey){
		global $wpdb;
		self::generateTableName($wpdb);
		$aResult = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT meta_value FROM ".self::$tblName. " WHERE paymentID=%d AND meta_key=%s ORDER BY ID DESC",
				$sessionID, self::addPrefixToMetaName($metaKey)
			)
		);

		if ( empty($aResult) ){
			return false;
		}

		return maybe_unserialize($aResult);
	}

	/*
	 * @param $paymentID
	 */
	public static function getNextBillingDateGMT($paymentID){
		return self::get($paymentID, wilokeListingToolsRepository()->get('addlisting:nextBillingDateGMT'));
	}

	/*
	 * @param $nextBillingDateGMT
	 * @param $paymentID
	 */
	public static function setNextBillingDateGMT($nextBillingDateGMT, $paymentID){
		self::set($paymentID, wilokeListingToolsRepository()->get('addlisting:nextBillingDateGMT'), $nextBillingDateGMT);
	}

	public static function delete($sessionID, $metaKey){
		global $wpdb;
		self::generateTableName($wpdb);

		$status = $wpdb->delete(
			self::$tblName,
			array(
				'meta_key'  => $metaKey,
				'paymentID' => $sessionID
			),
			array(
				'%s',
				'%d'
			)
		);

		if ( empty($status) ){
			return false;
		}

		return $sessionID;
	}

	public static function getPostTypeByPlanID($planID){
		$aPostTypes = GetSettings::getFrontendPostTypes(true);
		foreach ($aPostTypes as $postType){
			$planIDs = GetWilokeSubmission::getField($postType.'_plans');
			if ( empty($planIDs) ){
				continue;
			}

			$aPlanIDs = explode(',', $planIDs);
			if ( in_array($planID, $aPlanIDs) ){
				return $postType;
			}
		}
	}

	/**
	 * Get Session ID by specifying token
	 *
	 * @param string $token
	 * @param string $metaKey
	 *
	 * @return mixed
	 */
	public static function getPaymentIDByMetaValue($metaValue, $metaKey){
		global $wpdb;
		self::generateTableName($wpdb);

		$sessionID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT paymentID FROM ".self::$tblName. " WHERE meta_value=%s AND meta_key=%s ORDER BY ID DESC",
				$metaValue, self::addPrefixToMetaName($metaKey)
			)
		);

		if ( empty($sessionID) ){
			return false;
		}

		return $sessionID;
	}

	/**
	 * Get Session ID By Meta Value
	 *
	 * @param string $metaValue
	 * @return number $sessionID
	 */
	public static function getSessionWhereEqualToMetaValue($metaKey, $metaVal){
		global $wpdb;
		self::generateTableName($wpdb);

		$sessionID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT paymentID FROM ".self::$tblName. " WHERE meta_key=%s AND meta_value=%s",
				self::addPrefixToMetaName($metaKey), $metaVal
			)
		);

		return abs($sessionID);
	}
}