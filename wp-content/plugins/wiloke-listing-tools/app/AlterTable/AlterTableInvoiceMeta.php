<?php
namespace WilokeListingTools\AlterTable;


class AlterTableInvoiceMeta implements AlterTableInterface {
    use TableExists;
    public static $tblName = 'wilcity_invoice_meta';
    protected $version = '1.0';

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
        $invoiceTbl = $wpdb->prefix . AlterTableInvoices::$tblName;

        $sql = "CREATE TABLE $tblName(
				ID bigint(20) NOT NULL AUTO_INCREMENT,
				invoiceID bigint(20) NOT NULL,
				meta_key VARCHAR(100) NULL DEFAULT NULL,
				meta_value LONGTEXT NULL DEFAULT NULL,
				PRIMARY KEY(ID),
				FOREIGN KEY (invoiceID) REFERENCES $invoiceTbl (ID) ON DELETE CASCADE
			) $charsetCollate";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        update_option(self::$tblName, $this->version);
    }

    public function deleteTable() {

    }

}
