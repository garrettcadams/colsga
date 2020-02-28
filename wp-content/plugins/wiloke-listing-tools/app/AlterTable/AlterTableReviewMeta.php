<?php

namespace WilokeListingTools\AlterTable;


class AlterTableReviewMeta implements AlterTableInterface {
	public static $tblName = 'wilcity_review_meta';
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
		$reviewMetaTbl = $wpdb->prefix . self::$tblName;
		$reviewTbl = $wpdb->posts;

		$charsetCollate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $reviewMetaTbl(
          ID bigint(20) NOT NULL AUTO_INCREMENT,
          reviewID bigint(20) UNSIGNED NOT NULL,
          meta_key VARCHAR (50) NOT NULL,
          meta_value LONGTEXT NOT NULL,
          FOREIGN KEY (reviewID) REFERENCES $reviewTbl(ID) ON DELETE CASCADE,
          date DATE NOT NULL,
          PRIMARY KEY (ID)
        ) $charsetCollate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
	}
}