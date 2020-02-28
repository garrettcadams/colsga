<?php
namespace jvbpdelement\Modules\Meta;

use jvbpdelement\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() { return 'jvbpd_meta'; }

	public function get_widgets() {
		return Array(
			/* 'jvbpd_acf_detail', */
			'Module_Meta',
			'Module_Repeater_Meta',
			'Archive_Meta',
			'Block_Media_Meta',
			'Post_Base_Meta',
			'Listing_Base_Meta',
			'Listing_Base_Field',
			'Block_Card',
			'Module_Card',
			'Ticket_Base_Meta',
		);
	}
}
