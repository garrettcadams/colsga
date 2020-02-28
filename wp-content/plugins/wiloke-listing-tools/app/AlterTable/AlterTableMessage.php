<?php
/*
 |--------------------------------------------------------------------------
 | Favorite Table
 |--------------------------------------------------------------------------
 | This table container list of favorite of user
 |
 */

namespace WilokeListingTools\AlterTable;

class AlterTableMessage implements AlterTableInterface{
	public static $tblName = 'wilcity_message';
	public $version = '1.0';
	use TableExists;

	public function __construct() {
		add_action('plugins_loaded', array($this, 'createTable'));
//		add_action('plugins_loaded', array($this, 'addUTCColumns'));
	}

	public function addUTCColumns(){
		global $wpdb;
		$tblName = $wpdb->prefix . self::$tblName;
		if (!$wpdb->query("SHOW TABLES LIKE '".$tblName."'")){
			return false;
		}
		if ( !$wpdb->query("SHOW COLUMNS FROM `$tblName` LIKE 'messageDateUTC'") ){
			$wpdb->query(
				"ALTER TABLE $tblName ADD messageDateUTC DATETIME DEFAULT NULL AFTER messageDate"
			);
		}
	}

	public function createTable() {
		if ( $this->isTableExists() ){
			return false;
		}

		global $wpdb;
		$tblName = $wpdb->prefix . self::$tblName;
		$userTbl = $wpdb->users;

		$charsetCollate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $tblName (
          ID bigint(20) NOT NULL AUTO_INCREMENT,
          messageUserReceivedID bigint(20) UNSIGNED NOT NULL,
          messageAuthorID bigint( 20 ) UNSIGNED NOT NULL,
          messageContent Text NOT NULL, 
          messageDate DATETIME DEFAULT NULL,
          messageDateUTC DATETIME DEFAULT NULL,
          messageReceivedSeen VARCHAR (5) DEFAULT 'no',
          PRIMARY  KEY (ID),
          FOREIGN KEY (messageAuthorID) REFERENCES $userTbl(ID) ON DELETE CASCADE
        ) $charsetCollate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}
