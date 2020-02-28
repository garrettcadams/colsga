<?php
namespace jvbpdelement\Modules\Slider;

use jvbpdelement\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	// Modue name : should not be duplicated
	public function get_name() { return 'jvbpd-slider'; }

	public function get_widgets() {
		return Array(
			'JV_Page_Slider',
		);
	}
}
