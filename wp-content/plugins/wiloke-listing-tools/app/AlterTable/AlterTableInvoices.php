<?php

namespace WilokeListingTools\AlterTable;


class AlterTableInvoices {
	public static $tblName = 'wilcity_invoices';
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

		$charsetCollect = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $tblName(
	  		ID bigint(20) NOT NULL AUTO_INCREMENT,
			paymentID bigint(20) NOT NULL,
			currency VARCHAR (100) NOT NULL,
			subTotal DECIMAL(10, 2) NULL,
			discount DECIMAL(10, 2) NULL,
			tax DECIMAL(10, 2) NULL,
			total DECIMAL(10, 2) NULL,
			created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			updated_at TIMESTAMP NULL,
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