<?php

namespace WilokeListingTools\AlterTable;


class AlterTableFollower {
	public static $tblName = 'wilcity_follower';
	public $version = '1.0';
	use TableExists;

	public function __construct() {
		add_action('plugins_loaded', array($this, 'createTable'));
	}

	public function createTable() {
		if ( $this->isTableExists() ){
			return false;
		}

		global $wpdb;
		$tblName = $wpdb->prefix . self::$tblName;
		$userTbl = $wpdb->users;

		$charsetCollect = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $tblName(
	  		followerID bigint(20) UNSIGNED NOT NULL,
			authorID bigint(20) UNSIGNED NOT NULL,
			date DATETIME NULL,
			FOREIGN KEY $tblName (authorID) REFERENCES $userTbl(ID) ON DELETE CASCADE
		) $charsetCollect";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}