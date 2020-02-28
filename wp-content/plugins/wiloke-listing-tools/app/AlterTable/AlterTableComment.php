<?php

namespace WilokeListingTools\AlterTable;


class AlterTableComment implements AlterTableInterface {
	public static $tblName = 'wilcity_comments';
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
		$reviewTbl = $wpdb->prefix . self::$tblName;
		$postTbl = $wpdb->posts;

		$charsetCollect = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $reviewTbl(
	  		ID bigint(20) NOT NULL AUTO_INCREMENT,
			objectID bigint(20) NOT NULL,
			content LONGTEXT NOT NULL,
			post_type VARCHAR (50) NULL,
			created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			updated_at TIMESTAMP NULL,
			FOREIGN KEY (objectID) REFERENCES $postTbl(ID) ON DELETE CASCADE,
			PRIMARY KEY (ID)
		) $charsetCollect";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
	}
}