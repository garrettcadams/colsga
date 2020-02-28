<?php

namespace WilokeListingTools\Models;


use WilokeListingTools\AlterTable\AlterTableBusinessHourMeta;

class BusinessHourMeta {
	public static function add($postID, $metaKey, $metaVal){
		global $wpdb;
		$table = $wpdb->prefix . AlterTableBusinessHourMeta::$tblName;
		$metaVal = maybe_serialize($metaVal);

		return $wpdb->insert(
			$table,
			array(
				'objectID'      => $postID,
				'meta_key'      => $metaKey,
				'meta_value'    => $metaVal
			),
			array(
				'%d',
				'%s',
				'%s'
			)
		);
	}

	public static function delete($postID, $metaKey){
		global $wpdb;
		$table = $wpdb->prefix . AlterTableBusinessHourMeta::$tblName;

		return $wpdb->delete(
			$table,
			array(
				'objectID'      => $postID,
				'meta_key'      => $metaKey
			),
			array(
				'%d',
				'%s'
			)
		);
	}

	public static function get($postID, $metaKey){
		global $wpdb;
		$table = $wpdb->prefix . AlterTableBusinessHourMeta::$tblName;
		$metaKey = $wpdb->_real_escape($metaKey);

		$val = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT meta_value FROM $table WHERE meta_key=%s AND objectID=%d",
				$metaKey, $postID
			)
		);

		return maybe_unserialize($val);
	}

	public static function getMetaID($postID, $metaKey){
		global $wpdb;
		$table = $wpdb->prefix . AlterTableBusinessHourMeta::$tblName;
		$metaKey = $wpdb->_real_escape($metaKey);

		$val = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $table WHERE meta_key=%s AND objectID=%d",
				$metaKey, $postID
			)
		);

		return $val;
	}

	public static function updateByMetaID($metaID, $metaKey, $metaVal){
		global $wpdb;
		$table = $wpdb->prefix . AlterTableBusinessHourMeta::$tblName;

		return $wpdb->update(
			$table,
			array(
				'meta_value' => $metaVal
			),
			array(
				'ID' => $metaID
			),
			array(
				'%s'
			),
			array(
				'%d'
			)
		);
	}

	public static function update($postID, $metaKey, $metaVal){
		if ( $metaID = self::getMetaID($postID, $metaKey) ){
			return self::updateByMetaID($metaID, $metaKey, $metaVal);
		}

		return self::add($postID, $metaKey, $metaVal);
	}
}