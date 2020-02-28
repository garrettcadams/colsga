<?php
namespace jvbpdelement\Modules\Userforms;

use jvbpdelement\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {
	
	// Modue name : should not be duplicated
	public function get_name() { return 'jvbpd_userforms'; }

	public function get_widgets() {
		return Array(
			'jv_login',
			'jv_signup',
		);
	}
}
