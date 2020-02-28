<?php

namespace WilokeListingTools\AlterTable;


class AlterTableNotifications {
	public static $tblName = 'wilcity_notifications';
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
		$usersTbl = $wpdb->users;

		$charsetCollect = $wpdb->get_charset_collate();


		$sql = "CREATE TABLE $tblName(
	  		ID bigint(200) NOT NULL AUTO_INCREMENT,
			receiverID bigint(200) UNSIGNED NOT NULL,
			senderID bigint(200) NULL,
			type VARCHAR (50) NOT NULL,
			objectID bigint(200) NULL,
		 	date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          	FOREIGN KEY (receiverID) REFERENCES $usersTbl(ID) ON DELETE CASCADE,
			PRIMARY KEY (ID)
		) $charsetCollect";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}