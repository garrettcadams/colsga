<?php
namespace jvbpdelement\Modules\Review;

use jvbpdelement\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() { return 'jvbpd_review'; }

	public function get_widgets() {
		$widgets = Array();
		if(function_exists('lv_directoryReview')){
			$widgets[] = 'Single_Review';
		}
		return $widgets;
	}
}
