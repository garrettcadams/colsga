<?php

namespace WilokeListingTools\AlterTable;


class AlterTablePaymentPlanRelationship {
	public static $tblName = 'wicity_payment_plan_relationship';
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
		$paymentHistoryTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$charsetCollect = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $tblName(
			paymentID bigint(20) UNSIGNED,
			planID bigint(20) UNSIGNED,
		 	FOREIGN KEY (paymentID) REFERENCES $paymentHistoryTbl(ID) ON DELETE CASCADE
		) $charsetCollect";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}