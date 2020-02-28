<?php
namespace jvbpdelement\Modules\Button;

use jvbpdelement\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() { return 'jvbpd_buttons'; }

	public function get_widgets() {
		$widgets = Array( 'LoginSignUp');

		if(function_exists('lava_directory')){
			$widgets[] = 'AddFormSubmit';
		}

		if(function_exists('lv_directory_favorite')){
			$widgets[] = 'Favorite';
		}

		return $widgets;
	}
}
