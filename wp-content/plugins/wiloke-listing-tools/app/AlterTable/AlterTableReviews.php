<?php

namespace WilokeListingTools\AlterTable;


class AlterTableReviews implements AlterTableInterface {
	public static $tblName = 'wilcity_reviews';
	protected $version = '1.0';
	use TableExists;

	public function __construct() {
		add_action('plugins_loaded', array($this, 'createTable'));
	}

	public function createTable() {
		if ( $this->isTableExists() ){
			return false;
		}

		global $wpdb;
		$realName = $wpdb->prefix . self::$tblName;
		$postTbl = $wpdb->posts;

		$charsetCollate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $realName (
          ID bigint(20) NOT NULL AUTO_INCREMENT,
          objectID bigint(20) UNSIGNED NOT NULL,
          userID bigint(20) NOT NULL,
          title VARCHAR(300) NOT NULL,
          content LONGTEXT NOT NULL,
          parentID bigint(20) NULL,
          date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          FOREIGN KEY (objectID) REFERENCES $postTbl(ID) ON DELETE CASCADE,
          PRIMARY KEY (ID)
        ) $charsetCollate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
	}
}