<?php

namespace WilokeListingTools\AlterTable;


trait TableExists {
	private static $isCheckEngine = false;
	public function isTableExists(){
		global $wpdb;
		$tblName = $wpdb->prefix . self::$tblName;
		$result = $wpdb->query("SHOW TABLES LIKE '".$tblName."'");

		if (!$result){
			if ( !self::$isCheckEngine ){
				if ( !self::isInnoDB() ){
					self::convertDefaultTblsToInno();
				}
				self::$isCheckEngine = true;
			}
			return false;
		}

		if ( get_option(self::$tblName) && (version_compare(get_option(self::$tblName), $this->version, '>=')) ){
			return true;
		}

		update_option(self::$tblName, $this->version);
		return true;
	}

	private static function isInnoDB(){
		global $wpdb;
		return $wpdb->get_var("SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE engine = 'innodb'");
	}

	private static function convertDefaultTblsToInno(){
		global $wpdb;
		$wpdb->query("ALTER TABLE {$wpdb->posts} ENGINE=InnoDB;");
		$wpdb->query("ALTER TABLE {$wpdb->comments} ENGINE=InnoDB;");
		$wpdb->query("ALTER TABLE {$wpdb->users} ENGINE=InnoDB;");
	}
}