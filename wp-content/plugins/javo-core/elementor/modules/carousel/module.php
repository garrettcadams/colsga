<?php
namespace jvbpdelement\Modules\Carousel;

use jvbpdelement\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'JV_Media_Carousel',
			'Jv_Carousel_single_listing',
		];
	}

	public function get_name() {
		return 'jv-carousel';
	}
}
