<?php

namespace WilokeListingTools\AlterTable;


class AlterTablePlanRelationships {
	use TableExists;
	public $version = '1.0';
	public static $tblName = 'wicity_submission_plan_relationships';

	public function __construct() {
		add_action('plugins_loaded', array($this, 'createTable'));
	}

	public function createTable() {
		global $wpdb;

		if ( $this->isTableExists() ){
			return false;
		}

		$charsetCollate = $wpdb->get_charset_collate();
		$tblName = $wpdb->prefix . self::$tblName;

		$sql = "CREATE TABLE $tblName(
				ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				planID bigint(20) UNSIGNED NOT NULL,
				objectID bigint(100) UNSIGNED NOT NULL,
				userID bigint(20) UNSIGNED NOT NULL,
				paymentID bigint(20) NULL DEFAULT 0,
				updatedAtGMT TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY(ID)
			) $charsetCollate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}