<?php
namespace jvbpdelement\Modules\Block;

use jvbpdelement\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() { return 'jvbpd_blocks'; }

	public function get_widgets() {
		return Array(
			'page_block',
			'map_block',
			'map_list_block',
			'Archive_Block',
		);
	}
}
